<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Configuración de la base de datos
$host = 'localhost';
$db = 'directorio_negocios';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $userId = $_SESSION['user_id'];

 

    // Validar formato de email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Formato de email inválido');
    }

    // Obtener datos actuales del usuario
    $stmt = $pdo->prepare('SELECT username, email, nombre, photo FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $currentUser = $stmt->fetch();

    if (!$currentUser) {
        throw new Exception('Usuario no encontrado');
    }

    // Preparar datos para actualización
    $updateFields = [];
    $updateValues = [];
    $updateSQL = [];

    // Verificar y agregar campos modificados
    $fieldsToCheck = [
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'nombre' => $_POST['name']
    ];

    foreach ($fieldsToCheck as $field => $value) {
        if (!empty($value) && $value !== $currentUser[$field]) {
            $updateFields[] = $field;
            $updateValues[] = $value;
            $updateSQL[] = "$field = ?";
        }
    }

    // Manejar la foto del perfil
    if (!empty($_POST['profileImageURL'])) {
        $updateFields[] = 'photo';
        $updateValues[] = $_POST['profileImageURL'];
        $updateSQL[] = "photo = ?";
    }

    // Manejar la contraseña
    if (!empty($_POST['password'])) {
        $updateFields[] = 'pass';
        $updateValues[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $updateSQL[] = "pass = ?";
    }

    $pdo->beginTransaction();

    try {
        // Actualizar usuario si hay cambios
        if (!empty($updateSQL)) {
            $updateValues[] = $userId;
            $sql = 'UPDATE users SET ' . implode(', ', $updateSQL) . ' WHERE id = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($updateValues);

            // Actualizar negocios relacionados si email o nombre cambiaron
            if (in_array('email', $updateFields) || in_array('nombre', $updateFields)) {
                $businessUpdates = [];
                $businessValues = [];

                if (in_array('email', $updateFields)) {
                    $businessUpdates[] = "email = ?";
                    $businessValues[] = $_POST['email'];
                }
                if (in_array('nombre', $updateFields)) {
                    $businessUpdates[] = "name = ?";
                    $businessValues[] = $_POST['name'];
                }

                if (!empty($businessUpdates)) {
                    $businessValues[] = $userId;
                    $sql = 'UPDATE businesses SET ' . implode(', ', $businessUpdates) . ' WHERE owner_id = ?';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($businessValues);
                }
            }

            $pdo->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Usuario actualizado correctamente'
            ]);
        } else {
            $pdo->commit();
            echo json_encode([
                'success' => true,
                'message' => 'No hay cambios para actualizar'
            ]);
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
