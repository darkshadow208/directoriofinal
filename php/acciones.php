<?php
session_start();
require_once 'conexion.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['usario'];
    $password = $_POST['contrasena'];

    try {
        // Modificamos la consulta para incluir información del negocio y la categoría
        $sql = "SELECT u.*, b.id as business_id, b.name as business_name, 
                b.category_id, c.name as category_name
                FROM users u 
                LEFT JOIN businesses b ON u.id = b.owner_id 
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE u.email = :username";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            if (password_verify($password, $user['pass'])) {
                // Variables de sesión básicas
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['nombre'];
                $_SESSION['profileImageURL'] = $user['photo'];

                // Verificar si es un usuario de negocio
                if ($user['business_id']) {
                    $_SESSION['is_business'] = true;
                    $_SESSION['business_id'] = $user['business_id'];
                    $_SESSION['business_name'] = $user['business_name'];
                    $_SESSION['business_category_id'] = $user['category_id'];
                    $_SESSION['business_category_name'] = $user['category_name'];
                } else {
                    $_SESSION['is_business'] = false;
                }

                $response = [
                    'success' => true,
                    'message' => 'Inicio de sesión exitoso',
                    'is_business' => isset($_SESSION['is_business']) && $_SESSION['is_business'],
                    'redirect' => $_SESSION['is_business'] ? 'profile-business.html' : 'profile-user.html'
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Contraseña inválida'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'Correo no encontrado'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'success' => false,
            'message' => 'Error en el servidor: ' . $e->getMessage()
        ];
    }

    echo json_encode($response);
}
?>
