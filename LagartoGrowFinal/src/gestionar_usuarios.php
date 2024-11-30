<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$user = 'feriasof1_grupo3';
$password = 'RY9jaepMgPmTP6gEQKbM';
$dbname = 'feriasof1_grupo3';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$mensaje_error = "";
$pagina_anterior = $_SERVER['HTTP_REFERER'] ?? 'gestionar_usuarios.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $email = $conn->real_escape_string($_POST['email']);
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
    $rango = $conn->real_escape_string($_POST['rango']);
    $proveedor_id = ($rango === 'Proveedor' && !empty($_POST['proveedor'])) ? $conn->real_escape_string($_POST['proveedor']) : null;

    if (empty($nombre) || empty($email) || empty($contraseña) || empty($rango)) {
        $mensaje_error = "Completa todos los campos obligatorios.";
    } else {
        $sql_verificar = "SELECT id FROM usuarios WHERE email = '$email'";
        $result_verificar = $conn->query($sql_verificar);

        if ($result_verificar && $result_verificar->num_rows > 0) {
            $mensaje_error = "El email ya está registrado.";
        } else {
            $sql_insertar = "
                INSERT INTO usuarios (nombre, email, contraseña, rango, proveedor_id) 
                VALUES ('$nombre', '$email', '$contraseña', '$rango', " . ($proveedor_id ? "'$proveedor_id'" : "NULL") . ")";
            if ($conn->query($sql_insertar)) {
                echo "<script>alert('Usuario agregado con éxito'); window.location.href = '$pagina_anterior';</script>";
                exit;
            } else {
                $mensaje_error = "Error al agregar el usuario: " . $conn->error;
            }
        }
    }
}

$sql_proveedores = "SELECT id, nombre FROM proveedores WHERE id NOT IN (SELECT proveedor_id FROM usuarios)";
$result_proveedores = $conn->query($sql_proveedores);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Usuario</title>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rangoField = document.querySelector('select[name="rango"]');
            const proveedorField = document.querySelector('select[name="proveedor"]').parentElement;
            rangoField.addEventListener('change', function () {
                proveedorField.style.display = this.value === 'Proveedor' ? 'block' : 'none';
            });
            proveedorField.style.display = rangoField.value === 'Proveedor' ? 'block' : 'none';
        });
    </script>
</head>
<body>
    <h1>Agregar Usuario</h1>
    <form method="POST" action="">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Contraseña:</label>
        <input type="password" name="contraseña" required>
        <label>Rango:</label>
        <select name="rango" required>
            <option value="">Seleccione un rango</option>
            <option value="Jefe">Jefe</option>
            <option value="Proveedor">Proveedor</option>
        </select>
        <div>
            <label>Proveedor:</label>
            <select name="proveedor">
                <option value="">Seleccione un proveedor</option>
                <?php while ($row = $result_proveedores->fetch_assoc()) { ?>
                    <option value="<?= $row['id']; ?>"><?= $row['nombre']; ?></option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" name="agregar">Agregar Usuario</button>
    </form>
    <?php if (!empty($mensaje_error)): ?>
        <p style="color: red;"><?= $mensaje_error; ?></p>
    <?php endif; ?>
</body>
</html>

<?php $conn->close(); ?>






