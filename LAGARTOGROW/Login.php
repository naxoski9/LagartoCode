<?php
session_start();

$host = 'localhost';
$user = 'root'; 
$password = '';
$dbname = 'lagartogrow_db';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$usuario_default = "profesora";
$contraseña_default = "USM2024";
$rango_default = "jefe";

$query = "SELECT * FROM usuarios WHERE nombre = '$usuario_default'";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    $contraseña_encriptada = password_hash($contraseña_default, PASSWORD_DEFAULT);
    $insert_query = "INSERT INTO usuarios (nombre, contraseña, rango) 
                     VALUES ('$usuario_default', '$contraseña_encriptada', '$rango_default')";

    if ($conn->query($insert_query) === TRUE) {
        echo "Usuario 'profesora' agregado con éxito.";
    } else {
        echo "Error al agregar el usuario 'profesora': " . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT id, nombre, contraseña, rango FROM usuarios WHERE nombre = '$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['contraseña'])) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario_nombre'] = $row['nombre'];
            $_SESSION['usuario_rango'] = $row['rango'];

            if ($row['rango'] === 'jefe' || $row['rango'] === 'usuario') {
                header("Location: ../LAGARTOGROW/src/index.html");
            } else if ($row['rango'] === 'proveedor') {
                header("Location: ../LAGARTOGROW/src/seguimiento.php");
            }
        } else {
            echo "<script>alert('Contraseña incorrecta');</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Lagarto Grow</title>
    <link rel="stylesheet" href="../LAGARTOGROW/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Ingreso Sistema</h2>
            <form action="login.php" method="POST">
                <input type="text" name="username" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit">Iniciar sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
