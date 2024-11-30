<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rango'] === 'proveedor') {
    echo '<script type="text/javascript">
            alert("Acceso denegado. No tienes permisos para acceder a esta página.");
            window.history.back();
          </script>';
    exit();
}

$host = 'localhost';
$user = 'feriasof1_grupo3';
$password = 'RY9jaepMgPmTP6gEQKbM';
$dbname = 'feriasof1_grupo3';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("<script>alert('Error al conectar a la base de datos: " . $conn->connect_error . "');</script>");
}

$conn->set_charset("utf8");

$mensaje_error = "";

$pagina_anterior = $_SERVER['HTTP_REFERER'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar']) && $_POST['agregar'] == '1') {
        try {
            if (empty($_POST['nombre']) || empty($_POST['email']) || empty($_POST['contraseña']) || empty($_POST['rango'])) {
                throw new Exception('Por favor, completa todos los campos obligatorios.');
            }

            $nombre = trim($conn->real_escape_string($_POST['nombre']));
            $email = trim($conn->real_escape_string($_POST['email']));
            $contraseña = password_hash(trim($_POST['contraseña']), PASSWORD_DEFAULT);
            $rango = trim($conn->real_escape_string($_POST['rango']));
            $proveedor_id = null;

            if ($rango === 'Proveedor') {
                if (empty($_POST['proveedor'])) {
                    throw new Exception('Debes seleccionar un proveedor para este usuario.');
                }
                $proveedor_id = trim($conn->real_escape_string($_POST['proveedor']));

                $query_verificar_proveedor = "SELECT id FROM usuarios WHERE proveedor_id = '$proveedor_id'";
                $resultado_proveedor = $conn->query($query_verificar_proveedor);

                if (!$resultado_proveedor) {
                    throw new Exception("Error en la consulta de verificación: " . $conn->error);
                }

                if ($resultado_proveedor->num_rows > 0) {
                    throw new Exception("El proveedor seleccionado ya tiene un usuario asignado.");
                }
            }

            $query_verificar_email = "SELECT id FROM usuarios WHERE email = '$email'";
            $resultado_email = $conn->query($query_verificar_email);

            if (!$resultado_email) {
                throw new Exception("Error en la consulta de verificación: " . $conn->error);
            }

            if ($resultado_email->num_rows > 0) {
                throw new Exception("Ya existe un usuario con este correo electrónico.");
            }

            $query_insertar = "
                INSERT INTO usuarios (nombre, email, contraseña, rango, proveedor_id) 
                VALUES ('$nombre', '$email', '$contraseña', '$rango', " . ($proveedor_id ? "'$proveedor_id'" : "NULL") . ")
            ";

            if (!$conn->query($query_insertar)) {
                throw new Exception("Error al insertar usuario: " . $conn->error);
            }

            echo "<script>alert('Usuario agregado con éxito'); window.location.href = '$pagina_anterior';</script>";
        } catch (Exception $e) {
            $mensaje_error = $e->getMessage();
        }
    }
}

$sql_usuarios = "SELECT u.id, u.nombre, u.email, u.rango, p.nombre AS proveedor_nombre
                 FROM usuarios u 
                 LEFT JOIN proveedores p ON u.proveedor_id = p.id";
$result_usuarios = $conn->query($sql_usuarios);

$sql_proveedores = "SELECT id, nombre FROM proveedores WHERE id NOT IN (SELECT proveedor_id FROM usuarios WHERE proveedor_id IS NOT NULL)";
$result_proveedores = $conn->query($sql_proveedores);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: auto; }
        form, table { width: 100%; margin-bottom: 20px; }
        label { font-weight: bold; }
        input, button, select { width: 100%; padding: 10px; margin-top: 10px; border-radius: 5px; border: 1px solid #ccc; }
        button { background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        table { border-collapse: collapse; margin-top: 30px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gestión de Usuarios</h2>
        <?php if (!empty($mensaje_error)) echo "<p style='color: red; font-size: 18px; text-align: center;'>$mensaje_error</p>"; ?>

        <form id="usuarioForm" method="POST" action="">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="contraseña">Contraseña:</label>
            <input type="password" id="contraseña" name="contraseña" required>
            <label for="rango">Rango:</label>
            <select name="rango" id="rango" required>
                <option value="">Seleccionar</option>
                <option value="Jefe">Jefe</option>
                <option value="Proveedor">Proveedor</option>
            </select>

            <div id="proveedor_div" style="display:none;">
                <label for="proveedor">Proveedor:</label>
                <select name="proveedor" id="proveedor">
                    <option value="">Seleccionar</option>
                    <?php while ($row = $result_proveedores->fetch_assoc()) { ?>
                        <option value="<?= $row['id']; ?>"><?= $row['nombre']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" name="agregar" value="1">Agregar Usuario</button>
            <button type="submit" name="agregar" onclick="window.history.back()">Volver atras</button>
        </form>

        <h3>Usuarios Registrados</h3>
<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rango</th>
            <th>Proveedor</th>
        
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result_usuarios->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['nombre']; ?></td>
                <td><?= $row['email']; ?></td>
                <td><?= $row['rango']; ?></td>
                <td><?= $row['proveedor_nombre'] ?? 'N/A'; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
    </div>

    <script>
        document.getElementById('rango').addEventListener('change', function() {
            var proveedorDiv = document.getElementById('proveedor_div');
            proveedorDiv.style.display = (this.value === 'Proveedor') ? 'block' : 'none';
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>










