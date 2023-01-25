<!DOCTYPE html>
<?php
    include './funcioncarga.php';
    $vias = listarVias();
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Adicionar Vía</title>
        <script src="js/leaflet.js"></script>
        <link rel="stylesheet" type="text/css" href="css/leaflet.css" />
        <script src="js/Control.OSMGeocoder.js"></script>
        <link rel="stylesheet" type="text/css" href="css/Control.OSMGeocoder.css" />
        <script src="js/L.Control.ZoomBar.js"></script>
        <link rel="stylesheet" type="text/css" href="css/L.Control.ZoomBar.css" />
        <script src="js/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    </head>
    <body>
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
            <a class="navbar-brand" href="#">
                <img src="images/ubicacion.gif" alt="logo" style="width:40px;">
            </a>
            <ul class="navbar-nav" role="nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">Adicionar Vía</a>
                </li>
            </ul>
        </nav>
        <div id="container">
            <div class="row">
                <div class="col-md-7">
                    <div id="map" style="height:450px;"></div>
                    <p>Clic en cada vértice de la vía, luego presionar <strong>Dibujar Vía</strong></p>
                    <input type="button" class="btn btn-primary" value="Dibujar Vía" onclick="dibujarLinea();" />
                    <input type="button" class="btn btn-danger" value="Limpiar Coordenadas" onclick="limpiarLinea();" />
                </div>
                <div class="col-md-5">
                    <form class="form-vertical" method="POST" action="insertayactualizavia.php" enctype="multipart/form-data" autocomplete="off">
                        <h3>Adicionar nueva vía</h3>
                        <table cellpadding="5" cellspacing="0" border="0">
                            <tbody>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Código</td>
                                    <td align="left" valign="top">
                                        <input type="text" name="txtcodigo" class="form-control" readonly />
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Distrito</td>
                                    <td align="left" valign="top">
                                        <input type="text" name="txtdistrito" class="form-control" required />
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Vía</td>
                                    <td align="left" valign="top">
                                        <input type="text" name="txtvia" class="form-control" required />
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Coordenadas</td>
                                    <td align="left" valign="top">
                                        <textarea id="geo" name="txtcoordenadas" rows="6" class="form-control" readonly></textarea>
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top"></td>
                                    <td align="right" valign="top">
                                        <input type="submit" value="Grabar" class="btn btn-success"/>
                                        <input type="reset" value="Limpiar" class="btn btn-danger"/>
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
            let opcionesRaster = {opacity: 0.85, attribution: 'Créditos imágen'};
            let raster = L.imageOverlay(rasterUrl, limiteRaster, opcionesRaster).addTo(map);
            let barraZoom = new L.Control.ZoomBar({position: 'topleft'}).addTo(map);
            let osmGeocoder = new L.Control.OSMGeocoder({
                collapsed: true,
                text: 'Buscar'
            });
            map.addControl(osmGeocoder);

            let polilinea = null;
            let marcadoresArrastrables = new Array();

            let limpiarLinea = () => {
                if (polilinea != null) {
                    map.removeLayer(polilinea);
                }
                for(let i = 0; i < marcadoresArrastrables.length; i++) {
                    if (marcadoresArrastrables[i] != null) {
                        map.removeLayer(marcadoresArrastrables[i]);
                    }
                }
                marcadoresArrastrables = new Array();
                $('#geo').val('');
            };

            let adicionarMarcador = (latLng) => {
                let opcionesMarcador = {draggable: true, autoPan: true, zIndexOffset: 900};
                let marcador = L.marker([latLng.lat, latLng.lng], opcionesMarcador).addTo(map);
                marcador.arrayId = marcadoresArrastrables.length;
                marcador.on('click', () => {
                    map.removeLayer(marcadoresArrastrables[marcador.arrayId]);
                    marcadoresArrastrables[marcador.arrayId] = null;
                });
                marcadoresArrastrables.push(marcador);
            };

            let dibujarLinea = () => {
                if (polilinea != null) {
                    map.removeLayer(polilinea);
                }
                let latsLongs = new Array();
                let puntos = new Array();
                for(let i = 0; i < marcadoresArrastrables.length; i++) {
                    if (marcadoresArrastrables[i] != null) {
                        latsLongs.push(L.latLng(marcadoresArrastrables[i].getLatLng().lat, marcadoresArrastrables[i].getLatLng().lng));
                        puntos.push(marcadoresArrastrables[i].getLatLng().lng.toFixed(6), marcadoresArrastrables[i].getLatLng().lat.toFixed(6));
                    }
                }
                if (latsLongs.length > 1) {
                    $('#geo').val(puntos);
                    polilinea = L.polyline(latsLongs, {color: 'red'}).addTo(map);
                    map.fitBounds(polilinea.getBounds());
                }
            };

            let textoACoordenadas = (geo) => {
                let vertices = geo.split(',');
                let lats = new Array();
                let lngs = new Array();
                let coordenadas = new Array();

                for (let i = 0; i < vertices.length; i++) {
                    if (i % 2) {
                        lats.push(vertices[i]);
                    } else {
                        lngs.push(vertices[i]);
                    }
                }

                for (let i = 0; i < lats.length; i++) {
                    coordenadas.push(L.latLng(lats[i], lngs[i]));
                }

                return coordenadas;
            };

            let vias = JSON.parse('<?php echo json_encode($vias); ?>');
            let adicionarVias = () => {
                for (let i = 0; i < vias.length; i++) {
                    let polilineaDB = L.polyline(textoACoordenadas(vias[i]['ubicaciones']), {color: 'lightblue'}).addTo(map);
                    polilineaDB.bindPopup('<b>' + vias[i]['via'] + '</b');
                }
            };

            $(document).ready(() => {
                map.on('click', (e) => {
                    adicionarMarcador(e.latlng);
                });
                adicionarVias();
            });
        </script>
    </body>
</html>