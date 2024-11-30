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
    body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
}

    .form-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    margin: auto;
    padding: 20px;
}

h1 {
    text-align: center;
    color: #333;
}

label {
    display: block;
    margin-bottom: 5px;
    color: #555;
}

input[type="text"],
input[type="number"],
select,
textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
button {
    width: 242px;
    padding: 10px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    margin-top: 20px;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

button:hover {
    background-color: #218838;

}


p {
    color: red;
    text-align: center;
    margin-top: 10px;
}

textarea {
    resize: none;
}

.logo {
    width: 100px;
    height: auto;
    position: absolute;
    top: 10px;
    right: 10px;
}
    .button-s {
        background-color: #ddd;
        color: black;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 20px;
        display: block;
        width: 218px;
        margin-left: auto;
        margin-right: auto;
    }

    .button-s:hover {
        background-color: #bbb;
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        margin-top: 100px;
    }

    .logo {
        width: 100px;
        height: auto;
        position: absolute;
        top: 10px;
        right: 10px;
    }

    input[type="file"] {
        display: none;
    }

    .custom-file-upload {
        display: center;
        padding: 10px;
        cursor: pointer;
        background-color: #28a745;
        color: white;
        border-radius: 8px;
        text-align: center;
        transition: background-color 0.3s, transform 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin: 10px 0;
        width: 120%;
        max-width: 220px;
        margin-left: auto;
        margin-right: auto;
    }
    .custom-file-upload:hover {
        background-color: #218838;
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
    }

    .custom-file-upload:active {
        background-color: #1e7e34;
        transform: scale(0.98);
        box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
    }
    </style>
</head>

<body>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $host = 'localhost';
        $user = 'feriasof1_grupo3';
        $password = 'RY9jaepMgPmTP6gEQKbM';
        $dbname = 'feriasof1_grupo3';

        $conn = new mysqli($host, $user, $password, $dbname);

        if ($conn->connect_error) {
            die("<p style='color: red;'>Error de conexión: " . $conn->connect_error . "</p>");
        }

        $nombre = $conn->real_escape_string($_POST['nombre_proveedor']);
        $id = $conn->real_escape_string($_POST['id']);
        $email = $conn->real_escape_string($_POST['email']);
        $direccion = $conn->real_escape_string($_POST['direccion']);
        $telefono = $conn->real_escape_string($_POST['telefono']);

        $check_sql = "SELECT * FROM proveedores WHERE id = '$id'";
        $check_result = $conn->query($check_sql);

        if (!$check_result) {
            die("<p style='color: red;'>Error al verificar el ID del proveedor: " . $conn->error . "</p>");
        }

        if ($check_result->num_rows > 0) {
            echo "<p style='color: red;'>El proveedor con el ID '$id' ya existe.</p>";
        } else {
            $sql = "INSERT INTO proveedores (nombre, id, email, direccion, telefono) 
                    VALUES ('$nombre', '$id', '$email', '$direccion', '$telefono')";

            if ($conn->query($sql) === TRUE) {
                echo "<p style='color: green;'>Proveedor agregado exitosamente.</p>";
                header("Location: Proveedores.php");
                exit();
            } else {
                echo "<p style='color: red;'>Error al insertar proveedor: " . $conn->error . "</p>";
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
            <button class="button-s" onclick="location.href='Proveedores.php'">Volver atras</button>
        </div>
    </form>
</body>

</html>
