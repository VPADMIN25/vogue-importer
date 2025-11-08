<?php
// fix_db.php
// EGYSZERI JAVÍTÓ SZKRIPT - A 'shopifyproducts' TÁBLA KARAKTERKÓDOLÁSÁNAK JAVÍTÁSÁRA

echo "JAVÍTÓ SZKRIPT INDUL...\n";

// 1. Időlimit kikapcsolása (kritikus, az ALTER TABLE lassú lehet!)
ini_set('max_execution_time', 0);
set_time_limit(0);
ini_set('memory_limit', '1024M'); // Biztonság kedvéért memórialimit növelés

// 2. Adatbázis kapcsolat (ugyanaz, mint az indexnew.php-ban)
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = (int)getenv('DB_PORT');
$sslmode = getenv('DB_SSLMODE');

$conn = mysqli_init();
if ($sslmode === 'require') {
    mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
}

if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("❌ HIBA: A kapcsolódás sikertelen: " . mysqli_connect_error() . "\n");
}

echo "✅ Adatbázis-kapcsolat sikeres ($host).\n";

// Beállítjuk a kapcsolatot, hogy biztosan értsék egymást
mysqli_set_charset($conn, "utf8mb4");

// 3. Az SQL Parancs
$sql_command = "ALTER TABLE shopifyproducts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

echo "ℹ️ FUTTATÁS: $sql_command\n";
echo "Ez eltarthat 1-2 percig is a tábla méretétől függően. Kérlek, várj...\n";

// 4. Parancs végrehajtása
if ($conn->query($sql_command) === TRUE) {
    echo "\n------------------------------------------------------\n";
    echo "✅ SIKER! A 'shopifyproducts' tábla sikeresen átalakítva utf8mb4-re.\n";
    echo "------------------------------------------------------\n";
} else {
    echo "\n------------------------------------------------------\n";
    echo "❌ HIBA! A parancs futtatása sikertelen: " . $conn->error . "\n";
    echo "------------------------------------------------------\n";
}

// 5. Kapcsolat bezárása
$conn->close();
echo "Kapcsolat bezárva. Szkript vége.\n";
?>
