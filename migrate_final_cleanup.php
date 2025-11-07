<?php
// Végleges takarító szkript: Eltávolít minden felesleges GID oszlopot

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

// Az összes felesleges oszlop, amit törölni fogunk
$columns_to_drop = [
    "gid_shopifyproductid",
    "gid_shopifyvariantid",
    "gid_shopifyinventoryid",
    "gid_shopifyinventorytid" // Ezt is belevesszük, hátha mégis létezik
];

$all_successful = true;

foreach ($columns_to_drop as $column) {
    $sql = "ALTER TABLE `shopifyproducts` DROP COLUMN `$column`;";
    echo "Parancs futtatása: $sql <br>";

    if ($conn->query($sql) === TRUE) {
        echo "✅ SIKER: A '$column' oszlop sikeresen eltávolítva.<br>";
    } else {
        if (strpos($conn->error, "Can't DROP") !== false || strpos($conn->error, "Unknown column") !== false) {
             echo "ℹ️ INFORMÁCIÓ: A '$column' oszlop már nem létezik. Nincs teendő.<br>";
        } else {
             echo "❌ HIBA: " . $conn->error . "<br>";
             $all_successful = false;
        }
    }
}

if ($all_successful) {
    echo "<br>✅✅✅ MINDEN FELESLEGES OSZLOP SIKERESEN ELTÁVOLÍTVA! Az adatbázis tiszta. ✅✅✅<br>";
}

$conn->close();
?>
