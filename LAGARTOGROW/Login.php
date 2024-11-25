<?php
// Inicia la respuesta JSON solo si es una solicitud AJAX de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
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

    // Recibe las credenciales del usuario
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Consulta para verificar el usuario
    $sql = "SELECT contraseña FROM usuarios WHERE nombre = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verifica la contraseña
        if (password_verify($password, $row['contraseña'])) {
            echo json_encode(["status" => "success", "message" => "Inicio de sesión exitoso"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Contraseña incorrecta"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Usuario no encontrado"]);
    }

    $conn->close();
    exit;
}
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
            <form id="loginForm" onsubmit="validateLogin(event)">
                <input type="text" id="username" name="username" placeholder="Usuario" required>
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
                <button type="submit">Iniciar sesión</button>
            </form>
        </div>
    </div>

    <script>
        function validateLogin(event) {
            event.preventDefault();

            const formData = new FormData(document.getElementById('loginForm'));

            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    location.href = '../LAGARTOGROW/src/index.html';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error al iniciar sesión:', error));
        }
    </script>
</body>
</html>
