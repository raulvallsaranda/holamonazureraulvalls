<?php
require 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

// Configuració
$connectionString = getenv("AZURE_STORAGE_CONNECTION_STRING");
$containerName = "comprimits";  // Canvia açò pel nom del teu contenidor

$blobClient = BlobRestProxy::createBlobService($connectionString);

// Eliminar arxiur si se sol·licita
if (isset($_GET['delete'])) {
    $blobToDelete = $_GET['delete'];
    try {
        $blobClient->deleteBlob($containerName, $blobToDelete);
        echo "<p style='color:green;'>Arxiu $blobToDelete eliminat correctament.</p>";
    } catch (ServiceException $e) {
        echo "<p style='color:red;'>Error en eliminar: " . $e->getMessage() . "</p>";
    }
}

// Pujada d'arxiu ZIP
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["zipfile"])) {
    $uploadedFile = $_FILES["zipfile"];
    echo $uploadedFile["type"];
    if ($uploadedFile["type"] !== "application/zip" ||$uploadedFile["type"] !== "application/x-zip-compressed") {
        echo "<p style='color:red;'>Només es permeten arxius ZIP.</p>";
    } else {
        $blobName = basename($uploadedFile["name"]);
        $content = fopen($uploadedFile["tmp_name"], "r");

        try {
            $blobClient->createBlockBlob($containerName, $blobName, $content);
            echo "<p style='color:green;'>Arxiu $blobName pujat correctament.</p>";
        } catch (ServiceException $e) {
            echo "<p style='color:red;'>Error en pujar: " . $e->getMessage() . "</p>";
        }
    }
}

// Llistar arxius
try {
    $listOptions = new ListBlobsOptions();
    $blobList = $blobClient->listBlobs($containerName, $listOptions);
    $blobs = $blobList->getBlobs();
} catch (ServiceException $e) {
    die("Error en llistar arxius: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestor d'archivos ZIP en Azure Blob</title>
</head>
<body>
    <h1>Arxius ZIP en el contenidor '<?= htmlspecialchars($containerName) ?>'</h1>
    <ul>
        <?php foreach ($blobs as $blob): ?>
            <li>
                <a href="<?= htmlspecialchars($blob->getUrl()) ?>" target="_blank">
                    <?= htmlspecialchars($blob->getName()) ?>
                </a>
                [<a href="?delete=<?= urlencode($blob->getName()) ?>" onclick="return confirm('Eliminar aquest arxiu?')">Eliminar</a>]
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Pujar nou arxiu ZIP</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="zipfile" accept=".zip" required>
        <button type="submit">Pujar</button>
    </form>
</body>
</html>
