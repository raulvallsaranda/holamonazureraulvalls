<?php
// Recuperar variables d'entorn
$dbHost = getenv('DB_HOST');
$dbName = "prova";         
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASSWORD');

if (!$dbHost || !$dbUser || $dbPass === false) {
    throw new \RuntimeException('Falten variables d\'entorn per a la connexió a la base de dades.');
}

// DSN amb charset utf8mb4
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

try {
    $options = [
        // Excepcions en errors
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Fetch com a  array associatiu
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Desactivar emulació de prepares
        PDO::ATTR_EMULATE_PREPARES   => false,

        // Assegurar la connexió TLS cap a  Azure Database for MySQL
        PDO::MYSQL_ATTR_SSL_CA        => '/etc/ssl/certs/BaltimoreCyberTrustRoot.crt.pem',
        // Desactivem la validació del certificat SSL
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];

    // Crear la connexió PDO
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);

    // Exemple: consulta sencilla
    $stmt = $pdo->query('SELECT NOW() AS data_actual;');
    $fila = $stmt->fetch();
    echo "Connectat correctament. Hora del servidor: " . $fila['data_actual'];
} catch (PDOException $e) {
    error_log('Error de connexió PDO: ' . $e->getMessage());
    echo "Error al conectar amb la base de dades: " . htmlspecialchars($e->getMessage());
    exit;
}
