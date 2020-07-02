<?php
namespace MyClasses;

chdir(dirname(__DIR__));
require_once 'vendor/autoload.php';

use DateTime;
use DateInterval;
use Exception;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
use MicrosoftAzure\Storage\Blob\BlobSharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Common\Internal\Resources;

class BlobStorageManager {

    private $connectionString = "DefaultEndpointsProtocol=https;AccountName=;AccountKey=;EndpointSuffix=";      //Insert here api connection string
    private $blobClient;

    public function __construct(){

        $this->blobClient = BlobRestProxy::createBlobService($this->connectionString);

    }

    public function isShared($userName, $imageName){
        $apiUrl = 'localhost/api/Shared/'.$userName.'/'.$imageName;
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true 
        ]);
        $apiResult = curl_exec($curl);
        $resCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $res = json_decode($apiResult, true);
        curl_close($curl);
        if ($resCode != 200){
            return null;
        }
        
        return $res["expired"];
    }

    public function generateBlobDownloadLinkWithSAS(string $username, string $imageName) {
        $username = strtolower($username);

        $expirationDate = new DateTime();
        $expirationDate->add(new DateInterval('PT2M'));

        $settings = StorageServiceSettings::createFromConnectionString($this->connectionString);
        $accountName = $settings->getName();
        $accountKey = $settings->getKey();

        $helper = new BlobSharedAccessSignatureHelper(
            $accountName,
            $accountKey
        );

        // Refer to following link for full candidate values to construct a service level SAS
        // https://docs.microsoft.com/en-us/rest/api/storageservices/constructing-a-service-sas
        $sas = $helper->generateBlobServiceSharedAccessSignatureToken(
            Resources::RESOURCE_TYPE_BLOB,
            "$username/$imageName",
            'r',                            // Read
            $expirationDate->format('Y-m-d\TH:i:s\Z')     // A valid ISO 8601 format expiry time
            //'2016-01-01T08:30:00Z',       // A valid ISO 8601 format expiry time
            //'0.0.0.0-255.255.255.255'
            //'https,http'
        );

        $connectionStringWithSAS = Resources::BLOB_ENDPOINT_NAME .
            '='.
            'https://' .
            $accountName .
            '.' .
            Resources::BLOB_BASE_DNS_NAME .
            ';' .
            Resources::SAS_TOKEN_NAME .
            '=' .
            $sas;

        $blobClientWithSAS = BlobRestProxy::createBlobService(
            $connectionStringWithSAS
        );

        // We can download the blob with PHP Client Library
        // downloadBlobSample($blobClientWithSAS);

        // Or generate a temporary readonly download URL link
        $blobUrlWithSAS = sprintf(
            '%s%s?%s',
            (string)$blobClientWithSAS->getPsrPrimaryUri(),
            "$username/$imageName",
            $sas
        );

        return $blobUrlWithSAS;
    }
    

    public function GetBlobListByUsername (string $username){

        $username = strtolower($username);
        $listBlobsOptions = new ListBlobsOptions();
        $Blobs = array();

        do{
            $result = $this->blobClient->listBlobs($username, $listBlobsOptions);
            foreach ($result->getBlobs() as $blob) {

                $blobName = $blob->getName();
                $blobUrl = $blob->getUrl();
                $Blobs[$blobName] = $blobUrl;

            }
            $listBlobsOptions->setContinuationToken($result->getContinuationToken());
        } while($result->getContinuationToken());
        
        return $Blobs;

    }

    public function UploadBlob(string $username,string $fileName, string $pathFileToUpload){

        $username = strtolower($username);
        try {
            // Check if container name exists
            $this->blobClient->getContainerProperties($username);
        }
        catch(ServiceException $e){
        
            try{
                $createContainerOptions = new CreateContainerOptions();
                $this->blobClient->createContainer($username, $createContainerOptions);
            }
            catch(ServiceException $e){
                return $e->getCode();
            }
            
        }
        
        $content = fopen($pathFileToUpload, "r");
        //Upload blob
        $this->blobClient->createBlockBlob($username, $fileName, $content);

        return 0;

    }

    public function DeleteBlob(string $username, string $fileName){

        $username = strtolower($username);

        try{
            $this->blobClient->deleteBlob($username, $fileName);
        }
        catch(Exception $e){
            return $e->getCode();
        }

        return 0;

    }

}
