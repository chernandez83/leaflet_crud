<!DOCTYPE html>
<?php
    include './libreria.php';

    $codigo = $_POST['txtcodigo'];
    $distrito = $_POST['txtdistrito'];
    $via = $_POST['txtvia'];
    $coordenadas = $_POST['txtcoordenadas'];

    //echo $codigo." ".$distrito." ".$via." ".$coordenadas;

    if (($codigo == '') or ($codigo == 0)) {
        if (($distrito != '') and ($distrito != 0) and ($via != '') and ($via != 0)) {
            $sql = "INSERT INTO vias(distrito, via, ubicaciones) 
                VALUES ('$distrito', '$via', '$coordenadas')";
        } else {
            echo "Error: No se puede insertar o actualizar un registro con datos faltantes.";
            die();
        }
    } else {
        if (($distrito != '') and ($distrito != 0) and ($via != '') and ($via != 0)) {
            $sql = "UPDATE vias SET distrito = '$distrito', via = '$via', ubicaciones = '$coordenadas' 
                WHERE codigo = $codigo";
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