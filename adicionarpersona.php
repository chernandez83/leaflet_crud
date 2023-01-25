<!DOCTYPE html>
<?php
    include './funcioncarga.php';
    $personas = listarPersonas();
?>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title>Adicionar Persona</title>
    <script src="js/leaflet.js"></script>
    <link rel="stylesheet" href="css/leaflet.css" type="text/css" />
    <script src="js/Control.OSMGeocoder.js"></script>
    <link rel="stylesheet" href="css/Control.OSMGeocoder.css" type="text/css" />
    <script src="js/L.Control.ZoomBar.js"></script>
    <link rel="stylesheet" href="css/L.Control.ZoomBar.css" type="text/css" />
    <script src="js/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
</head>
<body>
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark" role="nav">
        <a href="#" class="navbar-brand">
            <img src="images/ubicacion.gif" alt="logo" style="width:40px">
        </a>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#">Adicionar Persona</a>
            </li>
        </ul>
    </nav>
    <div id="container">
        <div class="row">
            <div class="col-md-7">
                <div id="map" style="height:450px; width:auto"></div>
                <p><strong>Arrastrar el marcador en el mapa.</strong></p>
            </div>
            <div class="col-md-5">
                <form class="form-vertical" method="POST" action="insertayactualizapersona.php" enctype="multipart/form-data" autocomplete="off">
                    <h3>Adicionar Persona</h3>
                    <table cellpadding="5" cellspacing="0" border="0">
                        <tbody>
                        <tr align="left" valign="top">
                            <td align="left" valign="middle">Código</td>
                            <td align="left" valign="top">
                                <input type="text" name="txtcodigo" class="form-control" readonly />
                            </td>
                        </tr>
                        <tr align="left" valign="top">
                            <td align="left" valign="middle">Usuario</td>
                            <td align="left" valign="top">
                                <input type="text" name="txtusuario" class="form-control" required />
                            </td>
                        </tr>
                        <tr align="left" valign="top">
                            <td align="left" valign="middle">DNI</td>
                            <td align="left" valign="top">
                                <input type="text" name="txtdni" class="form-control" required />
                            </td>
                        </tr>
                        <tr align="left" valign="top">
                            <td align="left" valign="middle">Clave</td>
                            <td align="left" valign="top">
                                <input type="text" name="txtclave" class="form-control" required />
                            </td>
                        </tr>
                        <tr align="left" valign="top">
                            <td align="left" valign="middle">Latitud</td>
                            <td align="left" valign="top">
                                <input id="lat" type="text" name="txtlatitud" class="form-control" readonly />
                            </td>
                        </tr>
                        <tr align="left" valign="top">
                            <td align="left" valign="middle">Longitud</td>
                            <td align="left" valign="top">
                                <input id="lng" type="text" name="txtlongitud" class="form-control" readonly />
                            </td>
                        </tr>
                        <tr align="left" valign="top">
                            <td align="left" valign="middle"></td>
                            <td align="right" valign="top">
                                <input type="submit" value="Grabar" class="btn btn-success"/>
                                <input type="reset" value="Limpiar" class="btn btn-danger" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <script>
        let map = L.map('map', {zoomControl: false}).setView([-11.978140,-76.999559], 16);
        let osm = L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 21}).addTo(map);
        let rasterUrl = 'raster/SJL.jpg';
        let limiteRaster = [[-11.981203,-77.005885], [-11.974816,-76.994032]];
        let opcionesRaster = {opacity: 0.75, attribution: 'Créditos imágen'};
        let raster = L.imageOverlay(rasterUrl, limiteRaster, opcionesRaster).addTo(map);
        let barraZoom = new L.Control.ZoomBar({position: 'topleft'}).addTo(map);

        let osmGeocoder = new L.Control.OSMGeocoder({
            collapsed: true,
            text: 'Buscar'
        });
        map.addControl(osmGeocoder);

        let dibujarPunto = () => {
            let opcionesMarcador = {draggable: true, zIndexOffset: 900, autoPan: true}
            let marcadorArrastrable = L.marker([map.getCenter().lat, map.getCenter().lng], opcionesMarcador).addTo(map);

            marcadorArrastrable.on('dragend', (e) => {
                $('#lat').val(marcadorArrastrable.getLatLng().lat.toFixed(6));
                $('#lng').val(marcadorArrastrable.getLatLng().lng.toFixed(6));
            });
        };

        let personas = JSON.parse('<?php echo json_encode($personas) ?>');
        let adicionarPersonsas = () => {
            for(let i = 0; i < personas.length; i++) {
                var marcador = L.marker([personas[i]['latitud'], personas[i]['longitud']]).addTo(map);
                marcador.bindPopup("<b>" + personas[i]['codigo'] 
                    + "</b><br>Usuario: " + personas[i]['usuario']
                    + "<br>DNI: " + personas[i]['dni']);
            }
        };

        $(document).ready(() => {
            dibujarPunto();
            adicionarPersonsas();
        });
    </script>
</body>
</html>