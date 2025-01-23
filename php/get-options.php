<?php
$action = $_GET['action'] ?? '';

$db = new PDO('mysql:host=localhost;dbname=directorio_negocios', 'root', '');

// Verificar la acción y ejecutar la acción correspondiente
if ($action === 'opciones') {
    // Consulta para obtener las categorías
    $stmt = $db->prepare('SELECT id, name FROM categories');
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver las categorías en formato JSON
    echo json_encode($categories);
} else {
    // Acción no reconocida
    echo json_encode(['error' => 'Acción no reconocida']);
}