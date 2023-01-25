<!DOCTYPE html>
<?php
    include './funcioncarga.php';
    $personas = listarPersonas();
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Editar Persona</title>
        <script type="text/javascript" src="js/leaflet.js"></script>
        <link rel="stylesheet" type="text/css" href="css/leaflet.css" />
        <script type="text/javascript" src="js/Control.OSMGeocoder.js"></script>
        <link rel="stylesheet" type="text/css" href="css/Control.OSMGeocoder.css" />
        <script src="js/L.Control.ZoomBar.js"></script>
        <link rel="stylesheet" href="css/L.Control.ZoomBar.css" type="text/css" />
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    </head>
    <body>
        <div id="container">
            <div class="row">
                <div class="col-md-7"">
                    <div id="map" style="height:450px;"></div>
                    <p>Arrastrar el marcador en el mapa para cambiar coordenadas.</p>
                </div>
                <div class="col-md-5">
                    <form class="form-vertical" method="POST" action="insertayactualizapersona.php" enctype="multipart/form-data" autocomplete="off">
                        <h3>Editar Persona</h3>
                        <table cellpadding="5" cellspacing="0" border="0">
                            <tbody>
                                <tr align="left" valing="top">
                                    <td align="left" valign="middle">Persona</td>
                                    <td align="left" valign="top">
                                        <select id="txtpersona" name="txtpersona" class="form-control">
                                            <option value="0">Selecciona Persona</option>
                                            <?php
                                                for ($i=0; $i<count($personas); $i++) {
                                                    echo '<option value="'.$personas[$i]['codigo'].'">'.$personas[$i]['usuario'].'</option>';
                                                }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr align="left" valing="top">
                                    <td align="left" valign="middle">Código</td>
                                    <td align="left" valign="top">
                                        <input type="text" name="txtcodigo" id="txtcodigo" class="form-control" readonly />
                                    </td>
                                </tr>
                                <tr align="left" valing="top">
                                    <td align="left" valign="middle">Usuario</td>
                                    <td align="left" valign="top">
                                        <input type="text" name="txtusuario" id="txtusuario" class="form-control" />
                                    </td>
                                </tr>
                                <tr align="left" valing="top">
                                    <td align="left" valign="middle">DNI</td>
                                    <td align="left" valign="top">
                                        <input type="text" name="txtdni" id="txtdni" class="form-control" />
                                    </td>
                                </tr>
                                <tr align="left" valing="top">
                                    <td align="left" valign="middle">Clave</td>
                                    <td align="left" valign="top">
                                        <input type="text" name="txtclave" id="txtclave" class="form-control" />
                                    </td>
                                </tr>
                                <tr align="left" valing="top">
                                    <td align="left" valign="middle">Latitud</td>
                                    <td align="left" valign="top">
                                        <input type="text" name="txtlatitud" id="txtlatitud" class="form-control" readonly />
                                    </td>
                                </tr>
                                <tr align="left" valing="top">
                                    <td align="left" valign="middle">Longitud</td>
                                    <td align="left" valign="top">
                                        <input type="text" name="txtlongitud" id="txtlongitud" class="form-control" readonly />
                                    </td>
                                </tr>
                                <tr align="left" valing="top">
                                    <td align="left" valign="middle"></td>
                                    <td align="right" valign="top">
                                        <input type="submit" value="Actualizar" class="btn btn-success" />
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

            let marcador = null;
            let putDraggable = () => {
                let opcionesMarcador = {draggable: true, autoPan: true, zIndexOffset: 900};
                marcador = L.marker([map.getCenter().lat, map.getCenter().lng], opcionesMarcador).addTo(map);

                marcador.on('dragend',(e) => {
                    $('#txtlatitud').val(marcador.getLatLng().lat.toFixed(6));
                    $('#txtlongitud').val(marcador.getLatLng().lng.toFixed(6));
                });
            };

            let personas = JSON.parse('<?php echo json_encode($personas) ?>');

            $(document).ready(() => {
                putDraggable();
                
                $('#txtpersona').change(() => {
                    let seleccion = $('#txtpersona').val();

                    for (let i = 0; i < personas.length; i++) {
                        if (personas[i]['codigo'] == seleccion) {
                            $('#txtcodigo').val(personas[i]['codigo']);
                            $('#txtusuario').val(personas[i]['usuario']);
                            $('#txtdni').val(personas[i]['dni']);
                            $('#txtclave').val(personas[i]['clave']);
                            $('#txtlatitud').val(personas[i]['latitud']);
                            $('#txtlongitud').val(personas[i]['longitud']);

                            marcador.setLatLng([personas[i]['latitud'], personas[i]['longitud']]);
                            map.panTo(marcador.getLatLng());
                            break;
                        }
                    }
                });
            });
        </script>
    </body>
</html>