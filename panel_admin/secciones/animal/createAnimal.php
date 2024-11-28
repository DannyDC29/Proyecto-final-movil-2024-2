<?php
session_start();

// Verificar si el usuario está logueado (administrador)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../../conexion.php');

$usuario_id = $_SESSION['user_id'];  // Obtener el `usuario_id` de la sesión

// Verificar si el usuario actual es un administrador y obtener el `admin_id` correspondiente
$query_admin_id = "SELECT admin_id FROM admin WHERE User_usuario_id = $usuario_id";
$result_admin_id = mysqli_query($con, $query_admin_id);
$admin_row = mysqli_fetch_assoc($result_admin_id);

if (!$admin_row) {
    die("Error: El usuario actual no tiene permisos de administrador.");
}

$admin_id = $admin_row['admin_id'];  // Obtener el admin_id

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];
    $especialidad = $_POST['especialidad'];

    // Manejo del archivo de imagen
    $foto_animal = null;
    if (isset($_FILES['foto_animal']) && $_FILES['foto_animal']['error'] === UPLOAD_ERR_OK) {
        $foto_animal = addslashes(file_get_contents($_FILES['foto_animal']['tmp_name']));
    }

    // Inserción en la tabla `animal` con la foto incluida
    $query_insert_animal = "INSERT INTO animal (nombre, tipo, estado, especialidad, foto_animal) 
                            VALUES ('$nombre', '$tipo', '$estado', '$especialidad', '$foto_animal')";
    $result_insert_animal = mysqli_query($con, $query_insert_animal);

    if ($result_insert_animal) {
        $animal_id = mysqli_insert_id($con); // Obtener el ID del animal insertado

        // Registro de la acción en la tabla `bitacora`
        $accion = 'Insertar';
        $entidad = 'animal';
        $descripcion = "Se agregó un nuevo animal con ID: $animal_id, Nombre: $nombre";

        $query_insert_bitacora = "INSERT INTO bitacora (accion, entidad, fecha_hora, descripcion, Admin_admin_id) 
                                  VALUES ('$accion', '$entidad', NOW(), '$descripcion', $admin_id)";
        mysqli_query($con, $query_insert_bitacora);

        echo "<script>alert('Animal agregado exitosamente'); window.location.href='animal.php';</script>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al agregar el animal: " . mysqli_error($con) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Animal</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            margin-top: 50px;
            max-width: 600px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #1b185c;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            text-align: center;
        }

        .btn-primary {
            background-color: #1b185c;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-primary:hover {
            background-color: #17b09e;
            transform: scale(1.05);
        }

        /* Botón regresar con fondo blanco y borde */
        .btn-regresar {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            font-size: 16px;
            color: #1b185c;
            background-color: #1b185c;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            border: 2px solid;
            transition: background-color 0.3s, color 0.3s;
            margin-right: 10px;
        }

        .btn-regresar img {
            width: 20px;
            height: 20px;
            margin-right: 8px;
        }

        .btn-regresar:hover {
            background-color: #17b09e;
            color: #17b09e;
        }

        .form-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="../../index1.php">
            <img src="logoat.jpg" alt="Logo">
        </a>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">Agregar Nuevo Animal</div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data"> <!-- Agregado enctype para permitir archivos -->
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo:</label>
                        <select class="form-control" id="tipo" name="tipo" required>
                            <option value="">-- Sin asignar --</option>
                            <option value="perro">Perro</option>
                            <option value="gato">Gato</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="disponible">Disponible</option>
                            <option value="asignado">Asignado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="especialidad">Especialidad:</label>
                        <select class="form-control" id="especialidad" name="especialidad" required>
                            <option value="">-- Sin asignar --</option>
                            <option value="depresión">Depresión</option>
                            <option value="estrés">Estrés</option>
                            <option value="ansiedad">Ansiedad</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="foto_animal">Foto del Animal:</label>
                        <input type="file" class="form-control-file" id="foto_animal" name="foto_animal"
                            accept="image/*">
                    </div>

                    <div class="form-buttons">
                        <a href="animal.php" class="btn-regresar">
                            <img src="volver.png" alt="">
                        </a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>