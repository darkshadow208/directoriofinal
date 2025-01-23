
<?php
session_start();
header('Content-Type: application/json');

require_once 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

try {
    // Corregimos la consulta para usar la estructura correcta de las tablas
    $stmt = $pdo->prepare("SELECT u.*, b.id as business_id, b.name as business_name, 
                          b.category_id, c.name as category_name
                          FROM users u 
                          LEFT JOIN businesses b ON u.id = b.owner_id 
                          LEFT JOIN categories c ON b.category_id = c.id
                          WHERE u.id = ?");
    
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['business_id']) {
        // Actualizamos las variables de sesión con la información del negocio
        $_SESSION['is_business'] = true;
        $_SESSION['business_id'] = $user['business_id'];
        $_SESSION['business_name'] = $user['business_name'];
        $_SESSION['business_category_id'] = $user['category_id'];
        $_SESSION['business_category_name'] = $user['category_name'];
        
        echo json_encode([
            'status' => 'success',
            'is_business' => true,
            'redirect' => 'profile-business.html',
            'business_name' => $user['business_name'],
            'category_name' => $user['category_name']
        ]);
    } else {
        $_SESSION['is_business'] = false;
        echo json_encode([
            'status' => 'success',
            'is_business' => false,
            'redirect' => 'profile-user.html'
        ]);
    }
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Error de servidor'
        // En producción, no mostrar el mensaje de error detallado
        // 'debug' => $e->getMessage() 
    ]);
}
?>

