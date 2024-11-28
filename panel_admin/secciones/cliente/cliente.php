<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../../index.php");
    exit();
}

include('../../conexion.php');

// Verificar si se ha enviado el formulario de eliminación
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $usuario_id = $_POST['delete_id'];

    // Obtener información del cliente antes de eliminarlo (para la bitácora)
    $query_cliente_info = "
        SELECT u.nombre, u.apellido, u.correo, c.direccion, c.telefono, c.Preferencia_animal, c.diagnostico 
        FROM user u 
        INNER JOIN cliente c ON u.usuario_id = c.User_usuario_id 
        WHERE u.usuario_id = $usuario_id";
    $result_cliente_info = mysqli_query($con, $query_cliente_info);

    if ($result_cliente_info && mysqli_num_rows($result_cliente_info) > 0) {
        $cliente_info = mysqli_fetch_assoc($result_cliente_info);

        // Eliminar primero el cliente asociado al usuario en la tabla `cliente`
        $query_delete_cliente = "DELETE FROM cliente WHERE User_usuario_id = $usuario_id";
        mysqli_query($con, $query_delete_cliente);

        // Después, eliminar el usuario de la tabla `user`
        $query_delete_user = "DELETE FROM user WHERE usuario_id = $usuario_id";
        $result_delete_user = mysqli_query($con, $query_delete_user);

        if ($result_delete_user) {
            // Registrar la acción en la bitácora
            $admin_user_id = $_SESSION['user_id']; // ID del administrador actual
            $descripcion = "Se eliminó el cliente con ID: $usuario_id, Nombre: {$cliente_info['nombre']} {$cliente_info['apellido']}, Correo: {$cliente_info['correo']}, Dirección: {$cliente_info['direccion']}, Teléfono: {$cliente_info['telefono']}, Preferencia Animal: {$cliente_info['Preferencia_animal']}, Diagnóstico: {$cliente_info['diagnostico']}.";
            $query_insert_bitacora = "INSERT INTO bitacora (accion, entidad, fecha_hora, descripcion, Admin_admin_id) 
                                      VALUES ('Eliminar', 'cliente', NOW(), '$descripcion', 
                                      (SELECT admin_id FROM admin WHERE User_usuario_id = $admin_user_id))";
            mysqli_query($con, $query_insert_bitacora);

            echo "<script>alert('Cliente eliminado exitosamente'); window.location.href='cliente.php';</script>";
        } else {
            echo "<script>alert('Error al eliminar el cliente: " . mysqli_error($con) . "');</script>";
        }
    } else {
        echo "<script>alert('Cliente no encontrado. No se puede eliminar.');</script>";
    }
}

// Consulta para obtener solo la información de los clientes
$query_clientes = "
    SELECT u.usuario_id, u.nombre, u.apellido, u.correo, u.contrasena,
           c.direccion, c.telefono, c.Preferencia_animal, c.diagnostico
    FROM user u
    INNER JOIN cliente c ON u.usuario_id = c.User_usuario_id
    ORDER BY c.User_usuario_id DESC";

$result_clientes = mysqli_query($con, $query_clientes);
if (!$result_clientes) {
    die("Error en la consulta de clientes: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Clientes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, whitesmoke, #17b09e);
            font-family: 'Roboto', sans-serif;
            color: #333;
        }

        /* Barra de navegación */
        .navbar {
            background-color: white;
            padding: 1rem 2rem;
        }

        .navbar-brand img {
            width: 100px;
        }

        .navbar-nav .nav-link {
            color: #1b185c !important;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            transition: color 0.3s, background-color 0.3s;
            border-radius: 8px;
        }

        .navbar-nav .nav-link:hover {
            background-color: #17b09e !important;
            color: #fff !important;
        }

        /* Contenedor principal */
        .container {
            margin-top: 40px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1b185c;
            text-align: center;
            margin-bottom: 30px;
        }

        /* Estilo de la tabla */
        .table {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background-color: #1b185c;
            color: white;
            font-weight: bold;
        }

        .table td, .table th {
            text-align: center;
            vertical-align: middle;
        }

        /* Botones */
        .btn-primary {
            background-color: #1b185c;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-primary:hover {
            background-color: #1b185c;
            transform: scale(1.05);
        }

        .icono-btn {
            width: 25px;  
            height: 25px;
            margin-right: 10px; 
            vertical-align: middle; 
        }

        .btn-warning {
            background-color: white;
            color: #333;
            font-weight: bold;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-warning:hover {
            background-color: #1b185c;
        }

        .btn-danger {
            background-color: white;
            color: white;
            font-weight: bold;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Botón regresar */
        .btn-regresar {
            display: inline-flex;
            align-items: center;
            padding: 5px 15px;
            font-size: 14px;
            color: #fff;
            background-color: #1b185c;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .btn-regresar img {
            width: 16px;
            height: 16px;
            margin-right: 5px;
        }

        .btn-regresar:hover {
            background-color: #17b09e;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="../../index1.php">
            <img src="logoat.jpg" alt="">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="../../index1.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="../../secciones/user/user.php">Usuarios</a></li>
                <li class="nav-item"><a class="nav-link" href="../../secciones/cliente/cliente.php">Clientes</a></li>
                <li class="nav-item"><a class="nav-link" href="../../secciones/animal/animal.php">Animales</a></li>
                <li class="nav-item"><a class="nav-link" href="../../secciones/terapia/terapia.php">Terapias</a></li>
                <li class="nav-item"><a class="nav-link" href="../../secciones/seguimiento/seguimiento.php">Seguimientos</a></li>
                <li class="nav-item"><a class="nav-link" href="../../secciones/bitacora/bitacora.php">Bitácora</a></li>
                <li class="nav-item"><a class="nav-link" href="../../logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container">
        <h1 class="text-center">Lista de pacientes</h1>
        <a href="createCliente.php" class="btn btn-primary mb-3">
            <img src="agregaruser.png" alt="" class="icono-btn">
        </a>
        </a>        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th>Contraseña</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Preferencia Animal</th>
                    <th>Diagnóstico</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row_cliente = mysqli_fetch_assoc($result_clientes)): ?>
                    <tr>
                        <td><?php echo $row_cliente['usuario_id']; ?></td>
                        <td><?php echo $row_cliente['nombre']; ?></td>
                        <td><?php echo $row_cliente['apellido']; ?></td>
                        <td><?php echo $row_cliente['correo']; ?></td>
                        <td>
                            <input type="password" class="form-control-plaintext" style="display:inline-block; width:80%;" value="<?php echo $row_cliente['contrasena']; ?>" readonly>
                            <button onclick="togglePassword(this)" class="btn btn-sm btn-secondary">👁️</button>
                        </td>
                        <td><?php echo $row_cliente['direccion']; ?></td>
                        <td><?php echo $row_cliente['telefono']; ?></td>
                        <td><?php echo $row_cliente['Preferencia_animal']; ?></td>
                        <td><?php echo $row_cliente['diagnostico']; ?></td>
                        <td>
                            <a href="updateCliente.php?id=<?php echo $row_cliente['usuario_id']; ?>" class="btn btn-warning btn-sm">
                                <img src="editar.png" alt="" class="icono-btn"> 
                            </a>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $row_cliente['usuario_id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este cliente?');">
                                    <img src="borrar.png" alt="" class="icono-btn"> 
                                </button>                            
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Función para alternar la visibilidad de la contraseña
        function togglePassword(button) {
            const input = button.previousElementSibling;
            if (input.type === "password") {
                input.type = "text";
                button.innerText = "🙈";
            } else {
                input.type = "password";
                button.innerText = "👁️";
            }
        }
    </script>
</body>
</html>
