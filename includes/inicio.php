<?php

    // include "conect/conection.php";
session_start();
require_once('../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

 

// LA CONDICION CON EL ISSET ESCUCHA LAS VARIABLES ENVIADAS Y LAS PONE EN VARIABLES PARA GUARDARLAS
if (isset($_POST['enviar'])){
    $doc = ($_POST['documento']);
    $pass = ($_POST['contrasena']);


    // echo "$doc \n";
    // echo "$user \n";
   

// EN CASO DE QUE ESTEN VACIOS LOS CAMPOS INGRESADOS
if ( $doc == "" || $pass == "" ) {
    echo '<script> alert ("Los campos estan vacios") </script>';
    echo '<script> window.location = "../login.php" </script>';
    exit;
}

// SE DESCRIPTA LA CONTRASEÑA
    $pass_descr = htmlentities(addslashes($pass));

    // SE HACE LA VALIDACION SI EL USUARIO O EL DOCUMENTO YA ESTAN REGISTRADOS EN LA BASE DE DATOS
    $sql = $con -> prepare("SELECT * FROM usuarios WHERE documento = '$doc'");
    $sql -> execute();

    // TOMA LOS DATOS
    $fila = $sql -> fetch();

    // HACE UNA COMPARACION SI LA CONTRASEÑA QUE INGRESE ES IGUAL A LA DE LA BASE DE DATOS
    if ($fila && password_verify($pass_descr, $fila['contrasena']))
    {
        // EN CASO DE SER VERDAD
$_SESSION['documento'] = $fila['documento'];
$_SESSION['id_roles'] = $fila['id_roles'];
$_SESSION['id_empresa'] = $fila['id_empresa'];    

echo $_SESSION['documento'], $_SESSION['id_roles'], $_SESSION['id_empresa'];

if ($_SESSION['id_roles'] == 1){
    header("location: ../admin/index-admin.php");
    exit();
}

if ($_SESSION['id_roles'] == 2){
    header("location: ../super-admin/index-super-admin.php");
    exit();
}


    }
    else{
        // SI NO ES CIERTO
        echo '<script> alert ("Los datos ingresados son incorrectos") </script>';
        echo '<script> window.location = "../login.php" </script>';
    }

}


?>