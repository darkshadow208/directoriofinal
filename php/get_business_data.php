<?php
session_start();
require_once 'conexion.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['business_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

try {
    // Obtener datos del negocio
    $stmt = $pdo->prepare("SELECT b.*, u.email, u.nombre as owner_name 
                          FROM businesses b 
                          JOIN users u ON b.user_id = u.id 
                          WHERE b.business_id = ?");
    $stmt->execute([$_SESSION['business_id']]);
    $businessData = $stmt->fetch();

    // Obtener productos
    $stmt = $pdo->prepare("SELECT * FROM products WHERE business_id = ?");
    $stmt->execute([$_SESSION['business_id']]);
    $products = $stmt->fetchAll();

    // Obtener promociones activas
    $stmt = $pdo->prepare("SELECT * FROM promotions WHERE business_id = ? AND end_date >= CURRENT_DATE()");
    $stmt->execute([$_SESSION['business_id']]);
    $promotions = $stmt->fetchAll();

    // Obtener eventos
    $stmt = $pdo->prepare("SELECT * FROM events WHERE business_id = ? AND event_date >= CURRENT_DATE()");
    $stmt->execute([$_SESSION['business_id']]);
    $events = $stmt->fetchAll();

    // Obtener reseñas
    $stmt = $pdo->prepare("SELECT r.*, u.nombre as reviewer_name 
                          FROM reviews r 
                          JOIN users u ON r.user_id = u.id 
                          WHERE r.business_id = ? 
                          ORDER BY r.created_at DESC");
    $stmt->execute([$_SESSION['business_id']]);
    $reviews = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => [
            'business' => $businessData,
            'products' => $products,
            'promotions' => $promotions,
            'events' => $events,
            'reviews' => $reviews
        ]
    ]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
?>
