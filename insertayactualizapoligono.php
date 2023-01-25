<!DOCTYPE html>
<?php
    $codigo = $_POST['txtcodigo'];
    $distrito = $_POST['txtdistrito'];
    $nombre = $_POST['txtnombre'];
    $coordenadas = $_POST['txtcoordenadas'];

    //echo $codigo." ".$distrito." ".$nombre." ".$coordenadas;

    include './libreria.php';

    if ($codigo == "" or $codigo == 0) {
        if (($codigo != '') and ($codigo != 0) and ($distrito != '') and ($nombre != '') and ($coordenadas != '') ) {
            $sql = "INSERT INTO poligonos(distrito, nombre, ubicaciones) 
                VALUES('$distrito', '$nombre', '$coordenadas')";
        } else {
            echo "Error: No se puede insertar o actualizar un registro con datos faltantes.";
            die();
        }
    } else {
        if (($codigo != '') and ($codigo != 0) and ($distrito != '') and ($nombre != '') and ($coordenadas != '') ) {
            $sql = "UPDATE poligonos
                SET distrito = '$distrito', nombre = '$nombre', ubicaciones = '$coordenadas'
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