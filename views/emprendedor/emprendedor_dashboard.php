<?php
require_once __DIR__ . '/../../controllers/emprendedor_dashboardController.php';

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

        .profile-info h2 { margin: 0 0 4px; font-size: 20px; }
        .profile-info p  { margin: 0; font-size: 14px; color: #6b7280; }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #b45309; }

        /* ── Modal de perfil ── */
        .perfil-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
            padding: 24px;
        }
        .perfil-overlay.is-open {
            opacity: 1;
            pointer-events: auto;
        }
        .perfil-box {
            background: #fff;
            border-radius: 18px;
            padding: 26px;
            width: min(440px, 100%);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.15);
        }
        .perfil-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .perfil-header h3 { margin: 0; font-size: 17px; font-weight: 600; }
        .perfil-field { margin-bottom: 16px; }
        .perfil-field label {
            display: block;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 6px;
        }
        .perfil-field input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .perfil-field input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }
        .perfil-feedback {
            display: none;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 14px;
        }
        .perfil-feedback.ok    { background: #dcfce7; color: #15803d; }
        .perfil-feedback.error { background: #fee2e2; color: #b91c1c; }

        /* Botón de perfil en navbar */
        .navbar { justify-content: space-between; }
        .navbar-left { display: flex; align-items: center; gap: 8px; }
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
                <div class="navbar-left">
                    <button class="btn-icon" onclick="toggleSidebar()">
                        <span class="material-icons">menu</span>
                    </button>
                    <div class="navbar-title">Mi espacio</div>
                </div>
                <button class="btn-icon" id="btn-perfil" aria-label="Mi perfil" title="Mi perfil">
                    <span class="material-icons">account_circle</span>
                </button>
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
                                <span class="badge badge-success" style="margin-top:6px">
                                    <span class="material-icons" style="font-size:14px">verified</span>
                                    Correo verificado
                                </span>
                            <?php else: ?>
                                <span class="badge badge-warning" style="margin-top:6px">
                                    <span class="material-icons" style="font-size:14px">warning</span>
                                    Correo sin verificar
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </section>
        </div>
    </div>

    <!-- MODAL DE PERFIL -->
    <div class="perfil-overlay" id="modal-perfil" aria-hidden="true">
        <div class="perfil-box" role="dialog" aria-labelledby="perfil-title">
            <div class="perfil-header">
                <h3 id="perfil-title">Mi perfil</h3>
                <button class="btn-icon" id="btn-cerrar-perfil" aria-label="Cerrar">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div class="perfil-feedback" id="perfil-feedback"></div>
            <form id="form-perfil" novalidate>
                <div class="perfil-field">
                    <label for="p-nombre">Nombre</label>
                    <input id="p-nombre" type="text" name="nombre"
                        value="<?= htmlspecialchars($perfil['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Tu nombre">
                </div>
                <div class="perfil-field">
                    <label for="p-apellido">Apellido</label>
                    <input id="p-apellido" type="text" name="apellido"
                        value="<?= htmlspecialchars($perfil['apellido'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Tu apellido">
                </div>
                <div class="perfil-field">
                    <label for="p-apodo">Apodo</label>
                    <input id="p-apodo" type="text" name="apodo"
                        value="<?= htmlspecialchars($perfil['apodo'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Apodo o nombre de marca">
                </div>
                <div class="perfil-field">
                    <label for="p-fecha">Fecha de nacimiento</label>
                    <input id="p-fecha" type="date" name="fecha_nacimiento"
                        value="<?= htmlspecialchars($perfil['fecha_nacimiento'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <button class="btn btn-primary" type="submit" id="btn-guardar-perfil" style="width:100%">
                    Guardar cambios
                </button>
            </form>
        </div>
    </div>

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

        // ── Modal de perfil ──
        const modalPerfil    = document.getElementById('modal-perfil');
        const btnPerfil      = document.getElementById('btn-perfil');
        const btnCerrar      = document.getElementById('btn-cerrar-perfil');
        const formPerfil     = document.getElementById('form-perfil');
        const perfilFeedback = document.getElementById('perfil-feedback');
        const btnGuardar     = document.getElementById('btn-guardar-perfil');

        const abrirModal = () => {
            modalPerfil.classList.add('is-open');
            modalPerfil.setAttribute('aria-hidden', 'false');
            document.getElementById('p-nombre').focus();
        };

        const cerrarModal = () => {
            modalPerfil.classList.remove('is-open');
            modalPerfil.setAttribute('aria-hidden', 'true');
            perfilFeedback.className = 'perfil-feedback';
            perfilFeedback.style.display = 'none';
        };

        btnPerfil.addEventListener('click', abrirModal);
        btnCerrar.addEventListener('click', cerrarModal);
        modalPerfil.addEventListener('click', (e) => { if (e.target === modalPerfil) cerrarModal(); });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modalPerfil.classList.contains('is-open')) cerrarModal();
        });

        formPerfil.addEventListener('submit', async (e) => {
            e.preventDefault();
            btnGuardar.disabled = true;
            btnGuardar.textContent = 'Guardando...';
            perfilFeedback.style.display = 'none';

            try {
                const res  = await fetch('/controllers/perfil_controller.php', {
                    method: 'POST',
                    body: new FormData(formPerfil),
                });
                const data = await res.json();

                if (data.ok) {
                    perfilFeedback.className = 'perfil-feedback ok';
                    perfilFeedback.textContent = 'Perfil guardado correctamente.';
                    perfilFeedback.style.display = 'block';
                    setTimeout(cerrarModal, 1400);
                } else {
                    perfilFeedback.className = 'perfil-feedback error';
                    perfilFeedback.textContent = data.error ?? 'Error al guardar.';
                    perfilFeedback.style.display = 'block';
                }
            } catch {
                perfilFeedback.className = 'perfil-feedback error';
                perfilFeedback.textContent = 'Error de conexión. Intentá de nuevo.';
                perfilFeedback.style.display = 'block';
            } finally {
                btnGuardar.disabled = false;
                btnGuardar.textContent = 'Guardar cambios';
            }
        });
    </script>
</body>

</html>
