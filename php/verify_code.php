<?php
session_start();
header('Content-Type: application/json');

// Obtener el código del cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);
$code = $data['code'];

// Verificar si el código en la sesión coincide con el código ingresado
if (isset($_SESSION['recovery_code']) && $_SESSION['recovery_code'] === $code) {
    // El código es válido
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Código inválido']);
}
?>
