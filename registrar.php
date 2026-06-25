<?php
// 1. Datos de conexión a la base de datos
$servidor = "localhost";
$usuario  = "root";
$password = ""; // Contraseña vacía por defecto en XAMPP
$base_datos = "nanamimus";

// Crear la conexión
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error crítico de conexión: " . $conexion->connect_error);
}

// 2. Verificar que se hayan enviado los datos por el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recibir y limpiar variables
    $nombre    = trim($_POST['Nombre']);
    $apellido  = trim($_POST['Apellido']);
    $fecha_nac = trim($_POST['FechaNac']); // Va al campo 'fecha'
    $email     = trim($_POST['Email']);
    $telefono  = trim($_POST['Telefono']);
    $contra    = $_POST['Contra'];
    $confirmar_contra = $_POST['ConfirmarContra'];

    // 3. Validaciones de campos obligatorios
    if (empty($nombre) || empty($apellido) || empty($fecha_nac) || empty($email) || empty($contra)) {
        header("Location: registro.html?error=campos_vacios");
        exit;
    }

    // 4. Validación de coincidencia de contraseñas
    if ($contra !== $confirmar_contra) {
        header("Location: registro.html?error=no_coinciden");
        exit;
    }

    // 5. VALIDACIÓN DE CONTRASEÑA SEGURA (Cumple con los 3 requisitos de la imagen)
    // - (?=.*[A-Z]) : Al menos una letra mayúscula
    // - (?=.*\d)    : Al menos un número
    // - .{8,}       : Mínimo 8 caracteres de longitud
    $patron_contra = '/^(?=.*[A-Z])(?=.*\d).{8,}$/';

    if (!preg_match($patron_contra, $contra)) {
        header("Location: registro.html?error=contra_debil");
        exit;
    }

    // 6. Comprobar si el correo ya está registrado (UNIQUE)
    $buscar_email = "SELECT id FROM usuarios WHERE email = ?";
    $stmt_check = $conexion->prepare($buscar_email);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        header("Location: registro.html?error=email_duplicado");
        $stmt_check->close();
        exit;
    }
    $stmt_check->close();

    // 7. Cifrar la contraseña de forma segura
    $clave_encriptada = password_hash($contra, PASSWORD_BCRYPT);

    // 8. Preparar la consulta SQL de inserción
    $insertar_usuario = "INSERT INTO usuarios (nombre, apellido, email, clave, fecha, telefono) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt_insert = $conexion->prepare($insertar_usuario);
    $stmt_insert->bind_param("ssssss", $nombre, $apellido, $email, $clave_encriptada, $fecha_nac, $telefono);

    // 9. Ejecutar e iniciar sesión automática para ir al Index principal
    if ($stmt_insert->execute()) {
        // Obtenemos el ID del usuario que se acaba de registrar
        $usuario_id = $stmt_insert->insert_id;

        // Iniciamos la sesión en PHP para que el index lo reconozca como logueado
        session_start();
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['usuario_nombre'] = $nombre;
        $_SESSION['usuario_email'] = $email;

        // Redirección directa a la página principal sin alertas molestas
        header("Location: indexNanaMimus.php");
        exit;
    } else {
        // En caso de un error interno de la base de datos
        header("Location: registro.html?error=error_servidor");
        exit;
    }

    $stmt_insert->close();
}

$conexion->close();
?>