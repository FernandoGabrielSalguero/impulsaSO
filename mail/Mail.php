<?php

declare(strict_types=1);

namespace SVE\Mail;

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/lib/PHPMailer.php';
require_once __DIR__ . '/lib/SMTP.php';
require_once __DIR__ . '/lib/Exception.php';

final class Mailer
{
    // -------------------------------------------------------------------------
    // Infraestructura privada
    // -------------------------------------------------------------------------

    private static function logEmail(array $data): void
    {
        try {
            global $pdo;
            if (!($pdo instanceof \PDO)) {
                return;
            }

            $meta = isset($data['meta']) ? json_encode($data['meta']) : null;

            $stmt = $pdo->prepare(
                "INSERT INTO correos_log
                    (user_auth_id, correo, asunto, template, mensaje_html, mensaje_text, estado, error, meta)
                 VALUES
                    (:user_auth_id, :correo, :asunto, :template, :mensaje_html, :mensaje_text, :estado, :error, :meta)"
            );

            $stmt->execute([
                'user_auth_id' => $data['user_auth_id'] ?? null,
                'correo'       => $data['correo']       ?? '',
                'asunto'       => $data['asunto']       ?? '',
                'template'     => $data['template']     ?? null,
                'mensaje_html' => $data['mensaje_html'] ?? null,
                'mensaje_text' => $data['mensaje_text'] ?? null,
                'estado'       => $data['estado']       ?? 'fallido',
                'error'        => $data['error']        ?? null,
                'meta'         => $meta,
            ]);
        } catch (\Throwable) {
            // No interrumpir el flujo principal si falla el log.
        }
    }

    private static function baseMailer(?array &$debugLog = null): PHPMailer
    {
        $host   = getenv('SMTP_HOST')     ?: '';
        $user   = getenv('SMTP_USERNAME') ?: '';
        $pass   = getenv('SMTP_PASSWORD') ?: '';
        $port   = (int)(getenv('SMTP_PORT')   ?: 0);
        $secure = getenv('SMTP_SECURE')   ?: '';

        if ($host === '' || $user === '' || $pass === '') {
            throw new \RuntimeException('Configuración SMTP incompleta.');
        }

        if ($secure === '') {
            $secure = $port === 465 ? 'ssl' : 'tls';
        }
        if ($port <= 0) {
            $port = $secure === 'ssl' ? 465 : 587;
        }

        $from     = getenv('MAIL_FROM')      ?: $user;
        $fromName = getenv('MAIL_FROM_NAME') ?: 'Impulsa';

        $m = new PHPMailer(true);
        $m->isSMTP();
        $m->Host      = $host;
        $m->SMTPAuth  = true;
        $m->Username  = $user;
        $m->Password  = $pass;

        if ($secure === 'ssl') {
            $m->SMTPSecure  = PHPMailer::ENCRYPTION_SMTPS;
            $m->SMTPAutoTLS = false;
        } else {
            $m->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $m->Port      = $port;
        $m->CharSet   = 'UTF-8';
        $m->Encoding  = 'base64';
        $m->setFrom($from, $fromName);
        $m->Sender    = $from;
        $m->addReplyTo($from, $fromName);
        $m->isHTML(true);

        if ($debugLog !== null) {
            $m->SMTPDebug   = 2;
            $m->Debugoutput = function (string $str) use (&$debugLog): void {
                $line = trim($str);
                if (stripos($line, 'CLIENT -> SERVER: AUTH') === 0) {
                    $debugLog[] = 'CLIENT -> SERVER: AUTH [redacted]';
                    return;
                }
                if (preg_match('/^CLIENT -> SERVER: [A-Za-z0-9+\\/=]+$/', $line) === 1) {
                    $debugLog[] = 'CLIENT -> SERVER: [redacted]';
                    return;
                }
                $debugLog[] = $line;
            };
        }

        return $m;
    }

    // -------------------------------------------------------------------------
    // Métodos de envío
    // -------------------------------------------------------------------------

    /**
     * Envía el correo de verificación de dirección de email al registrarse.
     *
     * $data = [
     *   'correo'       => string,   // destino
     *   'link'         => string,   // URL con el token de verificación
     *   'user_auth_id' => int|null  // para el log (opcional)
     * ]
     *
     * @return array{ok: bool, error?: string}
     */
    public static function enviarVerificacionCorreo(array $data): array
    {
        $debugLog = [];
        $mail     = null;
        $html     = '';

        try {
            $tplPath = __DIR__ . '/template/verificacion_correo.html';
            if (!is_file($tplPath)) {
                throw new \RuntimeException('Template verificacion_correo.html no encontrado.');
            }

            $correo = (string)($data['correo'] ?? '');
            $link   = (string)($data['link']   ?? '');

            if ($correo === '' || $link === '') {
                throw new \InvalidArgumentException('correo y link son obligatorios.');
            }

            $tpl  = (string)file_get_contents($tplPath);
            $html = str_replace(
                ['{{title}}', '{{correo}}', '{{link}}'],
                [
                    'Verificá tu correo — Impulsa',
                    htmlspecialchars($correo, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($link,   ENT_QUOTES, 'UTF-8'),
                ],
                $tpl
            );

            $mail          = self::baseMailer($debugLog);
            $subject       = 'Verificá tu dirección de correo — Impulsa';
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = "Verificá tu correo en Impulsa.\n\nUsá este enlace: {$link}\n\nSi no creaste una cuenta, ignorá este mensaje.";
            $mail->addAddress($correo);

            $mail->send();

            self::logEmail([
                'user_auth_id' => $data['user_auth_id'] ?? null,
                'correo'       => $correo,
                'asunto'       => $subject,
                'template'     => 'verificacion_correo',
                'mensaje_html' => $html,
                'mensaje_text' => $mail->AltBody,
                'estado'       => 'enviado',
            ]);

            return ['ok' => true];

        } catch (\Throwable $e) {
            $mailError = $mail instanceof PHPMailer ? trim((string)$mail->ErrorInfo) : '';
            $debugText = '';
            if (!empty($debugLog)) {
                $debugText = ' SMTP Log: ' . implode(' | ', array_slice($debugLog, -10));
            }

            $errorMsg = $e->getMessage();
            if ($mailError !== '' && stripos($errorMsg, $mailError) === false) {
                $errorMsg .= ' | ErrorInfo: ' . $mailError;
            }

            self::logEmail([
                'user_auth_id' => $data['user_auth_id'] ?? null,
                'correo'       => $data['correo'] ?? '',
                'asunto'       => 'Verificá tu dirección de correo — Impulsa',
                'template'     => 'verificacion_correo',
                'mensaje_html' => $html ?: null,
                'mensaje_text' => $mail instanceof PHPMailer ? ($mail->AltBody ?? null) : null,
                'estado'       => 'fallido',
                'error'        => $errorMsg . $debugText,
            ]);

            return ['ok' => false, 'error' => $errorMsg . $debugText];
        }
    }
}
