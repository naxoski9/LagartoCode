<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emitir Boleta/Factura</title>
    <link rel="stylesheet" href="../css/emitir_boleta.css">
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
            margin: 20px;
        }

        h1 {
            text-align: center;
        }

        form {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:disabled {
            background-color: #ddd;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>

<body>
    <h1>Emitir Boleta/Factura</h1>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $host = 'localhost';
        $db = 'lagartogrow_db';
        $user = 'root';
        $password = '';

        // Conexión a la base de datos
        $conn = new mysqli($host, $user, $password, $db);

        // Verificar conexión
        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }

        // Recibir datos del formulario
        $cliente = $_POST['cliente'];
        $nmro_documento = $_POST['nmro_documento'];
        $direccion = $_POST['direccion'];
        $metodo_pago = $_POST['metodo_pago'];
        $fecha_emision = $_POST['fecha_emision'];
        $codigo = $_POST['codigo_producto']; // Cambiado para que coincida con el nombre del input
        $cantidad = $_POST['cantidad'];
        $precio_total = $_POST['precio_total'];

        // Verificar si el cliente existe
        $check_cliente = "SELECT id FROM cliente WHERE nmro_documento = '$nmro_documento'";
        $result_cliente = $conn->query($check_cliente);

        if ($result_cliente->num_rows == 0) {
            // Insertar cliente si no existe
            $sql_insert_cliente = "INSERT INTO cliente (nombre, nmro_documento, direccion, metodo_pago)
                                   VALUES ('$cliente', '$nmro_documento', '$direccion', '$metodo_pago')";

            if ($conn->query($sql_insert_cliente) === TRUE) {
                $cliente_id = $conn->insert_id; // Obtener el ID del nuevo cliente
                echo "<p class='success'>Cliente registrado con éxito.</p>";
            } else {
                echo "<p class='error'>Error al registrar el cliente: " . $conn->error . "</p>";
            }
        } else {
            // Obtener el ID del cliente existente
            $cliente_data = $result_cliente->fetch_assoc();
            $cliente_id = $cliente_data['id'];
        }

        // Verificar si el producto existe
        $check_producto = "SELECT id, stock FROM producto WHERE codigo = '$codigo'";
        $result_producto = $conn->query($check_producto);

        if ($result_producto->num_rows > 0) {
            $producto = $result_producto->fetch_assoc();
            $producto_id = $producto['id'];
            $stock_actual = $producto['stock'];

            if ($stock_actual >= $cantidad) {
                // Insertar el pedido
                $sql_pedido = "INSERT INTO pedidos (cliente_id, fecha, codigo, cantidad, precio_total)
                               VALUES ('$cliente_id', '$fecha_emision', '$codigo', '$cantidad', '$precio_total')";

                if ($conn->query($sql_pedido) === TRUE) {
                    // Actualizar el stock del producto
                    $nuevo_stock = $stock_actual - $cantidad;
                    $sql_actualizar_stock = "UPDATE producto SET stock = '$nuevo_stock' WHERE codigo= '$codigo'";
                    $conn->query($sql_actualizar_stock);

                    echo "<p class='success'>Boleta emitida y stock actualizado correctamente.</p>";
                } else {
                    echo "<p class='error'>Error al emitir la boleta: " . $conn->error . "</p>";
                }
            } else {
                echo "<p class='error'>Stock insuficiente. Solo quedan $stock_actual unidades.</p>";
            }
        } else {
            echo "<p class='error'>Error: Producto con código '$codigo' no encontrado.</p>";
        }

        // Cerrar conexión
        $conn->close();
    }
    ?>

    <form action="" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
        <label for="cliente">Nombre del cliente:</label>
        <input type="text" id="cliente" name="cliente" required>

        <label for="nmro_documento">Número de documento:</label>
        <input type="text" id="nmro_documento" name="nmro_documento" required>

        <label for="direccion">Dirección del cliente:</label>
        <input type="text" id="direccion" name="direccion" required>

        <label for="metodo_pago">Método de pago:</label>
        <select id="metodo_pago" name="metodo_pago" required>
            <option value="efectivo">Efectivo</option>
            <option value="tarjeta">Tarjeta</option>
            <option value="transferencia">Transferencia</option>
        </select>

        <label for="fecha_emision">Fecha de emisión:</label>
        <input type="date" id="fecha_emision" name="fecha_emision" required>

        <label for="codigo_producto">Código del producto:</label>
        <input type="text" id="codigo_producto" name="codigo_producto" required>

        <label for="cantidad">Cantidad:</label>
        <input type="number" id="cantidad" name="cantidad" required>

        <label for="precio_total">Precio total:</label>
        <input type="number" id="precio_total" name="precio_total" step="0.01" required>

        <button type="submit">Emitir Boleta</button>
    </form>
</body>

</html>
