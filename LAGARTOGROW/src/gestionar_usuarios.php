<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rango'] === 'proveedor') {
    echo '<p style="color: red; font-size: 20px; text-align: center;">Acceso denegado. No tienes permisos para acceder a esta página.</p>';
    exit(); 
}
?>
<?php
$host = 'localhost';
$user = 'root'; 
$password = '';
$dbname = 'lagartogrow_db';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'], $_POST['email'], $_POST['contraseña'], $_POST['rango'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $email = $conn->real_escape_string($_POST['email']);
    $contraseña = password_hash($conn->real_escape_string($_POST['contraseña']), PASSWORD_DEFAULT);
    $rango = $conn->real_escape_string($_POST['rango']);

    $query = "INSERT INTO usuarios (nombre, email, contraseña, rango) VALUES ('$nombre', '$email', '$contraseña', '$rango')";
    if ($conn->query($query) === TRUE) {
        header("Location: usuarios.php");
        exit();
    } else {
        echo "<script>alert('Error al agregar usuario: " . $conn->error . "'); window.location.href='usuarios.php';</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id = intval($_POST['eliminar_id']);
    $sql = "DELETE FROM usuarios WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: usuarios.php");
        exit();
    } else {
        echo "<script>alert('Error al eliminar usuario: " . $conn->error . "'); window.location.href='usuarios.php';</script>";
    }
}

$conn->close();
?>
