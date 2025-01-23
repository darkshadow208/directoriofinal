<?php
header('Content-Type: application/json');
   

try {
    // Validar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Obtener datos del FormData
    $data = [
        'username' => $_POST['username'] ?? null,
        'email' => $_POST['email'] ?? null,
        'pass' => $_POST['pass'] ?? null,
        'name' => $_POST['nombre'] ?? null,
        'description' => $_POST['description'] ?? null,
        'category_id' => $_POST['category_id'] ?? null,
        'address' => $_POST['address'] ?? null,
        'phone' => $_POST['phone'] ?? null,
        'website' => $_POST['website'] ?? null,
       

    ];

    // Validar campos requeridos
    $requiredFields = [
        'username', 'email', 'pass',          // Datos de usuario
        'name', 'description', 'category_id',  // Datos básicos del negocio
        'address', 'phone'                     // Datos adicionales
    ];

    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            throw new Exception("El campo {$field} es requerido");
        }
    }

    // Validar email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    // Manejar archivos si se enviaron
    $logoPath = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/business_logos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('business_') . '.' . $fileExtension;
        $logoPath = $uploadDir . $fileName;
        
        if (!move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath)) {
            throw new Exception('Error al subir el logo');
        }
        
        // Guardar solo la ruta relativa en la base de datos
        $logoPath = 'uploads/business_logos/' . $fileName;
    }

    // Conexión a la base de datos
    $db = new PDO(
        'mysql:host=localhost;dbname=directorio_negocios;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Iniciar transacción
    $db->beginTransaction();

    try {
        // Verificar si el usuario ya existe
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ? OR username = ?');
        $stmt->execute([$data['email'], $data['username']]);
        
        if ($stmt->rowCount() > 0) {
            throw new Exception('El usuario o email ya existe');
        }

        // Hash de la contraseña
        $passwordHash = password_hash($data['pass'], PASSWORD_DEFAULT);

        // Insertar usuario
        $stmt = $db->prepare('
    INSERT INTO users (
        username,
        email,
        pass,
        role,
        nombre,
        created_at,
        updated_at
    ) VALUES (
        ?, ?, ?, "business", ?, NOW(), NOW()
    )
');

$stmt->execute([
    $data['username'],
    $data['email'],
    $passwordHash,
    
    $data['name']
]);

        $userId = $db->lastInsertId();

        $stmt = $db->prepare('
        INSERT INTO businesses (
            owner_id,
            name,
            description,
            category_id,
            address,
            latitude,
            longitude,
            phone,
            email,
            website,
            created_at,
            updated_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
        )
    ');
    
    $stmt->execute([
        $userId,                 // owner_id
        $data['name'],
        $data['description'],
        $data['category_id'],
        $data['address'],
        $data['latitude'] ?? null,
        $data['longitude'] ?? null,
        $data['phone'],
        $data['email'],
        $data['website'] ?? null
    ]);
        // Confirmar transacción
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Negocio registrado exitosamente'
        ]);

    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $db->rollBack();
        // Si se subió un archivo, eliminarlo
        if ($logoPath && file_exists($logoPath)) {
            unlink($logoPath);
        }
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
