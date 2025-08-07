<?php
$error = '';

if (isset($_GET['expired']) && $_GET['expired'] == 1) {
    $error = "La sesión expiró por inactividad. Por favor, iniciá sesión nuevamente.";
} elseif (isset($_GET['error']) && $_GET['error'] == 1) {
    $error = "Usuario o contraseña incorrectos.";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar / Registrarse</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #8e44ad, #3498db);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-wrapper {
            width: 400px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        form {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            transition: opacity 0.4s ease;
            display: none;
        }

        form.active {
            display: block;
        }

        h1 {
            text-align: center;
            color: #673ab7;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            color: #444;
            margin-bottom: 5px;
            display: block;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .form-group button {
            width: 100%;
            padding: 12px;
            background-color: #673ab7;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-group button:hover {
            background-color: #512da8;
        }

        .toggle-link {
            text-align: center;
            margin-top: 20px;
            color: #3498db;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s;
        }

        .toggle-link:hover {
            color: #1b4f72;
        }

        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Animaciones tipo arena */
        @keyframes dissolve {
            0% {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0px);
                clip-path: polygon(0 0, 100% 0, 100% 100%, 0% 100%);
            }
            100% {
                opacity: 0;
                transform: translateY(-30px) scale(0.7);
                filter: blur(4px);
                clip-path: polygon(50% 0, 100% 20%, 80% 100%, 20% 100%, 0 20%);
            }
        }

        @keyframes assemble {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.7);
                filter: blur(4px);
                clip-path: polygon(50% 100%, 100% 80%, 80% 0%, 20% 0%, 0 80%);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0px);
                clip-path: polygon(0 0, 100% 0, 100% 100%, 0% 100%);
            }
        }

        .dissolve {
            animation: dissolve 0.6s forwards ease-out;
        }

        .assemble {
            animation: assemble 0.6s forwards ease-out;
        }
    </style>
</head>

<body>
    <div class="form-wrapper" id="formWrapper">
        <!-- LOGIN -->
        <form action="/login_handler.php" method="POST" id="loginForm" class="active">
            <h1>Iniciar Sesión</h1>
            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" name="contrasena" id="contrasena" required>
            </div>
            <div class="form-group">
                <button type="submit">INGRESAR</button>
            </div>
            <div class="toggle-link" onclick="toggleForms()">¿No tenés cuenta? Registrate</div>
        </form>

        <!-- REGISTRO -->
        <form action="/register_handler.php" method="POST" id="registerForm">
            <h1>Registro</h1>
            <div class="form-group">
                <label for="user_name">Nombre de usuario:</label>
                <input type="text" name="user_name" id="user_name" required>
            </div>
            <div class="form-group">
                <label for="pass">Contraseña:</label>
                <input type="password" name="pass" id="pass" required>
            </div>
            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <button type="submit">REGISTRAR</button>
            </div>
            <div class="toggle-link" onclick="toggleForms()">¿Ya tenés cuenta? Iniciá sesión</div>
        </form>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        function toggleForms() {
            const showingLogin = loginForm.classList.contains('active');

            const currentForm = showingLogin ? loginForm : registerForm;
            const nextForm = showingLogin ? registerForm : loginForm;

            currentForm.classList.remove('assemble');
            currentForm.classList.add('dissolve');

            setTimeout(() => {
                currentForm.classList.remove('dissolve', 'active');
                nextForm.classList.add('active', 'assemble');

                setTimeout(() => {
                    nextForm.classList.remove('assemble');
                }, 600);
            }, 400);
        }
    </script>
</body>

</html>
