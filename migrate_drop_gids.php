<?php
// Ideiglenes szkript a felesleges GID oszlopok eltávolításához

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

// 2. Az SQL parancsok futtatása (Oszlopok eltávolítása)
$sql1 = "ALTER TABLE `shopifyproducts` DROP COLUMN `gid_shopifyproductid`;";
$sql2 = "ALTER TABLE `shopifyproducts` DROP COLUMN `gid_shopifyvariantid`;";

echo "Parancs 1 futtatása: $sql1 <br>";
if ($conn->query($sql1) === TRUE) {
    echo "✅ SIKER: A 'gid_shopifyproductid' oszlop sikeresen eltávolítva.<br>";
} else {
    if (strpos($conn->error, "Can't DROP") !== false) {
         echo "ℹ️ INFORMÁCIÓ: A 'gid_shopifyproductid' oszlop már nem létezik.<br>";
    } else {
         echo "❌ HIBA: " . $conn->error . "<br>";
    }
}

echo "Parancs 2 futtatása: $sql2 <br>";
if ($conn->query($sql2) === TRUE) {
    echo "✅ SIKER: A 'gid_shopifyvariantid' oszlop sikeresen eltávolítva.<br>";
} else {
    if (strpos($conn->error, "Can't DROP") !== false) {
         echo "ℹ️ INFORMÁCIÓ: A 'gid_shopifyvariantid' oszlop már nem létezik.<br>";
    } else {
         echo "❌ HIBA: " . $conn->error . "<br>";
    }
}

$conn->close();
?>
