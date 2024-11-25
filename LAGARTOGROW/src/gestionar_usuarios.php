<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lagartogrow_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la conexión
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Error de conexión a la base de datos"]));
}

// Manejo de solicitudes POST y GET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nombre'], $_POST['email'], $_POST['contraseña'])) {
        // Agregar un nuevo usuario
        $nombre = $conn->real_escape_string($_POST['nombre']);
        $email = $conn->real_escape_string($_POST['email']);
        $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO usuarios (nombre, email, contraseña, fecha_creacion) VALUES ('$nombre', '$email', '$contraseña', NOW())";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Usuario agregado exitosamente"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al agregar el usuario"]);
        }
    } elseif (isset($_POST['eliminar_id'])) {
        // Eliminar un usuario
        $eliminar_id = (int)$_POST['eliminar_id'];
        $sql = "DELETE FROM usuarios WHERE id = $eliminar_id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Usuario eliminado exitosamente"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al eliminar el usuario"]);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener la lista de usuarios
    $sql = "SELECT id, nombre, email, fecha_creacion FROM usuarios ORDER BY id DESC";
    $result = $conn->query($sql);
    $usuarios = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }
    echo json_encode($usuarios);
}

// Cierra la conexión
$conn->close();
?>

