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
                    <li onclick="location.href='landing_page_request.php'">
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

    </script>
</body>

</html>
