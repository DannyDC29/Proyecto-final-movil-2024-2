<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ../../index.php');
    exit();
}
include ('../../conexion.php');

$id = $_GET['id'];
$mensaje = '';

// Capturar el `usuario_id` del administrador logueado
$usuario_id = $_SESSION['user_id'];

// Verificar si el usuario actual es un administrador y obtener el `admin_id` correspondiente
$query_admin_id = "SELECT admin_id FROM admin WHERE User_usuario_id = $usuario_id";
$result_admin_id = mysqli_query($con, $query_admin_id);
$admin_row = mysqli_fetch_assoc($result_admin_id);

if (!$admin_row) {
    die("Error: El usuario actual no tiene permisos de administrador.");
}

$admin_id = $admin_row['admin_id'];  // Obtener el admin_id

// Obtener datos actuales del animal antes de la actualización
$query_animal = "SELECT * FROM animal WHERE animal_id = $id";
$result_animal = mysqli_query($con, $query_animal);
if (!$result_animal) {
    die("Error en la consulta del animal: " . mysqli_error($con));
}

$animal = mysqli_fetch_assoc($result_animal);

// Variables para guardar los valores originales
$original_nombre = mysqli_real_escape_string($con, $animal['nombre']);
$original_tipo = mysqli_real_escape_string($con, $animal['tipo']);
$original_estado = mysqli_real_escape_string($con, $animal['estado']);
$original_especialidad = mysqli_real_escape_string($con, $animal['especialidad']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $tipo = mysqli_real_escape_string($con, $_POST['tipo']);
    $estado = mysqli_real_escape_string($con, $_POST['estado']);
    $especialidad = mysqli_real_escape_string($con, $_POST['especialidad']);

    // Manejo del archivo de imagen
    $foto_actualizada = false;
    if (isset($_FILES['foto_animal']) && $_FILES['foto_animal']['error'] === UPLOAD_ERR_OK) {
        $foto_animal = addslashes(file_get_contents($_FILES['foto_animal']['tmp_name']));
        $foto_actualizada = true;
    }

    // Construir la consulta de actualización
    $query_update_animal = "UPDATE animal SET nombre='$nombre', tipo='$tipo', estado='$estado', especialidad='$especialidad'";
    if ($foto_actualizada) {
        $query_update_animal .= ", foto_animal='$foto_animal'";
    }
    $query_update_animal .= " WHERE animal_id=$id";

    $result_update_animal = mysqli_query($con, $query_update_animal);

    if ($result_update_animal) {
        // Crear una descripción de los cambios
        $descripcion = "Se actualizó el animal con ID: $id";

        if ($nombre !== $original_nombre) {
            $descripcion .= ", Nombre anterior: '$original_nombre' nuevo Nombre: '$nombre'";
        }
        if ($tipo !== $original_tipo) {
            $descripcion .= ", Tipo anterior: '$original_tipo' nuevo Tipo: '$tipo'";
        }
        if ($estado !== $original_estado) {
            $descripcion .= ", Estado anterior: '$original_estado' nuevo Estado: '$estado'";
        }
        if ($especialidad !== $original_especialidad) {
            $descripcion .= ", Especialidad anterior: '$original_especialidad' nuevo Especialidad: '$especialidad'";
        }
        if ($foto_actualizada) {
            $descripcion .= ", Foto actualizada";
        }

        // Escapar la descripción para evitar problemas de sintaxis
        $descripcion = mysqli_real_escape_string($con, $descripcion);

        // Registrar la acción de actualización en la tabla `bitacora`
        $accion = 'Actualizar';
        $entidad = 'animal';

        $query_insert_bitacora = "INSERT INTO bitacora (accion, entidad, fecha_hora, descripcion, Admin_admin_id) 
                                  VALUES ('$accion', '$entidad', NOW(), '$descripcion', $admin_id)";
        mysqli_query($con, $query_insert_bitacora);

        echo "<script>alert('Animal actualizado exitosamente'); window.location.href='animal.php';</script>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al actualizar el animal: " . mysqli_error($con) . "</div>";
    }
}
?>

<!-- Continuación del HTML de la página -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Animal</title>
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
            border: 2px solid ;
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
            <img src="logoat.jpg" alt="Logo"> <!-- Cambia "logoat.jpg" por la ruta de tu logo -->
        </a>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">Editar animal</div>
            <div class="card-body">
        <?php if ($mensaje) echo $mensaje; ?>
        <form method="post" enctype="multipart/form-data"> <!-- Agregado enctype para permitir archivos -->
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $animal['nombre']; ?>" required>
            </div>
            <div class="form-group">
                <label for="tipo">Tipo:</label>
                <select class="form-control" id="tipo" name="tipo" required>
                    <option value="perro" <?php if ($animal['tipo'] == 'perro') echo 'selected'; ?>>Perro</option>
                    <option value="gato" <?php if ($animal['tipo'] == 'gato') echo 'selected'; ?>>Gato</option>
                </select>
            </div>
            <div class="form-group">
                <label for="estado">Estado:</label>
                <select class="form-control" id="estado" name="estado" required>
                    <option value="disponible" <?php if ($animal['estado'] == 'disponible') echo 'selected'; ?>>Disponible</option>
                    <option value="asignado" <?php if ($animal['estado'] == 'asignado') echo 'selected'; ?>>Asignado</option>
                </select>
            </div>
            <div class="form-group">
                <label for="especialidad">Especialidad:</label>
                <select class="form-control" id="especialidad" name="especialidad" required>
                    <option value="depresión" <?php if ($animal['especialidad'] == 'depresión') echo 'selected'; ?>>Depresión</option>
                    <option value="estrés" <?php if ($animal['especialidad'] == 'estrés') echo 'selected'; ?>>Estrés</option>
                    <option value="ansiedad" <?php if ($animal['especialidad'] == 'ansiedad') echo 'selected'; ?>>Ansiedad</option>
                </select>
            </div>
            <div class="form-group">
                <label for="foto_animal">Foto del Animal:</label>
                <input type="file" class="form-control-file" id="foto_animal" name="foto_animal" accept="image/*">
                <?php if ($animal['foto_animal']): ?>
                    <p>Imagen actual:</p>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($animal['foto_animal']); ?>" style="width: 100px; height: auto;">
                <?php endif; ?>
            </div>

            <div class="form-buttons">
                        <a href="animal.php" class="btn-regresar">
                            <img src="volver.png" alt=""> 
                        </a>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <br><br><br>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
