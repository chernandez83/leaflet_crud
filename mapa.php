<!DOCTYPE html>
<?php
    include "./funcioncarga.php";

    $personas = listarPersonas();
    $vias = listarVias();
    $poligonos = listarPoligonos();
?>

<html>
    <head>
        <title>Mapa en general</title>
        <link rel="stylesheet" href="./css/leaflet.css" />
        <link rel="stylesheet" href="./css/Control.OSMGeocoder.css"/>
        <script src="./js/jquery.min.js"></script>
        <script src="./js/leaflet.js"></script>
        <script src="./js/Control.OSMGeocoder.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
        <style>
            body {padding: 0; margin: 0;}
            html, body {height: 100%; width: 100%;}
            #map {height: 100%; width: 100%;}
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
            <a class="navbar-brand" href="#">
                <img src="images/ubicacion.gif" alt="logo" style="width:40px;">
            </a>
            <ul class="navbar-nav" role="nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">Mapa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./adicionarpersona.php">Agregar Persona</a>
                </li>
            </ul>
        </nav>
        <div id="map"></div>
        <script>
            let personas = JSON.parse('<?php echo json_encode($personas) ?>');
            let vias = JSON.parse('<?php echo json_encode($vias) ?>');
            let poligonos = JSON.parse('<?php echo json_encode($poligonos) ?>');

            let map = L.map('map').setView([-11.978140, -76.999559], 15);
            let osm = L.tileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {maxZoom: 21}).addTo(map);
            let urlImagen = "./raster/SJL.jpg";
            let limiteImagen = [[-11.981203, -77.005885], [-11.974816, -76.994032]];
            let raster = L.imageOverlay(urlImagen, limiteImagen, {opacity: 0.80, attribution: "Raster personalizado"}).addTo(map);
            let osmGeocoder = new L.Control.OSMGeocoder({
                collapsed: true,
                //position: "bottomright",
                text: "Buscar"
            }).addTo(map);

            let adicionarPersonas = () => {
                for(let i = 0; i < personas.length; i++) {
                    let marker = L.marker([personas[i]['latitud'], personas[i]['longitud']]).addTo(map);
                    marker.bindPopup("<b>" + personas[i]['codigo'] + 
                        "</b><br>Usuario: " + personas[i]['usuario'] + 
                        "<br>DNI: " + personas[i]['dni']);
                }
            }

            let textoACoordenadas = (geo) => {
                let vertices = geo.split(",");
                let lats = new Array();
                let lons = new Array();
                let coordenadas = new Array();

                for(let i = 0; i < vertices.length; i++) {
                    if (i % 2) {
                        lats.push(vertices[i]);
                    } else {
                        lons.push(vertices[i]);
                    }
                }               

                for(let i = 0; i < lats.length; i++) {
                    coordenadas.push(L.latLng(lats[i], lons[i]));
                }

                return coordenadas;
            }

            let adicionarVias = () => {
                for(let i = 0; i < vias.length; i++) {
                    let via = L.polyline(textoACoordenadas(vias[i]['ubicaciones']), {color:'blue'}).addTo(map);
                    via.bindPopup("<b>"+vias[i]['via']+"</b>");
                }
            }

            let adicionarPoligonos = () => {
                for(let i = 0; i < poligonos.length; i++) {
                    let poligono = L.polygon(textoACoordenadas(poligonos[i]['ubicaciones']), {color:'blue'}).addTo(map);
                    poligono.bindPopup("<b>"+poligonos[i]['nombre']+"</b>");
                }
            }

            $(document).ready(function() {
                adicionarPersonas();
                adicionarVias();
                adicionarPoligonos();
            });
        </script>
    </body>
</html>