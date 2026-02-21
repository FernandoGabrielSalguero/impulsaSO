<?php
require_once __DIR__ . '/../../controllers/emprendedor_dashboardController.php';

// Nombre de display: apodo > nombre > correo
$displayName = $perfil['apodo'] ?? $perfil['nombre'] ?? $_SESSION['correo'] ?? 'Emprendedor';
$displayName = htmlspecialchars((string) $displayName, ENT_QUOTES, 'UTF-8');

$correoVerificado = !empty($perfil['check_correo']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Impulsa — Mi espacio</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- Framework Impulsa desde CDN -->
    <link rel="stylesheet" href="https://framework.impulsagroup.com/assets/css/framework.css">
    <script src="https://framework.impulsagroup.com/assets/javascript/framework.js" defer></script>

    <style>
        .profile-card {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .profile-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .profile-info h2 {
            margin: 0 0 4px;
            font-size: 20px;
        }

        .profile-info p {
            margin: 0;
            font-size: 14px;
            color: #6b7280;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-warning {
            background: #fef3c7;
            color: #b45309;
        }

        .data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 14px;
            margin-top: 18px;
        }

        .data-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px 16px;
        }

        .data-item .label {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-item .value {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
        }

        .data-item .value.empty {
            color: #d1d5db;
            font-weight: 400;
            font-style: italic;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin: 0 0 14px;
        }
    </style>
</head>

<body>

    <div class="layout">

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <span class="material-icons logo-icon">rocket_launch</span>
                <span class="logo-text">Impulsa</span>
            </div>

            <nav class="sidebar-menu">
                <ul>
                    <li onclick="location.href='emprendedor_dashboard.php'">
                        <span class="material-icons" style="color:#6366f1">home</span>
                        <span class="link-text">Inicio</span>
                    </li>
                    <li onclick="location.href='../../logout.php'">
                        <span class="material-icons" style="color:red">logout</span>
                        <span class="link-text">Salir</span>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <button class="btn-icon" onclick="toggleSidebar()">
                    <span class="material-icons" id="collapseIcon">chevron_left</span>
                </button>
            </div>
        </aside>

        <!-- MAIN -->
        <div class="main">

            <!-- NAVBAR -->
            <header class="navbar">
                <button class="btn-icon" onclick="toggleSidebar()">
                    <span class="material-icons">menu</span>
                </button>
                <div class="navbar-title">Mi espacio</div>
            </header>

            <!-- CONTENIDO -->
            <section class="content">

                <!-- Bienvenida -->
                <div class="card">
                    <div class="profile-card">
                        <div class="profile-avatar"><?= mb_substr($displayName, 0, 1) ?></div>
                        <div class="profile-info">
                            <h2>Hola, <?= $displayName ?></h2>
                            <p><?= htmlspecialchars($perfil['correo'] ?? $_SESSION['correo'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                            <?php if ($correoVerificado): ?>
                                <span class="badge badge-success">
                                    <span class="material-icons" style="font-size:14px">verified</span>
                                    Correo verificado
                                </span>
                            <?php else: ?>
                                <span class="badge badge-warning">
                                    <span class="material-icons" style="font-size:14px">warning</span>
                                    Correo sin verificar
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Datos del perfil -->
                <div class="card">
                    <p class="section-title">Datos de perfil</p>
                    <div class="data-grid">
                        <?php
                        $campos = [
                            'Nombre'            => $perfil['nombre']           ?? null,
                            'Apellido'          => $perfil['apellido']         ?? null,
                            'Apodo'             => $perfil['apodo']            ?? null,
                            'Fecha de nacimiento' => $perfil['fecha_nacimiento'] ?? null,
                            'Rol'               => $perfil['rol']              ?? $_SESSION['rol'] ?? null,
                            'Miembro desde'     => isset($perfil['created_at'])
                                                    ? date('d/m/Y', strtotime($perfil['created_at']))
                                                    : null,
                        ];
                        foreach ($campos as $label => $value): ?>
                            <div class="data-item">
                                <div class="label"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></div>
                                <?php if ($value !== null && $value !== ''): ?>
                                    <div class="value"><?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?></div>
                                <?php else: ?>
                                    <div class="value empty">Sin completar</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </section>
        </div>
    </div>

    <script>
        // Log de sesión en consola
        const sesion = {
            user_id:          <?= json_encode($_SESSION['user_id']          ?? null) ?>,
            correo:           <?= json_encode($_SESSION['correo']           ?? null) ?>,
            rol:              <?= json_encode($_SESSION['rol']              ?? null) ?>,
            nombre:           <?= json_encode($_SESSION['nombre']           ?? null) ?>,
            apellido:         <?= json_encode($_SESSION['apellido']         ?? null) ?>,
            apodo:            <?= json_encode($_SESSION['apodo']            ?? null) ?>,
            fecha_nacimiento: <?= json_encode($_SESSION['fecha_nacimiento'] ?? null) ?>,
        };
        console.group('[Impulsa] Sesión activa');
        console.table(sesion);
        console.groupEnd();
    </script>

</body>

</html>
