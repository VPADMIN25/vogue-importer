<?php
// migrate_03_final_cleanup.php
// Cél: Eltávolítja a konfliktust okozó régi Shopify ID és User ID oszlopokat.

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
mysqli_set_charset($conn, "utf8mb4");

$columns_to_drop = [
    "productid", // Régi rövid Shopify ID
    "variantid", // Régi rövid Shopify ID
    "user_id",   // Redundáns (hardkódoltuk, hogy 1)
    "dateofmodification" // Redundáns/régi nyomon követés
];

$all_successful = true;

foreach ($columns_to_drop as $column) {
    $sql = "ALTER TABLE `shopifyproducts` DROP COLUMN `$column`;";
    echo "Parancs futtatása: `$sql` ... ";

    if ($conn->query($sql) === TRUE) {
        echo "✅ SIKER: A '$column' oszlop sikeresen ELTÁVOLÍTVA.<br>";
    } else {
        if (strpos($conn->error, "Can't DROP") !== false) {
             echo "ℹ️ INFORMÁCIÓ: A '$column' oszlop már nem létezik.<br>";
        } else {
             echo "❌ KRITIKUS HIBA: " . $conn->error . "<br>";
             $all_successful = false;
        }
    }
}

if ($all_successful) {
    echo "<h2>✅ Végső SCHEMA takarítás sikeres. A konfliktusok elhárítva.</h2>";
}
$conn->close();
?>
