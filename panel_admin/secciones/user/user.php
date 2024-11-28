<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../../index.php");
    exit();
}

include('../../conexion.php');

// Obtener el rol seleccionado del filtro (si existe)
$rol_filtro = isset($_GET['rol']) ? $_GET['rol'] : '';

// Eliminar usuario si se envía el formulario de eliminación
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Obtener datos del usuario antes de eliminarlo para la bitácora
    $query_user_info = "SELECT nombre, apellido, correo FROM user WHERE usuario_id = $delete_id";
    $result_user_info = mysqli_query($con, $query_user_info);
    $user_info = mysqli_fetch_assoc($result_user_info);

    // Paso 1: Eliminar las terapias relacionadas con el especialista antes de eliminar al especialista
    $query_delete_terapias = "DELETE FROM terapia WHERE Especialista_especialista_id = (SELECT especialista_id FROM especialista WHERE User_usuario_id = $delete_id)";
    mysqli_query($con, $query_delete_terapias);

    // Paso 2: Eliminar cualquier rol asociado en tablas relacionadas antes de eliminar el usuario
    $query_delete_admin = "DELETE FROM admin WHERE User_usuario_id = $delete_id";
    mysqli_query($con, $query_delete_admin);

    $query_delete_especialista = "DELETE FROM especialista WHERE User_usuario_id = $delete_id";
    mysqli_query($con, $query_delete_especialista);

    // Paso 3: Ahora eliminar el usuario de la tabla `user`
    $query_delete_user = "DELETE FROM user WHERE usuario_id = $delete_id";
    $result_delete_user = mysqli_query($con, $query_delete_user);

    if ($result_delete_user) {
        // Registrar en la bitácora
        $accion = 'Eliminar';
        $entidad = 'user';
        $descripcion = "Se eliminó al usuario con ID: $delete_id, Nombre: {$user_info['nombre']} {$user_info['apellido']}, Correo: {$user_info['correo']}";

        // Obtener el ID del administrador que realiza la acción
        $admin_user_id = $_SESSION['user_id'];
        $query_insert_bitacora = "INSERT INTO bitacora (accion, entidad, fecha_hora, descripcion, Admin_admin_id) 
                                  VALUES ('$accion', '$entidad', NOW(), '$descripcion', 
                                  (SELECT admin_id FROM admin WHERE User_usuario_id = $admin_user_id))";
        mysqli_query($con, $query_insert_bitacora);

        echo "<script>alert('Usuario eliminado exitosamente'); window.location.href='user.php';</script>";
    } else {
        die("Error al eliminar el usuario: " . mysqli_error($con));
    }
}

// Consulta para obtener usuarios con los roles de administrador y especialista concatenados, excluyendo a los clientes
$query_usuarios = "
    SELECT u.usuario_id, u.nombre, u.apellido, u.correo, u.contrasena,
           CASE
               WHEN a.admin_id IS NOT NULL AND e.especialista_id IS NOT NULL THEN 'Admin, Especialista'
               WHEN a.admin_id IS NOT NULL THEN 'Admin'
               WHEN e.especialista_id IS NOT NULL THEN 'Especialista'
               ELSE 'No asignado'
           END AS rol
    FROM user u
    LEFT JOIN admin a ON u.usuario_id = a.User_usuario_id
    LEFT JOIN especialista e ON u.usuario_id = e.User_usuario_id
    LEFT JOIN cliente c ON u.usuario_id = c.User_usuario_id
    WHERE c.cliente_id IS NULL";

// Agregar filtro de rol en la consulta si está seleccionado
if ($rol_filtro == 'Admin') {
    $query_usuarios .= " AND a.admin_id IS NOT NULL";
} elseif ($rol_filtro == 'Especialista') {
    $query_usuarios .= " AND e.especialista_id IS NOT NULL";
} elseif ($rol_filtro == 'No asignado') {
    $query_usuarios .= " AND a.admin_id IS NULL AND e.especialista_id IS NULL";
}

// Agregar ordenamiento
$query_usuarios .= " ORDER BY u.usuario_id DESC";

// Ejecutar la consulta
$result_usuarios = mysqli_query($con, $query_usuarios);
if (!$result_usuarios) {
    die("Error en la consulta de usuarios: " . mysqli_error($con));
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, whitesmoke, #17b09e);
            font-family: 'Roboto', sans-serif;
            color: #333;
        }

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

        .form-inline label {
            font-weight: bold;
            color: #333;
        }

        .table {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
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

        .action-btn {
            border: none;
            background: none;
            cursor: pointer;
            padding: 0;
            margin: 0;
        }

        .action-icon {
            width: 20px;
            height: 20px;
            vertical-align: middle;
        }

        .filter-button {
            background-color: #1b185c;
            border: none;
            padding: 10px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-button:hover {
            background-color: #17b09e;
        }

        .add-button {
            background-color: #1b185c;
            border: none;
            padding: 10px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-button:hover {
            background-color: #17b09e;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="../../index1.php">
            <img src="logoat.jpg" alt="Animal Therapy Logo">
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

    <div class="container">
        <h1>Lista de Usuarios</h1>
        
        <div class="text-right mb-3">
            <form method="GET" action="user.php" class="form-inline">
                <label for="rol" class="mr-2">Filtrar por rol:</label>
                <select name="rol" id="rol" class="form-control mr-2">
                    <option value="">Todos</option>
                    <option value="Admin" <?php if ($rol_filtro == 'Admin') echo 'selected'; ?>>Admin</option>
                    <option value="Especialista" <?php if ($rol_filtro == 'Especialista') echo 'selected'; ?>>Especialista</option>
                    <option value="No asignado" <?php if ($rol_filtro == 'No asignado') echo 'selected'; ?>>No asignado</option>
                </select>
                <button type="submit" class="filter-button">
                    <img src="buscar.png" alt="Buscar" class="action-icon">
                </button>
            </form>
        </div>

        <a href="createUser.php" class="add-button mb-3">
            <img src="agregaruser.png" alt="Agregar Usuario" class="action-icon">
        </a>
        <br><br>
        

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th>Contraseña</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row_usuario = mysqli_fetch_assoc($result_usuarios)): ?>
                    <tr>
                        <td><?php echo $row_usuario['usuario_id']; ?></td>
                        <td><?php echo $row_usuario['nombre']; ?></td>
                        <td><?php echo $row_usuario['apellido']; ?></td>
                        <td><?php echo $row_usuario['correo']; ?></td>
                        <td>
                            <input type="password" class="form-control-plaintext" style="display:inline-block; width:80%;" value="<?php echo $row_usuario['contrasena']; ?>" readonly>
                            <button onclick="togglePassword(this)" class="btn btn-sm btn-secondary">👁️</button>
                        </td>
                        <td><?php echo $row_usuario['rol']; ?></td>
                        <td>
                            <a href="updateUser.php?id=<?php echo $row_usuario['usuario_id']; ?>" class="action-btn">
                                <img src="editar.png" alt="Editar" class="action-icon">
                            </a>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $row_usuario['usuario_id']; ?>">
                                <button type="submit" class="action-btn" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                    <img src="borrar.png" alt="Eliminar" class="action-icon">
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
