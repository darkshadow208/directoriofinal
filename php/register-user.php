<?php
header('Content-Type: application/json');

try {
    // Validar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Obtener datos del POST (FormData)
    $data = [
        'username' => $_POST['username'] ?? null,
        'email' => $_POST['email'] ?? null,
        'pass' => $_POST['pass'] ?? null,
        'nombre' => $_POST['nombre'] ?? null
    ];

    // Validar campos requeridos
    $requiredFields = ['username', 'email', 'pass', 'nombre'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            throw new Exception("El campo {$field} es requerido");
        }
    }

    // Validar email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    // Conexión a la base de datos
    $db = new PDO(
        'mysql:host=localhost;dbname=directorio_negocios;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Verificar si el usuario ya existe
    $stmt = $db->prepare('SELECT id FROM users WHERE email = :email OR username = :username');
    $stmt->execute([
        ':email' => $data['email'],
        ':username' => $data['username']
    ]);
    
    if ($stmt->rowCount() > 0) {
        throw new Exception('El usuario o email ya existe');
    }

    // Hash de la contraseña
    $hashedPassword = password_hash($data['pass'], PASSWORD_DEFAULT);

    // Insertar usuario
    $stmt = $db->prepare('
        INSERT INTO users (username, email, pass, role, nombre, created_at, updated_at)
        VALUES (:username, :email, :pass, :role, :nombre, NOW(), NOW())
    ');

    $stmt->execute([
        ':username' => $data['username'],
        ':email' => $data['email'],
        ':pass' => $hashedPassword,
        ':role' => 'normal',
        ':nombre' => $data['nombre']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Usuario registrado exitosamente'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
