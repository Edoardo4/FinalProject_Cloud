<!DOCTYPE html>
<?php
session_start();
if ($_SESSION['userName'] == null){
    var_dump($_SESSION);
    header("Location: /webPage/index.php");
}
$userName = $_SESSION['userName'];

$tags = '';
if (array_key_exists('tags', $_GET)){
    $tags = '?tags='.$_GET['tags'];
}

$apiUrl = 'localhost/api/ComputerVision/'.$userName. $tags;
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true 
]);
$apiResult = curl_exec($curl);
$resCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
$imageList = json_decode($apiResult);

?>
<html>
<head>
    <title>Cloud Project</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.5/ol.js"></script>
    
    <script type="text/javascript" src="/webPage/js/HomePage.js"></script>
    <link rel="stylesheet" href="/webPage/css/Style.css">

</head>
<body>

    <div class="container-fluid pageContainer">



        <!-- Trigger the modal with a button -->
        <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Upload Image</button>

        <!-- Modal -->
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
            
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Modal Header</h4>
                </div>
                <div class="modal-body">

                    <form action="./../script/UploadImage.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="imageToUpload" class="form-control-file" accept="image/*" id="imageToUpload">
                    <input type="hidden" name="accountName" value="<?= $userName ?>"/>
                    <br><br>
                    <button type="submit">Submit</button>
                    </form> 

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
            
            </div>
        </div>


        <input class="btn btn-primary" type="button" value="Elimina Immagini" id="deleteButton">

        <input type="text" id="filter" placeholder="Insert tags ex. 'cat,dog'">
        <input type="button" onclick="search()" value="Search">

        <form action="./../script/DeleteBlobs.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="accountName" value="<?= $userName ?>"/>
            <ul>
            <?php

                chdir(dirname(__DIR__));
                require_once 'vendor/autoload.php';
                use MyClasses\BlobStorageManager;
                
                $blobStorageManager = new BlobStorageManager();

                foreach ($imageList as $key){
                    $imageUrl = $blobStorageManager->generateBlobDownloadLinkWithSAS($userName, $key);

                    echo '
                        <li>
                            <input type="checkbox" name="Images['.$key.']" id="'.$key.'" disabled="disabled"/>
                            <label for="'. $key .'">
                                <div class="imageContainer">
                                    <img class="rounded center-block" src="'. $imageUrl .'" onclick="goToImage(\''.$key.'\')"/>
                                </div>
                            </label>
                        </li>
                        ';

                }

            ?>
            </ul>

            <button type="submit" class="btn btn-danger" id="submitDeleteButton" style="display:none;">Elimina Selezionati</button>

        </form>


        <div id="popup" class="popup"></div>
        <div style="width: 100%; text-align: center;">
            <div id="miniMap" class="map"></div>
        </div>
        
        <script>

            let search = () => {
                let tags = $('#filter').val()
                let regex = /^\w+(,\w+)*$/
                let validated = regex.test(tags)
                if (!validated){
                    alert('Tags search is not well formatted')
                    return
                }
                location.href = location.protocol + '//' + location.host + location.pathname + '?tags=' + tags
            }

            let dataInfo = JSON.parse(`
            <?php
                $apiUrl = 'localhost/api/ComputerVision/'.$userName.'/positions';
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $apiUrl,
                    CURLOPT_RETURNTRANSFER => true 
                ]);
                $apiResult = curl_exec($curl);
                $resCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                if ($resCode == 200)
                    echo $apiResult;
            ?>`)
            dataArray = [];
            for (let key in dataInfo) {
                dataArray.push({name: key, position: dataInfo[key]})
            }


            var Turin = ol.proj.fromLonLat([7.667129335409262, 45.07799857283038]);
            var view = new ol.View({
                center: Turin,
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
            
            dataArray.forEach(element => {
                places.push(element)
            });

            var features = [];
            for (var i = 0; i < places.length; i++) {
                if (places[i].position[0] == null)
                    continue;
                var iconFeature = new ol.Feature({
                    geometry: new ol.geom.Point(ol.proj.transform([places[i].position[0], places[i].position[1]], 'EPSG:4326', 'EPSG:3857')),
                    imageName: places[i].name 
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


            popupOverlay = new ol.Overlay({
                element: popup,
                offset: [10, 10]
            });
            map.addOverlay(popupOverlay);

            var selectHover = new ol.interaction.Select({
                style: iconStyle,
                condition: ol.events.condition.pointerMove
            });
            var selectClick = new ol.interaction.Select({
                style: iconStyle
            });
            map.addInteraction(selectHover);
            map.addInteraction(selectClick);
            selectHover.on('select', function(e) {
                let selectedFeature = e.selected[0];
                let element = popupOverlay.element;
                if (!selectedFeature){
                    element.hidden = true;
                    document.body.style.cursor = ''
                    return;
                }
                document.body.style.cursor = 'pointer'
                element.hidden = false;
                let coordinatePopup = selectedFeature.N.geometry.A
                console.log(selectedFeature)
                element.innerHTML = '<div class="popup">'+selectedFeature.N.imageName+'</div>'
                popupOverlay.setPosition(coordinatePopup);
            });
            selectClick.on('select', function(e){
                let imageName = e.selected[0].N.imageName
                window.location = location.protocol + '//' + location.host + '/webPage/image.php?name=' + imageName
            })

        </script>

    
    </div>

</body>
</html>