<!DOCTYPE html>
<?php
    include './libreria.php';

    $codigo = intval($_POST['txtcodigo']);

    if (($codigo == null) or ($codigo == 0)) {
        echo "Error, esta página no debe ser invocada por el usuario.";
        die();
    }

    $sql = "DELETE FROM personas WHERE codigo=$codigo";
    //echo $sql;
    //die();
    
    $exito = ejecutar($sql);

    if ($exito) {
        header('location: mapa.php');
    } else {
        echo "Error en ejecucion de: <br /> $sql";
    }
?>