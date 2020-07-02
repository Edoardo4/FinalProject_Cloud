<?php
$apiUrl = 'localhost/api/Shared/'.$_GET['userName'].'/'.$_GET['imageName'];
$apiFields = array(
    "expirationDate" => $_POST['expirationDate']
);
$apiData = json_encode($apiFields);

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_POSTFIELDS => $apiData,
    CURLOPT_CUSTOMREQUEST => "PATCH",
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

echo 'Link immagine: '.$_SERVER['HTTP_HOST']. '/webPage/image.php?userName=' .$_GET['userName'].'&name='.$_GET['imageName'];

