<?php
session_start();
if (!isset($_SESSION['admin']) && !isset($_SESSION['especialista'])) {
    header('Location: ../../index.php');
    exit();
}

// Obtener datos del cliente desde los parámetros de la URL
$nombre = $_GET['nombre'];
$correo = $_GET['correo'];
$password = $_GET['password'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Cliente</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, whitesmoke, #17b09e);
            font-family: 'Roboto', sans-serif;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .confirmation-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .confirmation-container h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1b185c;
        }

        .confirmation-container p {
            font-size: 1.2rem;
            color: #555;
            margin-top: 1rem;
        }

        .btn-primary {
            background-color: #1b185c;
            border: none;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.3s;
            margin-top: 1.5rem;
            color: #fff;
            text-decoration: none;
        }

        .btn-primary:hover {
            background-color: #17b09e;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <h1>¡Bienvenido, <?php echo htmlspecialchars($nombre); ?>!</h1>
        <p>Sus credenciales de acceso son:</p>
        <p><strong>Correo:</strong> <?php echo htmlspecialchars($correo); ?></p>
        <p><strong>Contraseña:</strong> <?php echo htmlspecialchars($password); ?></p>
        <a href="verClientes.php" class="btn-primary">Volver a Clientes</a>
    </div>
</body>
</html>
