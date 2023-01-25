<!DOCTYPE html>
<?php
    include './funcioncarga.php';
    $poligonos = listarPoligonos();
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Eliminar Polígono</title>
        <script type="text/javascript" src="js/leaflet.js"></script>
        <link rel="stylesheet" type="text/css" href="css/leaflet.css" />
        <script type="text/javascript" src="js/Control.OSMGeocoder.js"></script>
        <link rel="stylesheet" type="text/css" href="css/Control.OSMGeocoder.css" />
        <script type="text/javascript" src="js/L.Control.ZoomBar.js"></script>
        <link rel="stylesheet" type="text/css" href="css/L.Control.ZoomBar.css" />
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    </head>
    <body>
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark" role="navigation">
            <a class="navbar-brand" href="#">
                <img src="images/ubicacion.gif" alt="logo" style="width:40px;">
            </a>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">Eliminar Polígono</a>
                </li>
            </ul>
        </nav>
        <div id="container">
            <div class="row">
                <div class="col-md-7">
                    <div id="map" style="height:450px;"></div>
                    <!--<input type="button" class="btn btn-primary" value="Dibujar" onclick="dibujarPoligono();" />
                    <input type="button" class="btn btn-danger" value="Limpiar" onclick="limpiarPoligono();" />
                    <p>Clic en cada vértice para editar, después clic en <strong>Dibujar</strong> para reflejar cambios en el polígono</p>-->
                </div>
                <div class="col-md-5">
                    <form class="form-vertical" method="POST" action="borrarpoligono.php" enctype="multipart/form-data"
                         autocomplete="off" onsubmit="return confirmarEliminar();">
                        <h3>Eliminar Polígono</h3>
                        <table cellpadding="5" cellspacing="0" border="0">
                            <tbody>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Polígono</td>
                                    <td align="left" valign="top">
                                        <select id="txtseleccion" name="txtseleccion" class="form-control">
                                            <option value="0">Seleccione Polígono&nbsp;</option>
                                            <?php
                                                for ($i = 0; $i < count($poligonos); $i++) {
                                                    echo '<option value="'.$poligonos[$i]['codigo'].'">'.$poligonos[$i]['nombre'].'</option>';
                                                }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Código</td>
                                    <td align="left" valign="top">
                                        <input type="text" name="txtcodigo" id="txtcodigo" class="form-control" readonly />
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Distrito</td>
                                    <td align="left" valign="top">
                                        <input type="text" name="txtdistrito" id="txtdistrito" class="form-control" readonly />
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Nombre</td>
                                    <td align="left" valign="top">
                                        <input type="text" name="txtnombre" id="txtnombre" class="form-control" readonly />
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Coordenadas</td>
                                    <td align="left" valign="top">
                                        <textarea name="txtcoordenadas" id="txtcoordenadas" class="form-control" rows="6" readonly></textarea>
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle"></td>
                                    <td align="right" valign="top">
                                        <input type="submit" value="Eliminar" class="btn btn-danger" />
                                        <input type="button" value="Cancelar" class="btn btn-primary" onclick="window.location.href = 'mapa.php';" />
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
            let opcionesRaster = {opacity: 0.85,attribution: 'Créditos imágen'};
            let raster = L.imageOverlay(rasterUrl, limiteRaster, opcionesRaster).addTo(map);
            let barraZoom = new L.Control.ZoomBar({position: 'topleft'}).addTo(map);
            
            let osmGeocoder = new L.Control.OSMGeocoder({
                collapsed: true,
                text: 'Buscar',
            });
            map.addControl(osmGeocoder);

            let poligono = null;
            let marcadores = new Array();

            let limpiarPoligono = () => {
                if (poligono != null) {
                    map.removeLayer(poligono);
                }
                for (let i = 0; i < marcadores.length; i++) {
                    if (marcadores[i] != null) {
                        map.removeLayer(marcadores[i]);
                    }
                }
                marcadores = new Array();
                $('#txtcoordenadas').val('');
            };

            let agregarMarcador = (latLng) => {
                let opcionesMarcador = {draggable: false, autoPan: false, zIndexOffset: 900};
                let marcador = L.marker([latLng.lat, latLng.lng], opcionesMarcador).addTo(map);
                marcador.arrayId = marcadores.length;
                marcador.on('click', () => {
                    map.removeLayer(marcadores[marcador.arrayId]);
                    marcadores[marcador.arrayId] = null;
                });
                marcadores.push(marcador);
            };

            let dibujarPoligono = () => {
                if (poligono != null) {
                    map.removeLayer(poligono);
                }

                let latLngs = new Array();
                let puntos = new Array();

                for (let i = 0; i < marcadores.length; i++) {
                    if (marcadores[i] != null) {
                        latLngs.push(L.latLng(marcadores[i].getLatLng().lat, marcadores[i].getLatLng().lng));
                        puntos.push(marcadores[i].getLatLng().lng.toFixed(6), marcadores[i].getLatLng().lat.toFixed(6));
                    }
                }

                if (latLngs.length > 2) {
                    $('#txtcoordenadas').val(puntos);
                    poligono = L.polygon(latLngs, {color: 'red', dashArray: '5, 5'}).addTo(map);
                    map.fitBounds(poligono.getBounds());
                }
            };

            let dibujarPuntos = (geo) => {
                let coordsLinea = stringAGeoPuntos(geo);

                for (let i = 0; i < coordsLinea.length; i++) {
                    agregarMarcador(coordsLinea[i]);
                }
            };

            let stringAGeoPuntos = (geo) => {
                let coords = geo.split(',');
                let lats = new Array();
                let lngs = new Array();

                for (let i = 0; i < coords.length; i++) {
                    if (i % 2) {
                        lats.push(coords[i]);
                    } else {
                        lngs.push(coords[i]);
                    }
                }

                let coordsLinea = new Array();
                for (let i = 0; i < lats.length; i++) {
                    coordsLinea.push(L.latLng(lats[i], lngs[i]));
                }

                return coordsLinea;
            };

            let confirmarEliminar = () => {
                return confirm(`Presione OK para eliminar a ${$('#txtnombre').val()} de la base de datos`);
            };

            let poligonos = JSON.parse('<?php echo json_encode($poligonos) ?>');
            /*let agregarPoligonos = () => {
                for (let i = 0; i < poligonos.length; i++) {
                    let pol = L.polygon(stringAGeoPuntos(poligonos[i]['ubicaciones']), {color: 'cyan'}).addTo(map);
                }
            };*/

            $(document).ready(() => {
                //agregarPoligonos();
                map.on('click', (e) => {
                    agregarMarcador(e.latlng);
                });

                $('#txtseleccion').change(() => {
                    limpiarPoligono();
                    for (let i =0; i < poligonos.length; i++) {
                        if (poligonos[i]['codigo'] == $('#txtseleccion').val()) {
                            $('#txtcodigo').val(poligonos[i]['codigo']);
                            $('#txtdistrito').val(poligonos[i]['distrito']);
                            $('#txtnombre').val(poligonos[i]['nombre']);
                            $('#txtcoordenadas').val(poligonos[i]['ubicaciones']);
                            dibujarPuntos(poligonos[i]['ubicaciones']);
                            dibujarPoligono();
                            break;
                        }
                    }
                })
            });
        </script>
    </body>
</html>