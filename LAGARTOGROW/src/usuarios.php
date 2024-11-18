<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rango'] === 'proveedor') {
    echo '<script type="text/javascript">
            alert("Acceso denegado. No tienes permisos para acceder a esta página.");
            window.history.back(); // Esto hace que el usuario vuelva a la página anterior.
          </script>';
    exit(); 
}

$host = 'localhost';
$user = 'root';
$password = ''; 
$dbname = 'lagartogrow_db';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$mensaje_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar'])) {
        if (isset($_POST['nombre'], $_POST['email'], $_POST['contraseña'], $_POST['rango'])) {
            $nombre = $conn->real_escape_string($_POST['nombre']);
            $email = $conn->real_escape_string($_POST['email']);
            $contraseña = password_hash($conn->real_escape_string($_POST['contraseña']), PASSWORD_DEFAULT);
            $rango = $conn->real_escape_string($_POST['rango']);
            $proveedor_id = $rango === 'Proveedor' && isset($_POST['proveedor']) ? $conn->real_escape_string($_POST['proveedor']) : null;
            $sql_email_check = "SELECT * FROM usuarios WHERE email = '$email'";
            $result_email_check = $conn->query($sql_email_check);

            if ($result_email_check->num_rows > 0) {
                $mensaje_error = "Error: Ya existe un usuario con este email.";
            } else {
                $query = "INSERT INTO usuarios (nombre, email, contraseña, rango, proveedor_id) 
                          VALUES ('$nombre', '$email', '$contraseña', '$rango', " . ($proveedor_id ? "'$proveedor_id'" : "NULL") . ")";
                
                if ($conn->query($query) === TRUE) {
                    echo "<script>alert('Usuario agregado con éxito'); window.location.href = 'usuarios.php';</script>";
                } else {
                    echo "<script>alert('Error al agregar usuario: " . $conn->error . "');</script>";
                }
            }
        } else {
            echo "<script>alert('Por favor, complete todos los campos requeridos');</script>";
        }
    }

    if (isset($_POST['eliminar_id'])) {
        $eliminar_id = $conn->real_escape_string($_POST['eliminar_id']);
        $sql_delete = "DELETE FROM usuarios WHERE id = '$eliminar_id'";

        if ($conn->query($sql_delete) === TRUE) {
            echo "<script>alert('Usuario eliminado con éxito'); window.location.href = 'usuarios.php';</script>";
        } else {
            echo "<script>alert('Error al eliminar usuario: " . $conn->error . "');</script>";
        }
    }
}

$sql_proveedores = "
    SELECT p.id, p.nombre 
    FROM proveedores p 
    LEFT JOIN usuarios u ON p.id = u.proveedor_id
    WHERE u.proveedor_id IS NULL
";
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
        table { border-collapse: collapse; }
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
            <select id="rango" name="rango" required>
                <option value="Jefe">Jefe</option>
                <option value="Usuario">Usuario</option>
                <option value="Proveedor">Proveedor</option>
            </select>
            <div id="proveedorSection" style="display: none;">
                <label for="proveedor">Seleccionar Proveedor:</label>
                <select id="proveedor" name="proveedor">
                    <option value="">Seleccione un proveedor</option>
                    <?php if ($result_proveedores->num_rows > 0) {
                        while ($row = $result_proveedores->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                        }
                    } else {
                        echo "<option value=''>No hay proveedores disponibles</option>";
                    } ?>
                </select>
            </div>
            <button type="submit" name="agregar">Agregar Usuario</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rango</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT id, nombre, email, rango, fecha_creacion FROM usuarios";
                $result = $conn->query($sql);
                if ($result === false) {
                    echo "<tr><td colspan='6'>Error en la consulta: " . $conn->error . "</td></tr>";
                } elseif ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nombre']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['rango']}</td>
                            <td>{$row['fecha_creacion']}</td>
                            <td>
                                <form method='POST' action='' style='display:inline'>
                                    <input type='hidden' name='eliminar_id' value='{$row['id']}' />
                                    <button type='submit'>Eliminar</button>
                                </form>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay usuarios registrados</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById('rango').addEventListener('change', function() {
            var proveedorSection = document.getElementById('proveedorSection');
            proveedorSection.style.display = this.value === 'Proveedor' ? 'block' : 'none';
        });

        document.getElementById('proveedor').addEventListener('change', function() {
            var proveedorId = this.value;

            if (proveedorId) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'obtener_proveedor.php?id=' + proveedorId, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var proveedor = JSON.parse(xhr.responseText);
                        document.getElementById('nombre').value = proveedor.nombre;
                        document.getElementById('email').value = proveedor.email;
                    }
                };
                xhr.send();
            } else {
                document.getElementById('nombre').value = '';
                document.getElementById('email').value = '';
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>
