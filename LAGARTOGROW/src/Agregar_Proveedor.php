<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Proveedor</title>
    <link rel="stylesheet" href="../css/AgregarProveedores.css">
    <img src="../img/lagarto.jpg" alt="Lagarto Grow Logo" class="logo">
    <style>
    .logo {
        width: 100px;
        height: auto;
        position: absolute;
        top: 10px;
        right: 10px;
    }
    </style>

</head>

<body>


    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $host = 'localhost';
        $db = 'lagartogrow_db';
        $user = 'root';
        $password = '';

        $conn = new mysqli($host, $user, $password, $db);

        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }

        $nombre = $_POST['nombre_proveedor'];
        $id = $_POST['id'];
        $email = $_POST['email'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];

    
        $check_sql = "SELECT * FROM proveedores WHERE id = '$id'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            echo "<p style='color: red;'>El proveedor con el id '$id' ya existe.</p>";
        } else {
           
            $sql = "INSERT INTO proveedores (nombre, id, email, direccion, telefono) 
                    VALUES ('$nombre', '$id', '$email', '$direccion', '$telefono')";

            if ($conn->query($sql) === TRUE) {
                echo "<p style='color: green;'>Proveedor agregado exitosamente.</p>";
               
                header("Location: Proveedores.php");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

    
        $conn->close();
    }
    ?>
    <form action="" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
        <label for="nombre">Nombre del proveedor:</label>
        <input type="text" id="nombre" name="nombre_proveedor" required><br><br>

        <label for="id">Id del proveedor:</label>
        <input type="number" id="id" name="id" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="telefono">Teléfono:</label>
        <input type="number" id="telefono" name="telefono" required><br><br>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion" required><br><br>

        <button type="submit">Agregar Proveedor</button>
        <div>
            <button class="button-s" onclick="location.href='inventario.php'">Volver al Inventario</button>
        </div>

    </form>

</body>

</html>