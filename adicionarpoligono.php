<!DOCTYPE html>
<?php
    include './funcioncarga.php';
    $poligonos = listarPoligonos();
?>
<html>
    <head>
        <meta charset="utf-8>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Adicionar polígonos</title>
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
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark" role="nav">
            <a class="navbar-brand" href="#">
                <img src="images/ubicacion.gif" alt="Logo" style="width:40px;" />
            </a>
            <ul class="nav navbar-nav">
                <li class="nav nav-item">
                    <a class="nav-link" href="#">Adición de polígonos</a>
                </li>
            </ul>
        </nav>
        <div id="container">
            <div class="row">
                <div class="col-md-7">
                    <div id="map" style="height:450px"></div>
                    <p>Clic en cada vertice del polígono, luego presionar el boton <strong>Dibujar polígono</strong>.</p>
                    <input type="button" class="btn btn-primary" value="Dibujar polígono" onclick="dibujarPoligono();" />
                    <input type="button" class="btn btn-danger" value="Limpiar coordenadas" onclick="limpiarPoligono()" /><br />
                </div>
                <div class="col-md-5">
                    <form class="form-vertical" method="POST" action="insertayactualizapoligono.php" enctype="multipart/form-data" autocomplete="off">
                        <h3>Adicionar nuevo polígono</h3>
                        <table cellpadding="5" celllspacing="0" border="0">
                            <tbody>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Código</td>
                                    <td align="left" valign="top"><input type="text" name="txtcodigo" readonly class="form-control" /> </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Distrito</td>
                                    <td align="left" valign="top"><input type="text" name="txtdistrito" class="form-control" /> </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Nombre</td>
                                    <td align="left" valign="top"><input type="text" name="txtnombre" class="form-control" /> </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="middle">Coordenadas</td>
                                    <td align="left" valign="top"><textarea id="geo" name="txtcoordenadas" rows="6" class="form-control" readonly></textarea> </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top"></td>
                                    <td align="right" valign="top"><input type="submit" value="Grabar" class="btn btn-success" />
                                        <input type="reset" value="Limpiar" class="btn btn-danger" </td>
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
            let imagenUrl = 'raster/SJL.jpg';
            let limitenImagen = [[-11.981203,-77.005885], [-11.974816,-76.994032]];
            let opcionesImagen = {opacity: 0.85, attribution: "Créditos de imagen"};
            let raster = L.imageOverlay(imagenUrl, limitenImagen, opcionesImagen).addTo(map);
            let barraZoom = new L.Control.ZoomBar({position: 'topleft'}).addTo(map);
            let osmGeocoder = new L.Control.OSMGeocoder({collapsed: true, text: 'Buscar'});
            map.addControl(osmGeocoder);

            let poligono;
            let marcadorArrastrable = new Array();
            
            let limpiarPoligono = () => {
                if (poligono != null) {
                    map.removeLayer(poligono);
                }
                for(let i = 0; i < marcadorArrastrable.length; i++) {
                    if (marcadorArrastrable[i] != null) {
                        map.removeLayer(marcadorArrastrable[i]);
                    }
                }
                poligono = null;
                marcadorArrastrable = new Array();
                $('#geo').val('');
            };

            let adicionarMarcador = (latLng) => {
                let opcionesMarcador = {draggable: true, zIndexOffset: 900};
                let marcador = L.marker([latLng.lat, latLng.lng], opcionesMarcador).addTo(map);
                marcador.arrayId = marcadorArrastrable.length;
                marcador.on('click', () => {
                    map.removeLayer(marcadorArrastrable[marcador.arrayId]);
                    marcadorArrastrable[marcador.arrayId] = null;
                });
                marcadorArrastrable.push(marcador);
            };

            let dibujarPoligono = () => {
                if (poligono != null) {
                    map.removeLayer(poligono);
                }

                let latLng = new Array();
                let puntos = new Array();
                for(let i = 0; i < marcadorArrastrable.length; i++) {
                    if (marcadorArrastrable[i] != null) {
                        latLng.push(L.latLng(marcadorArrastrable[i].getLatLng().lat, marcadorArrastrable[i].getLatLng().lng));
                        puntos.push(marcadorArrastrable[i].getLatLng().lng.toFixed(6), marcadorArrastrable[i].getLatLng().lat.toFixed(6));
                    }
                }

                if (latLng.length > 2) {
                    poligono = L.polygon(latLng, {color: 'blue'}).addTo(map);
                    $('#geo').val(puntos);
                }

                if (poligono != null) {
                    map.fitBounds(poligono.getBounds());
                }
            };

            let textoACoordenadas = (geo) => {
                let vertices = geo.split(',');
                let coordenadasLat = new Array();
                let coordenadasLng = new Array();

                for(let i = 0; i < vertices.length; i++) {
                    if (i % 2) {
                        coordenadasLat.push(vertices[i]);
                    } else {
                        coordenadasLng.push(vertices[i]);
                    }
                }

                let coordenadas = new Array();
                for(let i = 0; i < coordenadasLat.length; i++) {
                    coordenadas.push(L.latLng(coordenadasLat[i], coordenadasLng[i]));
                }

                return coordenadas;
            };

            let poligonos = JSON.parse('<?php echo json_encode($poligonos) ?>');
            let adicionarPoligonos = () => {
                for(let i = 0; i < poligonos.length; i++) {
                    let poligono = L.polygon(textoACoordenadas(poligonos[i]['ubicaciones']), {color: 'blue'}).addTo(map);
                    poligono.bindPopup("<b>" + poligonos[i]['nombre'] + "</b>");
                }
            }

            $(document).ready(() => {
                map.on('click', (e) => {
                    adicionarMarcador(e.latlng);
                });
                adicionarPoligonos();
            });
        </script>
    </body>
</html>