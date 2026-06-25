<?php
// 1. Datos de conexión a la base de datos
$servidor = "localhost";
$usuario  = "root";
$password = ""; // Poné tu contraseña si tenés, por defecto en XAMPP viene vacío
$base_datos = "nanamimus";

// Crear la conexión
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error crítico de conexión: " . $conexion->connect_error);
}

// 2. Verificar que se hayan enviado los datos por el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recibir y limpiar variables para evitar espacios vacíos o inyecciones básicas
    $nombre    = trim($_POST['Nombre']);
    $apellido  = trim($_POST['Apellido']);
    $fecha_nac = trim($_POST['FechaNac']); // Va al campo 'fecha' de tu BD
    $email     = trim($_POST['Email']);
    $telefono  = trim($_POST['Telefono']);
    $contra    = $_POST['Contra'];
    $confirmar_contra = $_POST['ConfirmarContra'];

    // 3. Validaciones de seguridad
    if (empty($nombre) || empty($apellido) || empty($fecha_nac) || empty($email) || empty($contra)) {
        echo "<script>
                alert('Por favor, completa todos los campos obligatorios.');
                window.history.back();
              </script>";
        exit;
    }

    if ($contra !== $confirmar_contra) {
        echo "<script>
                alert('Las contraseñas no coinciden. Inténtalo de nuevo.');
                window.history.back();
              </script>";
        exit;
    }

    // 4. Comprobar si el correo ya está registrado (el campo email es UNIQUE)
    $buscar_email = "SELECT id FROM usuarios WHERE email = ?";
    $stmt_check = $conexion->prepare($buscar_email);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<script>
                alert('Este correo electrónico ya se encuentra registrado.');
                window.history.back();
              </script>";
        $stmt_check->close();
        exit;
    }
    $stmt_check->close();

    // 5. Cifrar la contraseña (se encripta igual al formato bcrypt de tu BD)
    $clave_encriptada = password_hash($contra, PASSWORD_BCRYPT);

    // 6. Preparar la consulta SQL de inserción
    $insertar_usuario = "INSERT INTO usuarios (nombre, apellido, email, clave, fecha, telefono) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt_insert = $conexion->prepare($insertar_usuario);
    $stmt_insert->bind_param("ssssss", $nombre, $apellido, $email, $clave_encriptada, $fecha_nac, $telefono);

    // 7. Ejecutar y redireccionar
    if ($stmt_insert->execute()) {
        // Registro exitoso: Avisa al usuario y lo manda directo a iniciar sesión
        echo "<script>
                alert('¡Tu cuenta se creó con éxito! Bienvenido a Nana Mimus.');
                window.location.href = 'inicio_sesion.html';
              </script>";
    } else {
        echo "<script>
                alert('Hubo un problema al guardar tu usuario: " . $stmt_insert->error . "');
                window.history.back();
              </script>";
    }

    $stmt_insert->close();
}

$conexion->close();
?>