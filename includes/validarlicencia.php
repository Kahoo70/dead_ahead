<?php
session_start();
require_once('../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

// Verificar si el usuario está logueado
if (!isset($_SESSION['documento'])) {
    header("Location: ../index.php"); // Redirige a la página de inicio de sesión
    exit();
}

// Obtener el ID de la empresa del usuario logueado
$id_usuario = $_SESSION['documento'];
$sql_usuario = $con->prepare("SELECT id_empresa FROM usuarios WHERE documento = :id_usuario");
$sql_usuario->bindParam(':id_usuario', $id_usuario);
$sql_usuario->execute();
$usuario = $sql_usuario->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    // Si no se encuentra el usuario, cerrar sesión
    session_destroy();
    header("Location: ../index.php");
    exit();
}

$id_empresa = $usuario['id_empresa'];

// Verificar si existe una adquisición caducada para la empresa
$sql_licencia = $con->prepare("SELECT id_estado_licencia 
                               FROM adquisicion 
                               WHERE id_empresa = :id_empresa 
                               AND id_estado_licencia = 3 
                               ORDER BY fecha_fin DESC LIMIT 1");
$sql_licencia->bindParam(':id_empresa', $id_empresa);
$sql_licencia->execute();
$licencia = $sql_licencia->fetch(PDO::FETCH_ASSOC);

if ($licencia) {
    // Si existe una licencia caducada (id_estado_licencia = 3), cerrar sesión
    session_destroy();


    echo "<script>alert('Su licencia ha caducado.');</script>";
    echo  "<script>window.location = '../index.php';</script>";
    exit();
}

// Si todo está bien, continuar con la ejecución
?>