<?php
    $cnx = "";

    function conectar() {
        global $cnx;
        $cnx = new SQLite3("db/mapa.sqlite");
    }

    function desconectar() {
        global $cnx;
        $cnx->close();
    }

    function consultar($sql="") {
        global $cnx;
        conectar();
        $registros = $cnx->query($sql);
        $salida = array();
        while($registro=$registros->fetchArray(SQLITE3_ASSOC)) {
            $salida[] = $registro;
        }
        desconectar();
        return $salida;
    }

    function ejecutar($sql="") {
        global $cnx;
        conectar();
        $exito = $cnx->exec($sql);
        if ($exito == true or $exito == 1) {
            desconectar();
            return 1;
        } else {
            echo $cnx->lastErrorCode()." ".$cnx->lastErrorMsg()."<br/>";
            desconectar();
            return 0;
        }
    }
?>