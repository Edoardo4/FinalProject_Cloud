<?php

chdir(dirname(__DIR__));
require_once 'vendor/autoload.php';
use MyClasses\BlobStorageManager;

$userName = $_POST["accountName"];
$blobStorageManager = new BlobStorageManager();

foreach (array_keys($_POST["Images"]) as $imageName) {

    $returnCode = $blobStorageManager->DeleteBlob($userName, $imageName);
    if ($returnCode != 0){
        http_response_code(403);
        header("Location: /webPage");
    }

    $apiUrl = 'localhost/api/ComputerVision/'.$userName.'/'.$imageName;
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_RETURNTRANSFER => true 
    ]);
    $apiResult = curl_exec($curl);
    $resCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($returnCode != 200){
        http_response_code(403);
        header("Location: /webPage");
    }

}

http_response_code(200);
header("Location: /webPage");
