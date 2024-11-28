<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ../../index.php');
    exit();
}
include ('../../conexion.php');

$mensaje = '';

// Obtener el usuario actual (quien está creando el nuevo usuario)
$usuario_id_actual = $_SESSION['user_id']; // Asegúrate de que esta variable esté configurada al iniciar sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $roles = isset($_POST['roles']) ? $_POST['roles'] : [];

    // Encriptar la contraseña
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Inserción en la tabla `user`
    $query_insert_user = "INSERT INTO user (nombre, apellido, correo, contrasena) VALUES ('$nombre', '$apellido', '$correo', '$password_hash')";
    $result_insert_user = mysqli_query($con, $query_insert_user);
    
    if ($result_insert_user) {
        $usuario_id = mysqli_insert_id($con);

        if (in_array('Admin', $roles)) {
            $query_insert_admin = "INSERT INTO admin (User_usuario_id) VALUES ('$usuario_id')";
            mysqli_query($con, $query_insert_admin);
        }
        if (in_array('Especialista', $roles)) {
            $query_insert_especialista = "INSERT INTO especialista (User_usuario_id) VALUES ('$usuario_id')";
            mysqli_query($con, $query_insert_especialista);
        }

        // Registrar en la bitácora
        $accion = 'Insertar';
        $entidad = 'user';
        $descripcion = "Se creó un nuevo usuario con ID: $usuario_id, Nombre: $nombre $apellido, Correo: $correo";

        $query_insert_bitacora = "INSERT INTO bitacora (accion, entidad, fecha_hora, descripcion, Admin_admin_id) 
                                  VALUES ('$accion', '$entidad', NOW(), '$descripcion', 
                                  (SELECT admin_id FROM admin WHERE User_usuario_id = $usuario_id_actual))";
        mysqli_query($con, $query_insert_bitacora);

        echo "<script>alert('Usuario agregado exitosamente'); window.location.href='user.php';</script>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al agregar el usuario: " . mysqli_error($con) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
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

        /* Botón regresar con imagen */
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
            transition: background-color 0.3s;
            margin-right: 10px;
        }

        .btn-regresar img {
            width: 20px;
            height: 20px;
            margin-right: 8px;
        }

        .btn-regresar:hover {
            background-color: #17b09e;
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
            <div class="card-header">Agregar Usuario</div>
            <div class="card-body">
                <?php if ($mensaje) echo $mensaje; ?>
                <form method="post">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido:</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" required>
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo:</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">👁️</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Asignar Rol(es):</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="Admin" id="roleAdmin">
                            <label class="form-check-label" for="roleAdmin">Admin</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="Especialista" id="roleEspecialista">
                            <label class="form-check-label" for="roleEspecialista">Especialista</label>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <a href="user.php" class="btn-regresar">
                            <img src="volver.png" alt=""> 
                        </a>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const button = passwordField.nextElementSibling.firstElementChild;
            if (passwordField.type === "password") {
                passwordField.type = "text";
                button.innerText = "🙈";
            } else {
                passwordField.type = "password";
                button.innerText = "👁️";
            }
        }
    </script>
</body>
</html>
