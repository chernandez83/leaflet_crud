<?php
    include "./libreria.php";

    function listarPersonas() {
        $sql = "SELECT * FROM personas ORDER BY usuario ASC";
        $personas = consultar($sql);
        return $personas;
    }

    function listarVias() {
        $sql = "SELECT * FROM vias";
        $vias = consultar($sql);
        return $vias;
    }

    function listarPoligonos() {
        $sql = "SELECT * FROM poligonos";
        $poligonos = consultar($sql);
        return $poligonos;
    }

    //var_dump(listarPoligonos());
?>