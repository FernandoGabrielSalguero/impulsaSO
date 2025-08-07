<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/core/SessionManager.php';

// Iniciar la sesión
SessionManager::start();

// Validar datos POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = trim($_POST['user_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['pass'] ?? '';

    if (!$user_name || !$email || !$password) {
        header("Location: /index.php?error=1");
        exit;
    }

    try {
        // Verificar si el usuario o email ya existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_name = :user_name OR email = :email");
        $stmt->execute(['user_name' => $user_name, 'email' => $email]);
        $exists = $stmt->fetchColumn();

        if ($exists > 0) {
            header("Location: /index.php?error=1"); // Usuario ya existe
            exit;
        }

        // Hash de la contraseña
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insertar en tabla users
        $stmt = $pdo->prepare("INSERT INTO users (registration_date, user_name, pass, email) VALUES (NOW(), :user_name, :pass, :email)");
        $stmt->execute([
            'user_name' => $user_name,
            'pass' => $hashedPassword,
            'email' => $email
        ]);

        $userId = $pdo->lastInsertId();

        // Buscar el ID del rol 'client'
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'client'");
        $stmt->execute();
        $roleId = $stmt->fetchColumn();

        if (!$roleId) {
            throw new Exception("El rol 'client' no está configurado en la base de datos.");
        }

        // Asignar rol al nuevo usuario
        $stmt = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)");
        $stmt->execute([
            'user_id' => $userId,
            'role_id' => $roleId
        ]);

        // Obtener permisos para el rol
        $stmt = $pdo->prepare("
            SELECT p.name FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            WHERE rp.role_id = :role_id
        ");
        $stmt->execute(['role_id' => $roleId]);
        $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Crear sesión del usuario
        $sessionData = [
            'id' => $userId,
            'username' => $user_name,
            'email' => $email,
            'role' => 'client',
            'permissions' => $permissions
        ];
        SessionManager::setUser($sessionData);

        // Redirigir al dashboard
        header("Location: /views/client/client_dashboard.php");
        exit;
    } catch (Exception $e) {
        error_log("Error de registro: " . $e->getMessage());
        header("Location: /index.php?error=1");
        exit;
    }
} else {
    header("Location: /index.php");
    exit;
}
