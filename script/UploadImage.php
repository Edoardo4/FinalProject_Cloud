<?php

chdir(dirname(__DIR__));
require_once 'vendor/autoload.php';

// var_dump($_FILES["imageToUpload"]);
// $totalImages = count($_FILES['imageToUpload']['name']);
// echo "<br>";
// echo 'totale immagini: '.$totalImages.'<br>';

// for ($i = 0; $i < $totalImages; $i++){
//     if(!preg_match('/^image\/?/', $_FILES["imageToUpload"]["type"][$i])){
//         echo json_encode('"Error" : "The file \"'. $_FILES["imageToUpload"]["name"][$i] .'\" is not an image"');
//         http_response_code(202);
//     }
//     echo 'Image "'. $_FILES["imageToUpload"]["name"][$i] .'" is in local '. $_FILES["imageToUpload"]["tmp_name"][$i] . '<br>';
// }

// exit;

use MyClasses\BlobStorageManager;

$userName = strtolower($_POST["accountName"]);

#   Modifica upload_max_filesize, post_max_size del php.ini e del client_max_body_size del web server
if($_FILES["imageToUpload"]["type"] === ""){
    echo json_encode('"Error":"File size is too large"');
    http_response_code(400);
    header("Location: /webPage");
}

if(!preg_match('/^image\/?/', $_FILES["imageToUpload"]["type"])){
    http_response_code(403);
    header("Location: /webPage");
    exit;
}

// if ($_POST){
//     var_dump($_POST);
//     exit;
//     foreach(array_keys($_POST) as $c)
//         echo $c . "<br>";
//         exit;
// }

$blobStorageManager = new BlobStorageManager();

if ($blobStorageManager->UploadBlob($userName, $_FILES["imageToUpload"]["name"], $_FILES["imageToUpload"]["tmp_name"]) > 0){
    http_response_code(403);
    header("Location: /webPage");
    exit;
}

$apiUrl = 'localhost/api/ComputerVision/'.$userName;
$apiFields = array(
    "image_name" => $_FILES["imageToUpload"]["name"],
    "image_tmp" => $_FILES["imageToUpload"]["tmp_name"],
    "image_url" => $blobStorageManager->generateBlobDownloadLinkWithSAS($userName, $_FILES["imageToUpload"]["name"])
);
$apiData = json_encode($apiFields);

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_POSTFIELDS => $apiData,
    CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
    CURLOPT_RETURNTRANSFER => true 
]);
$apiResult = curl_exec($curl);
$resCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($resCode != 200){
    http_response_code(403);
    header("Location: /error");
}

http_response_code(200);
header("Location: /webPage");

