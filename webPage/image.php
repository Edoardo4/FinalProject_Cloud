<!DOCTYPE html>
<?php

chdir(dirname(__DIR__));
require_once 'vendor/autoload.php';
use MyClasses\BlobStorageManager;

$blobStorageManager = new BlobStorageManager();

session_start();
$expired = true;
$sessionFlag = false;
if (array_key_exists('userName', $_GET)){
    $expired = $blobStorageManager->isShared($_GET['userName'], $_GET['name']);
    if ($expired === true || $expired === NULL){
        var_dump($_SESSION);
        header("Location: /");
    }
    $sessionFlag = true;
    $userName = $_GET['userName'];
}

if (!$sessionFlag){
    if ($_SESSION['userName'] == NULL){
        var_dump($_SESSION);
        header("Location: /");
    }
    $userName = $_SESSION['userName'];
}

?>
<html>
<head>
    <title><?=$_GET['name']?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.5/ol.js"></script>

    <link rel="stylesheet" href="/webPage/css/Style.css">
    <style>
        .image {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            height: auto;
            width: 90rem;
        }
        .map {
            height: 250px;
            width: 250px;
            display: inline-block;
        }
    </style>
</head>

<body>

    <div class="container-fluid pageContainer">
        <h2><?=$_GET['name']?></h2>
        <div class="row">
            <div class="col">
                <?php
                $image = $blobStorageManager->generateBlobDownloadLinkWithSAS($userName, $_GET['name']);

                    echo '
                    <div class="mx-3 image">
                        <img src="'.$image.'">
                    </div>
                    ';
                ?>
            </div>
        </div>
        <div id="dataInfo"></div>
        <form action="/script/setExpirationDate.php?userName=<?=$userName ?>&imageName=<?=$_GET['name'] ?>" method="post">

            <label>Input format: dd-mm-YYYY HH:mm:ss</label>
            <input type="text" id="expirationDate" name="expirationDate">
            <input type="submit" value="Share">

        </form>

        <div id='easterEgg'><div>

        <div style="width: 100%; text-align: center; margin-top: 20px">
            <div id="miniMap" class="map"></div>
        </div>
        
        <script>

            let dataInfo =  `
            <?php
                $apiUrl = 'localhost/api/ComputerVision/'.$userName.'/'.$_GET['name'];
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $apiUrl,
                    CURLOPT_RETURNTRANSFER => true 
                ]);
                $apiResult = curl_exec($curl);
                $resCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                if ($resCode == 404){
                    http_response_code(404);
                    header("Location: /error");
                    exit;
                }
                echo $apiResult;
            ?>`

            dataInfo = JSON.parse(dataInfo)

            str = '';
            str += dataInfo.dateTime != '' ? `Date: ${dataInfo.dateTime} <br>` : '';
            str += dataInfo.make != '' ? `Make: ${dataInfo.make} <br>` : '';
            str += dataInfo.model != '' ? `Model: ${dataInfo.model} <br>` : '';
            if (dataInfo.categories)
                if (dataInfo.categories.length > 0){
                    str += 'Categories: <br>'
                    dataInfo.categories.forEach( x => {
                        str += x + '&emsp;'
                    })
                    str += '<br>'
                }
            if(dataInfo.tags)
                if (dataInfo.tags.length > 0){
                    str += 'Tags: <br>'
                    dataInfo.tags.forEach( x => {
                        str += x + '&emsp;'
                    })
                    str += '<br>'
                }
            if(dataInfo.faces)
                if (dataInfo.faces.length > 0){
                    str += 'Faces: <br>'
                    dataInfo.faces.forEach( x => {
                        str += `Gender: ${x.gender} - Age: ${x.age} <br>`
                    })
                }

            if (dataInfo.tags.includes('anime'))
                $('#easterEgg').html(`<img src='/webPage/images/anime.gif' />`)

            let el = document.getElementById('dataInfo')
            el.innerHTML = str

            dataInfo.position = JSON.parse(dataInfo.position)
            if (dataInfo.position[0] != undefined){
                var view = new ol.View({
                    center: ol.proj.fromLonLat(dataInfo.position),
                    zoom: 11
                });
                var vectorSource = new ol.source.Vector({});
                var iconStyle = new ol.style.Style({
                    image: new ol.style.Icon({
                        anchor: [0.5, 1],
                        anchorXUnits: 'fraction',
                        anchorYUnits: 'fraction',
                        src: 'http://maps.google.com/mapfiles/ms/micons/blue.png',
                        crossOrigin: 'anonymous',
                    })
                    });
                var places = [];
                places.push(dataInfo.position)

                var features = [];
                for (var i = 0; i < places.length; i++) {
                    var iconFeature = new ol.Feature({
                    geometry: new ol.geom.Point(ol.proj.transform([places[i][0], places[i][1]], 'EPSG:4326', 'EPSG:3857')),
                    });
                    
                    iconFeature.setStyle(iconStyle);
                    vectorSource.addFeature(iconFeature);
                }

                var vectorLayer = new ol.layer.Vector({
                    source: vectorSource,
                    updateWhileAnimating: true,
                    updateWhileInteracting: true,
                });

                var map = new ol.Map({
                    target: 'miniMap',
                    view: view,
                    layers: [
                    new ol.layer.Tile({
                        preload: 3,
                        source: new ol.source.OSM(),
                    }),
                    vectorLayer,
                    ],
                    loadTilesWhileAnimating: true,
                });
            }
        </script>
    </div>

</body>