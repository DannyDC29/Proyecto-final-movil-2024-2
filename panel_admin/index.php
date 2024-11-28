<?php
include('conexion.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // Escapar las variables para prevenir inyección SQL
    $correo = mysqli_real_escape_string($con, $correo);

    // Consulta para obtener los datos del usuario, incluyendo su rol
    $query_user = "
        SELECT u.usuario_id, u.contrasena, 
               CASE 
                   WHEN a.admin_id IS NOT NULL THEN 'Administrador' 
                   WHEN e.especialista_id IS NOT NULL THEN 'Especialista' 
                   ELSE NULL 
               END AS rol
        FROM user u
        LEFT JOIN admin a ON u.usuario_id = a.User_usuario_id
        LEFT JOIN especialista e ON u.usuario_id = e.User_usuario_id
        WHERE u.correo = '$correo'
    ";

    $result_user = mysqli_query($con, $query_user);

    if ($result_user && mysqli_num_rows($result_user) == 1) {
        $row = mysqli_fetch_assoc($result_user);
        $hashed_password = $row['contrasena'];
        $rol = $row['rol'];
        $usuario_id = $row['usuario_id'];

        // Verificar la contraseña ingresada con el hash almacenado
        if (password_verify($password, $hashed_password)) {
            // Configurar las variables de sesión según el rol
            if ($rol === 'Administrador') {
                $_SESSION['admin'] = $correo;
                $_SESSION['user_id'] = $usuario_id; // Guardar el ID del usuario en la sesión
                header('Location: index1.php');
            } elseif ($rol === 'Especialista') {
                $_SESSION['especialista'] = $correo;
                $_SESSION['user_id'] = $usuario_id; // Guardar el ID del usuario en la sesión
                header('Location: index2.php');
            }
            exit();
        } else {
            $mensaje = "<div class='alert alert-danger'>Correo o contraseña incorrectos</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger'>Correo o contraseña incorrectos</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #fff, #17b09e);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            color: #333;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .logo {
            width: 220px;
            margin-bottom: 20px;
        }

        .form-control {
            background-color: transparent;
            color: #333;
            border: none;
            border-bottom: 2px solid #17b09e; 
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px; 
        }

        .form-control:focus {
            background-color: transparent;
            border-bottom: 2px solid #0056b3;
            box-shadow: none;
            color: #333;
        }

        .btn-primary {
            background-color: #17b09e;
            border: none;
            width: 100%;
            border-radius: 8px;
            padding: 12px;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-primary:hover {
            background-color: #1b185c ;
            transform: scale(1.02);
        }

        .toggle-password {
            background: none;
            border: #17b09e;
            color: black;
            cursor: pointer;
            font-size: 1.2em;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: #007bff;
        }

        .input-group-append {
            display: flex;
            align-items: center;
        }

        label {
            color: black;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <div class="login-container">
        <img src="logoat.jpg" alt="Animal Therapy Logo" class="logo">
        <?php if (isset($mensaje)) echo $mensaje; ?>
        <form method="post" id="loginForm">
            <div class="form-group">
                <input type="text" class="form-control" id="correo" name="correo" placeholder="Correo" required>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                    <div class="input-group-append">
                        <button type="button" class="toggle-password" onclick="togglePassword(this)">👁️</button>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
    </div>

    <script>
        // Función para mostrar u ocultar la contraseña
        function togglePassword(button) {
            const passwordField = document.getElementById('password');
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
