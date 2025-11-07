<?php
// Ideiglenes szkript az adatbázis-tábla módosításához

// 1. Csatlakozás az adatbázishoz (a legutóbb javított kód)
$host = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASS");
$dbname = getenv("DB_NAME");
$port = (int)getenv("DB_PORT");
$sslmode = getenv("DB_SSLMODE"); // "REQUIRED"

$conn = mysqli_init();

// Ez a kulcs: Beállítjuk az SSL-t, de NEM ellenőrizzük a tanúsítványt
if ($sslmode === "require") {
    mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
}

// Csatlakozás a mysqli_real_connect segítségével, SSL flag-et kényszerítve
if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("❌ Connection failed (VPC SSL Handshake Failed): " . mysqli_connect_error());
}
echo "✅ Adatbázis-kapcsolat sikeres.<br>";
mysqli_set_charset($conn, "utf8");

// 2. Az SQL parancs futtatása
$sql = "ALTER TABLE `products` ADD `sku_group` VARCHAR(255) NULL DEFAULT NULL AFTER `Handle`, ADD INDEX (`sku_group`);";

echo "Parancs futtatása: $sql <br>";

if ($conn->query($sql) === TRUE) {
    echo "✅ SIKER: Az adatbázis-tábla sikeresen módosítva (sku_group oszlop hozzáadva).<br>";
} else {
    // Ellenőrizzük, hogy a hiba az-e, hogy már létezik
    if (strpos($conn->error, "Duplicate column name") !== false) {
         echo "ℹ️ INFORMÁCIÓ: A \"sku_group\" oszlop már létezik. Nincs teendő.<br>";
    } else {
         echo "❌ HIBA: " . $conn->error . "<br>";
    }
}

$conn->close();
?>
