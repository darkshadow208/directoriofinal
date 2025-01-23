<?php
session_start();
header('Content-Type: application/json');

// Conexión a la base de datos
$db = new PDO('mysql:host=localhost;dbname=directorio_negocios', 'root', '');

// Obtener la nueva contraseña del cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);
$newPassword = $data['newPassword'];

// Obtener el correo electrónico de la sesión
$email = $_SESSION['recovery_email'];

if ($email) {
    // Hashear la nueva contraseña
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Actualizar la contraseña en la base de datos
    $stmt = $db->prepare('UPDATE users SET pass = ? WHERE email = ?');
    $stmt->execute([$hashedPassword, $email]);

    // Limpiar la sesión
    session_unset();
    session_destroy();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Correo electrónico no válido']);
}
?>
