<!DOCTYPE html>
<?php
    include './libreria.php';

    $codigo = $_POST['txtcodigo'];
    $usuario = $_POST['txtusuario'];
    $dni = $_POST['txtdni'];
    $clave = $_POST['txtclave'];
    $latitud = $_POST['txtlatitud'];
    $longitud = $_POST['txtlongitud'];
    //echo $codigo." ".$usuario." ".$dni." ".$clave." ".$latitud." ".$longitud;

    if (($codigo == '') or ($codigo == 0)) {
        if (($usuario != '') and ($usuario != 0) and ($dni != '') and ($latitud != '') and ($longitud != '')) {
            $sql = "INSERT INTO personas(usuario, dni, clave, latitud, longitud)
                VALUES ('$usuario', '$dni', '$clave', $latitud, $longitud);";
        } else {
            echo "Error: No se puede insertar o actualizar un registro con datos faltantes.";
            die();
        }
    } else {
        if (($usuario != '') and ($usuario != 0) and ($dni != '') and ($latitud != '') and ($longitud != '')) {
            $sql = "UPDATE personas SET usuario='$usuario', dni='$dni', clave='$clave', latitud=$latitud, longitud=$longitud 
                WHERE codigo=$codigo;";
        } else {
            echo "Error: No se puede insertar o actualizar un registro con datos faltantes.";
            die();
        }
    }

    $exito = ejecutar($sql);

    if ($exito) {
        header('location: mapa.php');
    } else {
        echo "Error en ejecucion de: <br /> $sql";
    }
?>