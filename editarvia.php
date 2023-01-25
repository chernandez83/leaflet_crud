<!DOCTYPE html>
<?php
    include './funcioncarga.php';
    $vias = listarVias();
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width; initial-scale=1">
        <title>Editar Vía</title>
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
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
            <a class="navbar-brand" href="#">
                <img src="images/ubicacion.gif" alt="logo" style="width:40px;">
            </a>
            <ul class="navbar-nav" role="nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">Editar Vía</a>
                </li>
            </ul>
        </nav>
        <div id="container">
            <div class="row">
                <div class="col-md-7">
                    <div id="map" style="height:450px"></div>
                    <input type="button" class="btn btn-primary" value="Dibujar" onclick="dibujarVia()" />
                    <input type="button" class="btn btn-danger" value="Limpiar" onclick="limpiarVia()" />
                    <p>Clic en cada vértice para editar, después clic en <strong>Dibujar</strong> para reflejar cambios en la vía</p>
                </div>
                <div clas="col-md-5">
                    <form class="form-vertical" method="POST" action="insertayactualizavia.php" autocomplete="off">
                        <h3>Editar Vía</h3>
                        <table cellpadding="5" cellspacing="0" border="0">
                            <tbody>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Vía</td>
                                    <td align="left" valign="top">
                                        <select id="txtseleccion" name="txtseleccion" class="form-control">
                                            <option value="0">Seleccione Vía&nbsp;</option>
                                            <?php
                                                for($i = 0; $i < count($vias); $i++) {
                                                    echo '<option value="'.$vias[$i]['codigo'].'">'.$vias[$i]['via'].'</option>';
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
                                        <input type="text" name="txtdistrito" id="txtdistrito" class="form-control" required />
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Vía</td>
                                    <td align="left" valign="top">
                                        <input type="text" name="txtvia" id="txtvia" class="form-control" required />
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
                                        <input type="submit" value="Actualizar" class="btn btn-success" />
                                        <input type="button" value="Cancelar" class="btn btn-danger" onclick="window.location.href = 'mapa.php';" />
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
            let opcionesRaster = {opacity: 0.85, attribution: 'Cŕeditos imágen'};
            let raster = L.imageOverlay(rasterUrl, limiteRaster, opcionesRaster).addTo(map);
            let barraZoom = new L.Control.ZoomBar({position: 'topleft'}).addTo(map);

            let osmGeocoder = new L.Control.OSMGeocoder({
                collapsed: true,
                text: 'Buscar'
            });
            map.addControl(osmGeocoder);

            let polilinea = null;
            let marcadores = new Array();

            let limpiarVia = () => {
                if (polilinea != null) {
                    map.removeLayer(polilinea);
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
                let opcionesMarcador = {draggable: true, autoPan: true, zIndexOffset: 900};
                let marcador = L.marker([latLng.lat, latLng.lng], opcionesMarcador).addTo(map);
                marcador.arrayId = marcadores.length;
                marcador.on('click', () => {
                    map.removeLayer(marcadores[marcador.arrayId]);
                    marcadores[marcador.arrayId] = null;
                });
                marcadores.push(marcador);
            };

            let dibujarVia = () => {
                if (polilinea != null) {
                    map.removeLayer(polilinea);
                }

                let latLngs = new Array();
                let puntos = new Array();

                for (let i = 0; i < marcadores.length; i++) {
                    if (marcadores[i] != null) {
                        latLngs.push(L.latLng(marcadores[i].getLatLng().lat, marcadores[i].getLatLng().lng));
                        puntos.push(marcadores[i].getLatLng().lng.toFixed(6), marcadores[i].getLatLng().lat.toFixed(6));
                    }
                }

                if (latLngs.length > 1) {
                    $('#txtcoordenadas').val(puntos);
                    polilinea = L.polyline(latLngs, {color: 'red', dashArray: '5, 5'}).addTo(map);
                    map.fitBounds(polilinea.getBounds());
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

            let vias = JSON.parse('<?php echo json_encode($vias) ?>');
            let agregarVias = () => {
                for (let i = 0; i < vias.length; i++) {
                    let linea = L.polyline(stringAGeoPuntos(vias[i]['ubicaciones']), {color:'cyan'}).addTo(map);
                };
            };

            $(document).ready(() => {
                agregarVias();
                map.on('click', (e) => {
                    agregarMarcador(e.latlng);
                });

                $('#txtseleccion').change(() => {
                    limpiarVia();

                    for (let i = 0; i < vias.length; i++) {
                        if (vias[i]['codigo'] == $('#txtseleccion').val()) {
                            $('#txtcodigo').val(vias[i]['codigo']);
                            $('#txtdistrito').val(vias[i]['distrito']);
                            $('#txtvia').val(vias[i]['via']);
                            $('#txtcoordenadas').val(vias[i]['ubicaciones']);
                            dibujarPuntos(vias[i]['ubicaciones']);
                            dibujarVia();
                            break;
                        }
                    }
                });
            });
        </script>
    </body>
</html>