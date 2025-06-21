<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="utf-8">
    <title>Hola món</title>
    <link rel="stylesheet" href="estils.css">
</head>

<body>
    <div id="wrapper">
        <header>
            <h1>Hola món Azure</h1>
        </header>
        <main>
            <p>
                <?php
                echo "Hola Món PHP Azure App Services!<br>";
                echo "Raül Valls Aranda";
                ?>
            </p>
            <p>
                <a href="./storage.php">Emmagatzematge en contenidors</a><br>
                <a href="./connexio.php">Prova connexió amb Base de dades AZURE mysql</a>
            </p>
        </main>
        <footer>
            <p>Curs Azure Cefire 2025</p>
        </footer>
    </div>
</body>

</html>