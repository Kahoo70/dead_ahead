<?php
session_start();
require_once('conex/conex.php');
$conex = new Database;
$con = $conex->conectar();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dead Ahead</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div id="container">
        <!-- Imagen dentro del cuadro de login -->
        <div class="logo">
            <img src="img/logo_zwarfareZ.png" alt="Logo" width="200px">
        </div>
        <!-- Formulario de login -->
        <form action="includes/inicio.php" method="POST">
            <h1>Iniciar Sesión</h1>
            <input type="text" name="documento" id="documento" placeholder="Documento" required>
            <input type="password" name="contrasena" id="contrasena" placeholder="Contraseña" required>
            <button type="submit" name="enviar">Ingresar</button>
        </form>
    </div>
</body>
</html>