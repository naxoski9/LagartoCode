<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Lagarto Grow</title>
    <link rel="stylesheet" href="../css/seguimiento.css">
<style>
    .product-list {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }

    .card {
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 10px;
        width: 300px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        margin-bottom: 15px;
    }

    .card h2 {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .card p {
        font-size: 16px;
        margin-bottom: 5px;
    }

    .button-group button {
        margin-right: 10px;
    }

    .botongestionar {
        padding: 10px;
        background-color: #4caf50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .botongestionar:hover {
        background-color: #45a049;
    }
    
    </style>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="user-section">
                <span class="user-icon">👤</span>
                <p>Admin</p>
            </div>
            <nav class="menu">
                <ul>
                    <li><button onclick="location.href='inventario.php'">📦 Inventario</button></li>
                    <li><button onclick="location.href='proveedores.php'">🛒 Proveedores</button></li>
                    <li><button onclick="location.href='seguimiento.php'">📊 Seguimiento</button></li>
                    <li><button onclick="location.href='boleta.php'">🧾 Historial ventas</button></li>
                </ul>
            </nav>
            <button class="logout-button" onclick="location.href=c">Cerrar Sesión</button>
            <button class="botongestionar" onclick="location.href='usuarios.php'">Gestionar Usuarios</button>

        </aside>

        <main class="main-content">
            <header class="header">
                <img src="../img/lagarto.jpg" alt="Lagarto Grow Logo" class="logo">
                <h1>Sistema Lagarto Grow</h1>
            </header>

            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Buscar" onkeyup="filterCards()">
                <button onclick="filterCards()">Buscar</button>
            </div>

            <div class="button-group">
                <button onclick="location.href='Agregar_Producto.php'">Agregar producto</button>
                <button onclick="location.href='Editar_Producto.php'">Editar</button>
                <button onclick="location.href='Eliminar_Producto.php'">Eliminar</button>
                <button onclick="location.href='Ofertas.php'">Ofertas y Promociones</button>
                <button onclick="sortProductsByPrice()">Ordenar por precio (Mayor a menor)</button>
            </div>

            <div class="product-list" id="productList">
                <?php
                function formatPrice($price) {
                    return '$' . number_format((float)$price, 0, ',', '.');
                }

                function createCard($row) {
                    $card = '<div class="card" data-name="' . strtolower($row["nombre_producto"]) . '">';
                    $card .= '<h2>' . $row["nombre_producto"] . '</h2>';
                    $card .= '<p><strong>Código:</strong> ' . $row["codigo"] . '</p>';
                    $card .= '<p><strong>Stock:</strong> ' . $row["stock"] . '</p>';
                    $card .= '<p><strong>Precio de venta:</strong> ' . formatPrice($row["precio"]) . '</p>';
                    $card .= '<p><strong>Estado:</strong> ' . $row["estado"] . '</p>';
                    $card .= '<p><strong>Proveedor:</strong> ' . $row["nombre_proveedor"] . '</p>';
                    $card .= '<p><strong>Descripcion:</strong> ' . $row["descripcion"] . '</p>';

                    if (!empty($row["imagen_url"])) {
                        $card .= '<img src="' . $row["imagen_url"] . '" alt="' . $row["nombre_producto"] . '">';
                    } else {
                        $card .= '<p>Sin imagen disponible</p>';
                    }

                    $card .= '</div>';
                    return $card;
                }

                $host = 'localhost';
                $db = 'lagartogrow_db'; 
                $user = 'root';
                $password = ''; 
                $conn = new mysqli($host, $user, $password, $db);

                if ($conn->connect_error) {
                    die("Error de conexión: " . $conn->connect_error);
                }

                $sql = "
                    SELECT p.stock, p.descripcion, p.codigo, p.precio, p.nombre_producto, p.estado, p.imagen_url, pr.nombre AS nombre_proveedor 
                    FROM producto p 
                    JOIN proveedores pr ON p.proveedor_id = pr.id
                ";
                $result = $conn->query($sql);
                $products = [];

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $products[] = $row;
                    }
                } else {
                    echo '<p>No hay productos disponibles.</p>';
                }

                foreach (array_slice($products, 0, 3) as $row) {
                    echo createCard($row);
                }

                $conn->close();
                ?>
            </div>
        </main>
    </div>

    <script>
    const allProducts = <?php echo json_encode($products); ?>;

    function formatPrice(price) {
        return '$' + Math.floor(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function filterCards() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const productList = document.getElementById('productList');
        productList.innerHTML = '';

        const filteredProducts = allProducts.filter(product =>
            product.nombre_producto.toLowerCase().includes(filter)
        );

        if (filteredProducts.length > 0) {
            filteredProducts.slice(0, 3).forEach(product => {
                const card = document.createElement('div');
                card.className = 'card';
                card.setAttribute('data-name', product.nombre_producto.toLowerCase());
                card.innerHTML = `
                        <h2>${product.nombre_producto}</h2>
                        <p><strong>Código:</strong> ${product.codigo}</p>
                        <p><strong>Stock:</strong> ${product.stock}</p>
                        <p><strong>Precio de venta:</strong> ${formatPrice(product.precio)}</p>
                        <p><strong>Estado:</strong> ${product.estado}</p>
                        <p><strong>Proveedor:</strong> ${product.nombre_proveedor}</p>
                        <p><strong>Descripcion:</strong> ${product.descripcion}</p>
                        ${product.imagen_url ? `<img src="${product.imagen_url}" alt="${product.nombre_producto}">` : '<p>Sin imagen disponible</p>'}
                    `;
                productList.appendChild(card);
            });
        } else {
            productList.innerHTML = '<p>No se encontraron resultados.</p>';
        }
    }

    function sortProductsByPrice() {
        const productList = document.getElementById('productList');
        productList.innerHTML = '';

        const sortedProducts = allProducts.sort((a, b) => b.precio - a.precio);

        sortedProducts.slice(0, 3).forEach(product => {
            const card = document.createElement('div');
            card.className = 'card';
            card.setAttribute('data-name', product.nombre_producto.toLowerCase());
            card.innerHTML = `
                    <h2>${product.nombre_producto}</h2>
                    <p><strong>Código:</strong> ${product.codigo}</p>
                    <p><strong>Stock:</strong> ${product.stock}</p>
                    <p><strong>Precio de venta:</strong> ${formatPrice(product.precio)}</p>
                    <p><strong>Estado:</strong> ${product.estado}</p>
                    <p><strong>Proveedor:</strong> ${product.nombre_proveedor}</p>
                    <p><strong>Descripcion:</strong> ${product.descripcion}</p>
                    ${product.imagen_url ? `<img src="${product.imagen_url}" alt="${product.nombre_producto}">` : '<p>Sin imagen disponible</p>'}
                `;
            productList.appendChild(card);
        });
    }
    </script>
</body>

</html>