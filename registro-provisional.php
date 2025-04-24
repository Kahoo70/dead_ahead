<?php

    // include "conect/conection.php";
session_start();
require_once('conex/conex.php');
$conex = new Database;
$con = $conex->conectar();
$estado = 2;
?>

<?php

if (isset($_POST['Send'])){
    $doc = $_POST['documento'];
    $user = $_POST['nombre'];
    $pass = $_POST['contrasena'];
    $id_user = $_POST['id_roles'];
   
 

    echo $user,"\n",$pass;

    $pass_enc = password_hash($pass, PASSWORD_DEFAULT, array("contrasena" => 12));


    $sql = $con -> prepare("SELECT * FROM usuarios WHERE documento = '$doc'");
    $sql -> execute();

    $fila = $sql -> fetchAll(PDO::FETCH_ASSOC);
    if($fila){
        echo'<script> alert ("El Usuario Ya Se Encuentra Registrado") </script>';
        echo '<script> window.location = "register.php" </script>';
    }
    if ($doc == "" || $user == "" || $pass == "" || $id_user == ""){
        echo'<script> alert ("Los Campos Se Encuentran Vacios") </script>';
        echo '<script> window.location = "register.php" </script>';
    }

    $insert = $con ->  prepare("INSERT INTO usuarios(documento, nombre, contrasena, id_roles) VALUES ('$doc','$user', '$pass_enc', '$id_user')");
    $insert -> execute();

    echo '<script> alert ("Save Registrer") </script>';
    echo '<script> window.location = "login.php" </script>';

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style3.css">
    <!-- <link rel="stylesheet" href="js/js.css"> -->

    <title>Login Z</title>
</head>
<body>
    <!-- <h1>
    <?php
    // echo $num1;

    ?>
    </h1>

    <h2>
        <?php
        // echo "Is Nice!"
        ?>
    </h2>
     -->

    <!-- quiere direccinar algo para otro archivo -->
    <!-- multipart/form-data sirve para capturar imagenes -->
    <div class="container">
        <h1>Register For Zeth</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="documento">Document</label>
            <input type="text" name="documento" id="documento" value="" placeholder="Insert Your Document">

            <label for="nombre">User</label>
            <input type="text" name="nombre" id="nombre" value="" placeholder="Insert Your Username">
            
            <label for="contrasena">Password</label>
            <input type="password" name="contrasena" id="contrasena" value="" placeholder="Insert Your Password">



            <label for="id_roles">Roles</label>
            
            <select name="id_roles" id="id_roles">
                <option value="">Seleccione el rol</option>

                <?php
                    $sql = $con -> prepare("SELECT * FROM roles where id_roles >= 1");
                    $sql -> execute();
                    // FETCH() SOLO PARA CUANDO ES UN REGISTRO
                    // pg_fetch_all() SIRVE PARA MUCHOS REGISTROS
                    while ($fila = $sql -> fetch(PDO::FETCH_ASSOC))
                    {
                        echo "<option value =" . $fila['id_roles'] .">" .$fila['nom_roles'] . "</option>";
                    }

                ?>

            </select>

            <label for="Submit"></label>
            <input type="Submit" value="guardar" name="Send">
    
            </form>
    </div></body>
</html>
