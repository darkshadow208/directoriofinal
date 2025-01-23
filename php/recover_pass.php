<?php
session_start();
header('Content-Type: application/json');

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Conexión a la base de datos
try {
    $db = new PDO('mysql:host=localhost;dbname=directorio_negocios', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log('Error de conexión a la base de datos: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['email'])) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
        exit;
  
    }

    $email = $_POST['email'];

    // Verificar si el correo electrónico existe en la base de datos
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generar un código único
        $uniqueCode = bin2hex(random_bytes(16));

        // Almacenar el código único y el correo electrónico en la sesión
        $_SESSION['recovery_code'] = $uniqueCode;
        $_SESSION['recovery_email'] = $email;

        // Enviar el código único por correo electrónico utilizando PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host       = 'mail.fabimaker.es';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'proyecto@fabimaker.es';
            $mail->Password   = '3654658a';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('proyecto@fabimaker.es', 'admin');

            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Código de Recuperación de Contraseña';
            $mail->Body    = "Tu código de recuperación de contraseña es: $uniqueCode";
            $mail->AltBody = "Tu código de recuperación de contraseña es: $uniqueCode";
           
            $mail->send();
          
            echo json_encode(['success' => true, 'message' => 'Código de recuperación enviado']);
        } catch (Exception $e) {
           
            error_log('Error al enviar el correo electrónico: ' . $mail->ErrorInfo);
            echo json_encode(['success' => false, 'message' => 'Error al enviar el correo electrónico: ' . $mail->ErrorInfo]);
        }
    } else {
        error_log('Correo electrónico no encontrado');
        echo json_encode(['success' => false, 'message' => 'Correo electrónico no encontrado']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido']);
}
?>
