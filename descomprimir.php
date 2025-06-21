<?php

require 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;

$connectionString = getenv("AZURE_STORAGE_CONNECTION_STRING");
$sourceContainer = "comprimits";
$targetContainer = "descomprimits";

$blobClient = BlobRestProxy::createBlobService($connectionString);

// Opcional: Obtenir el primer arxiu ZIP del contenedor
$options = new ListBlobsOptions();
$options->setPrefix(""); // Sense prefix específic
$blobs = $blobClient->listBlobs($sourceContainer, $options);

foreach ($blobs->getBlobs() as $blob) {
    if (strtolower(pathinfo($blob->getName(), PATHINFO_EXTENSION)) !== "zip") {
        continue;
    }

    echo "Processant ZIP: " . $blob->getName() . PHP_EOL;

    // Descarregar el ZIP
    $zipContent = $blobClient->getBlob($sourceContainer, $blob->getName())->getContentStream();
    $tempZip = tempnam(sys_get_temp_dir(), 'zip');
    file_put_contents($tempZip, stream_get_contents($zipContent));

    $zip = new ZipArchive();
    if ($zip->open($tempZip) === TRUE) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            $fileContent = $zip->getFromIndex($i);
            if ($fileContent !== false) {
                $uploadOptions = new CreateBlockBlobOptions();
                $blobClient->createBlockBlob($targetContainer, $entry, $fileContent, $uploadOptions);
                echo "Extret i pujat: $entry" . PHP_EOL;
            }
        }
        $zip->close();
    } else {
        echo "Error obrint el ZIP: " . $blob->getName() . PHP_EOL;
    }

    unlink($tempZip);
    break; // Només processem el primer ZIP per a  aquest exemple
}

?>
