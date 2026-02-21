<?php
require_once __DIR__ . '/../../controllers/landing_page_requestController.php';

// Valores actuales o precargados
$val = function(string $key, string $fallback = '') use ($request): string {
    return htmlspecialchars((string)($request[$key] ?? $fallback), ENT_QUOTES, 'UTF-8');
};

$displayName = $_SESSION['apodo'] ?? $_SESSION['nombre'] ?? $_SESSION['correo'] ?? 'Emprendedor';
$displayName = htmlspecialchars((string) $displayName, ENT_QUOTES, 'UTF-8');

$esEdicion          = isset($request['id']);
$espFisico          = !empty($request['espacio_fisico']);
$vendeProductos     = !empty($request['vende_productos']);
$vendeServicios     = !empty($request['vende_servicios']);
$dominioReg         = !empty($request['dominio_registrado']);
$hostingPropio      = !empty($request['hosting_propio']);
$yaFactura          = !empty($request['ya_factura']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Impulsa — Solicitud Landing Page</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <link rel="stylesheet" href="https://framework.impulsagroup.com/assets/css/framework.css">
    <script src="https://framework.impulsagroup.com/assets/javascript/framework.js" defer></script>

    <style>
        /* Navbar */
        .navbar { justify-content: space-between; }
        .navbar-left { display: flex; align-items: center; gap: 8px; }

        /* Formulario */
        .lp-form-card {
            background: #fff;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            max-width: 700px;
        }

        .lp-section-label {
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 24px 0 12px;
        }
        .lp-section-label:first-child { margin-top: 0; }

        .lp-field { margin-bottom: 14px; }
        .lp-field > label:not(.lp-toggle) {
            display: block;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .lp-field input[type="text"],
        .lp-field input[type="tel"],
        .lp-field input[type="date"],
        .lp-field input[type="number"],
        .lp-field textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            box-sizing: border-box;
            font-family: inherit;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .lp-field input:focus,
        .lp-field textarea:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        }
        .lp-field textarea { resize: vertical; min-height: 90px; }

        /* Toggle */
        .lp-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            cursor: pointer;
            user-select: none;
            margin-bottom: 0;
        }
        .lp-toggle:hover { background: #f9fafb; }
        .lp-toggle-text { font-size: 14px; color: #374151; }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 22px;
            flex-shrink: 0;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }
        .toggle-slider {
            position: absolute;
            inset: 0;
            background: #d1d5db;
            border-radius: 22px;
            transition: background 0.2s;
            cursor: pointer;
        }
        .toggle-slider::before {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            left: 3px;
            top: 3px;
            background: #fff;
            border-radius: 50%;
            transition: transform 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.18);
        }
        .toggle-switch input:checked + .toggle-slider              { background: #6366f1; }
        .toggle-switch input:checked + .toggle-slider::before      { transform: translateX(18px); }

        /* Checkboxes vende */
        .lp-check-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .lp-check-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            color: #374151;
            user-select: none;
            transition: border-color 0.15s, background 0.15s;
        }
        .lp-check-item input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; accent-color: #6366f1; }
        .lp-check-item:has(input:checked) {
            border-color: #6366f1;
            background: #eef2ff;
            color: #4338ca;
        }

        /* Dirección */
        .lp-address-block { display: none; }
        .lp-address-block.visible { display: block; }

        .lp-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        @media (max-width: 560px) {
            .lp-grid-2 { grid-template-columns: 1fr; }
        }

        /* Feedback */
        .lp-feedback {
            display: none;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .lp-feedback { display: none; }
        .lp-feedback.ok    { display: flex; background: #dcfce7; color: #15803d; }
        .lp-feedback.error { display: flex; background: #fee2e2; color: #b91c1c; }
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
                    <li class="active" onclick="location.href='landing_page_request.php'">
                        <span class="material-icons" style="color:#6366f1">rocket_launch</span>
                        <span class="link-text">Landing Page</span>
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
                <div class="navbar-left">
                    <button class="btn-icon" onclick="toggleSidebar()">
                        <span class="material-icons">menu</span>
                    </button>
                    <div class="navbar-title">Solicitud Landing Page</div>
                </div>
                <button class="btn-icon" id="btn-perfil" aria-label="Mi perfil" title="Mi perfil">
                    <span class="material-icons">account_circle</span>
                </button>
            </header>

            <!-- CONTENIDO -->
            <section class="content">

                <div class="lp-form-card">
                    <h2 style="margin:0 0 4px;font-size:18px">
                        <?= $esEdicion ? 'Editá tu solicitud' : 'Completá tu solicitud' ?>
                    </h2>
                    <p style="margin:0 0 20px;font-size:14px;color:#6b7280">
                        <?= $esEdicion
                            ? 'Tus datos están guardados. Podés actualizarlos cuando quieras.'
                            : 'Completá la información sobre tu emprendimiento para solicitar tu landing page.' ?>
                    </p>

                    <div class="lp-feedback" id="lp-feedback"></div>

                    <form id="form-landing" novalidate>

                        <p class="lp-section-label">Datos del emprendimiento</p>

                        <div class="lp-field">
                            <label for="lp-nombre-emp">Nombre del emprendimiento</label>
                            <input id="lp-nombre-emp" type="text" name="nombre_emprendimiento"
                                value="<?= $val('nombre_emprendimiento') ?>"
                                placeholder="Ej: Café del Sur">
                        </div>

                        <div class="lp-field">
                            <label for="lp-fecha">Fecha de inicio del emprendimiento</label>
                            <input id="lp-fecha" type="date" name="fecha_inicio"
                                value="<?= $val('fecha_inicio') ?>">
                        </div>

                        <div class="lp-field">
                            <label for="lp-desc">Descripción</label>
                            <textarea id="lp-desc" name="descripcion"
                                placeholder="Contanos de qué trata tu emprendimiento..."><?= $val('descripcion') ?></textarea>
                        </div>

                        <div class="lp-field">
                            <label for="lp-fundador">Nombre del fundador</label>
                            <input id="lp-fundador" type="text" name="nombre_fundador"
                                value="<?= $val('nombre_fundador', $request['perfil_nombre'] ?? '') ?>"
                                placeholder="Tu nombre completo">
                        </div>

                        <p class="lp-section-label">¿Qué ofrecés?</p>

                        <div class="lp-field">
                            <div class="lp-check-row">
                                <label class="lp-check-item">
                                    <input type="checkbox" name="vende_productos" value="1"
                                        <?= $vendeProductos ? 'checked' : '' ?>>
                                    Productos
                                </label>
                                <label class="lp-check-item">
                                    <input type="checkbox" name="vende_servicios" value="1"
                                        <?= $vendeServicios ? 'checked' : '' ?>>
                                    Servicios
                                </label>
                            </div>
                        </div>

                        <p class="lp-section-label">Situación actual</p>

                        <div class="lp-field">
                            <label class="lp-toggle">
                                <span class="lp-toggle-text">¿Ya facturás?</span>
                                <span class="toggle-switch">
                                    <input type="checkbox" id="toggle-factura" name="ya_factura" value="1"
                                        <?= $yaFactura ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </span>
                            </label>
                        </div>

                        <div class="lp-field">
                            <label for="lp-colaboradores">Cantidad de colaboradores</label>
                            <input id="lp-colaboradores" type="number" name="cantidad_colaboradores"
                                value="<?= $val('cantidad_colaboradores', '0') ?>"
                                min="0" placeholder="0">
                        </div>

                        <p class="lp-section-label">Infraestructura web</p>

                        <div class="lp-field">
                            <label class="lp-toggle">
                                <span class="lp-toggle-text">¿Tenés dominio registrado?</span>
                                <span class="toggle-switch">
                                    <input type="checkbox" name="dominio_registrado" value="1"
                                        <?= $dominioReg ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </span>
                            </label>
                        </div>

                        <div class="lp-field">
                            <label class="lp-toggle">
                                <span class="lp-toggle-text">¿Tenés hosting propio?</span>
                                <span class="toggle-switch">
                                    <input type="checkbox" name="hosting_propio" value="1"
                                        <?= $hostingPropio ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </span>
                            </label>
                        </div>

                        <p class="lp-section-label">Espacio físico</p>

                        <div class="lp-field">
                            <label class="lp-toggle" id="toggle-espacio-label">
                                <span class="lp-toggle-text">¿Tenés local o espacio físico?</span>
                                <span class="toggle-switch">
                                    <input type="checkbox" id="toggle-espacio" name="espacio_fisico" value="1"
                                        <?= $espFisico ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </span>
                            </label>
                        </div>

                        <!-- Dirección (visible solo si espacio_fisico = 1) -->
                        <div class="lp-address-block <?= $espFisico ? 'visible' : '' ?>" id="lp-address-block">

                            <div class="lp-field">
                                <label for="lp-pais">País</label>
                                <input id="lp-pais" type="text" name="pais"
                                    value="<?= $val('pais') ?>"
                                    placeholder="Ej: Argentina">
                            </div>

                            <div class="lp-grid-2">
                                <div class="lp-field">
                                    <label for="lp-provincia">Provincia</label>
                                    <input id="lp-provincia" type="text" name="provincia"
                                        value="<?= $val('provincia') ?>"
                                        placeholder="Ej: Buenos Aires">
                                </div>
                                <div class="lp-field">
                                    <label for="lp-localidad">Localidad</label>
                                    <input id="lp-localidad" type="text" name="localidad"
                                        value="<?= $val('localidad') ?>"
                                        placeholder="Ej: La Plata">
                                </div>
                            </div>

                            <div class="lp-grid-2">
                                <div class="lp-field">
                                    <label for="lp-calle">Calle</label>
                                    <input id="lp-calle" type="text" name="calle"
                                        value="<?= $val('calle') ?>"
                                        placeholder="Ej: Av. Siempreviva">
                                </div>
                                <div class="lp-field">
                                    <label for="lp-numero">Número</label>
                                    <input id="lp-numero" type="text" name="numero"
                                        value="<?= $val('numero') ?>"
                                        placeholder="Ej: 742">
                                </div>
                            </div>
                        </div>

                        <p class="lp-section-label">Contacto</p>

                        <div class="lp-field">
                            <label for="lp-telefono">Teléfono de contacto</label>
                            <input id="lp-telefono" type="tel" name="telefono_contacto"
                                value="<?= $val('telefono_contacto', $request['perfil_whatsapp'] ?? '') ?>"
                                placeholder="+54911XXXXXXXX">
                        </div>

                        <button class="btn btn-aceptar" type="submit" id="btn-guardar-lp"
                            style="width:100%;margin-top:24px">
                            <?= $esEdicion ? 'Actualizar solicitud' : 'Enviar solicitud' ?>
                        </button>

                    </form>
                </div>

            </section>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../partials/modal_perfil/modal_perfil.php'; ?>

    <script>
        // Sesión en consola
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

        // Toggle espacio físico
        const toggleEspacio  = document.getElementById('toggle-espacio');
        const addressBlock   = document.getElementById('lp-address-block');

        toggleEspacio.addEventListener('change', () => {
            if (toggleEspacio.checked) {
                addressBlock.classList.add('visible');
            } else {
                addressBlock.classList.remove('visible');
            }
        });

        // Submit AJAX
        const formLanding = document.getElementById('form-landing');
        const feedback    = document.getElementById('lp-feedback');
        const btnGuardar  = document.getElementById('btn-guardar-lp');

        function showFeedback(type, msg) {
            feedback.className = 'lp-feedback ' + type;
            feedback.innerHTML = '<span class="material-icons" style="font-size:18px">'
                + (type === 'ok' ? 'check_circle' : 'error')
                + '</span>' + msg;
        }

        formLanding.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validación cliente: al menos un checkbox de vende
            const prodCheck = formLanding.querySelector('input[name="vende_productos"]');
            const servCheck = formLanding.querySelector('input[name="vende_servicios"]');
            if (!prodCheck.checked && !servCheck.checked) {
                showFeedback('error', 'Debés seleccionar al menos una opción: Productos o Servicios');
                feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                return;
            }

            btnGuardar.disabled = true;
            btnGuardar.textContent = 'Guardando...';
            feedback.className = 'lp-feedback';

            // Construir FormData manualmente para los toggles (enviar "1" o "0")
            const fd = new FormData(formLanding);

            // Los toggles tipo checkbox solo envían valor si checked.
            // El backend espera '1' para true; si no está en FormData, será '0' por ausencia.
            // Solo necesitamos asegurar espacio_fisico para limpiar dirección si no está marcado.

            try {
                const res  = await fetch('/controllers/landing_page_requestSaveController.php', {
                    method: 'POST',
                    body: fd,
                });
                const data = await res.json();

                if (data.ok) {
                    showFeedback('ok', 'Guardado correctamente.');
                    feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    setTimeout(() => location.reload(), 1400);
                } else {
                    showFeedback('error', data.error ?? 'Error al guardar.');
                    feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            } catch {
                showFeedback('error', 'Error de conexión. Intentá de nuevo.');
                feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } finally {
                btnGuardar.disabled = false;
                btnGuardar.textContent = <?= json_encode($esEdicion ? 'Actualizar solicitud' : 'Enviar solicitud') ?>;
            }
        });
    </script>
</body>

</html>
