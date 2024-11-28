<?php
session_start();
if (!isset($_SESSION['admin']) || !isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

include('../../conexion.php');

$usuario_id = $_SESSION['user_id'];

// Verificar si se ha enviado el formulario para eliminar un animal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $animal_id = $_POST['delete_id'];

    // Obtener los datos del animal antes de eliminarlo para registrar en la bitácora
    $query_animal = "SELECT nombre, tipo, estado, especialidad FROM animal WHERE animal_id = $animal_id";
    $result_animal = mysqli_query($con, $query_animal);

    if ($result_animal && mysqli_num_rows($result_animal) > 0) {
        $animal = mysqli_fetch_assoc($result_animal);
        $nombre = $animal['nombre'];
        $tipo = $animal['tipo'];
        $estado = $animal['estado'];
        $especialidad = $animal['especialidad'];

        // Registrar la eliminación en la bitácora
        $accion = 'Eliminar';
        $entidad = 'animal';
        $descripcion = "Se eliminó el animal con ID: $animal_id, Nombre: $nombre, Tipo: $tipo, Estado: $estado, Especialidad: $especialidad";

        // Insertar en la bitácora con el `usuario_id` actual
        $query_bitacora = "INSERT INTO bitacora (accion, entidad, fecha_hora, descripcion, Admin_admin_id) 
                           VALUES ('$accion', '$entidad', NOW(), '$descripcion', 
                           (SELECT admin_id FROM admin WHERE User_usuario_id = $usuario_id LIMIT 1))";
        mysqli_query($con, $query_bitacora);

        // Eliminar el animal de la base de datos
        $query_delete_animal = "DELETE FROM animal WHERE animal_id = $animal_id";
        mysqli_query($con, $query_delete_animal);

        echo "<script>alert('Animal eliminado exitosamente'); window.location.href='animal.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: No se pudo encontrar el animal para eliminar');</script>";
    }
}

// Consulta para obtener la información de los animales
$query_animales = "SELECT animal_id, nombre, tipo, estado, especialidad, foto_animal FROM animal ORDER BY animal_id DESC";
$result_animales = mysqli_query($con, $query_animales);
if (!$result_animales) {
    die("Error en la consulta de animales: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Animales</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

    <div class="container mt-5">
        <h1 class="text-center">Lista de Animales</h1>
        <a href="createAnimal.php" class="btn btn-primary mb-3">
            <img src="agregaruser.png" alt="" class="icono-btn">
        </a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Especialidad</th>
                    <th>Foto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row_animal = mysqli_fetch_assoc($result_animales)): ?>
                    <tr>
                        <td><?php echo $row_animal['animal_id']; ?></td>
                        <td><?php echo $row_animal['nombre']; ?></td>
                        <td><?php echo $row_animal['tipo']; ?></td>
                        <td><?php echo $row_animal['estado']; ?></td>
                        <td><?php echo $row_animal['especialidad']; ?></td>
                        <td>
                            <?php if ($row_animal['foto_animal']): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($row_animal['foto_animal']); ?>" alt="Foto del animal" width="50">
                            <?php else: ?>
                                Sin foto
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="updateAnimal.php?id=<?php echo $row_animal['animal_id']; ?>" class="btn btn-warning btn-sm">
                                <img src="editar.png" alt="" class="icono-btn">
                            </a>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $row_animal['animal_id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este animal?');">
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
</body>
</html>
