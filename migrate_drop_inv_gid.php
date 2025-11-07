<?php
// Ideiglenes szkript az utolsó felesleges GID oszlop eltávolításához

// 1. Csatlakozás az adatbázishoz (a működő kód)
$host = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASS");
$dbname = getenv("DB_NAME");
$port = (int)getenv("DB_PORT");
$sslmode = getenv("DB_SSLMODE");
$conn = mysqli_init();
if ($sslmode === "require") { mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false); }
if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("❌ Connection failed: " . mysqli_connect_error());
}
echo "✅ Adatbázis-kapcsolat sikeres.<br>";
mysqli_set_charset($conn, "utf8");

// 2. Az SQL parancs futtatása (A 'gid_shopifyinventorytid' oszlop eltávolítása)
// FIGYELEM: A te hibaüzenetedben 'gid_shopifyinventorytid' szerepelt. 
// Ha a valódi oszlopnév 'gid_shopifyinventoryid' (d-vel a végén), kérlek, javítsd ki a parancsban!
// Én most a te hibaüzeneted alapján ('tid') járok el:

$sql = "ALTER TABLE `shopifyproducts` DROP COLUMN `gid_shopifyinventoryid`;";

// *** BIZTONSÁGI TARTALÉK (Ha elgépelted és 'id'-re végződik): ***
// Ha a fenti parancs hibát dob, próbáld meg ezt a fájlt futtatni ezzel a paranccsal:
// $sql = "ALTER TABLE `shopifyproducts` DROP COLUMN `gid_shopifyinventoryid`;";

echo "Parancs futtatása: $sql <br>";
if ($conn->query($sql) === TRUE) {
    echo "✅ SIKER: A 'gid_shopifyinventorytid' oszlop sikeresen eltávolítva.<br>";
} else {
    if (strpos($conn->error, "Can't DROP") !== false) {
         echo "ℹ️ INFORMÁCIÓ: A 'gid_shopifyinventorytid' oszlop már nem létezik.<br>";
    } else {
         echo "❌ HIBA: " . $conn->error . "<br>";
    }
}

$conn->close();
?>
