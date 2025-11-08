<?php
// migrate_03_add_timestamps.php
// Cél: Hozzáadja a hiányzó created_at és updated_at oszlopokat.

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

$columns_to_add = [
    "created_at" => "DATETIME NULL",
    "updated_at" => "DATETIME NULL"
];

$all_successful = true;

foreach ($columns_to_add as $column => $definition) {
    $sql = "ALTER TABLE `shopifyproducts` ADD COLUMN `$column` $definition;";
    echo "Parancs futtatása: `$sql` ... ";

    if ($conn->query($sql) === TRUE) {
        echo "✅ SIKER: A '$column' oszlop sikeresen hozzáadva.<br>";
    } else {
        if (strpos($conn->error, "Duplicate column name") !== false) {
             echo "ℹ️ INFORMÁCIÓ: A '$column' oszlop már létezik.<br>";
        } else {
             echo "❌ KRITIKUS HIBA: " . $conn->error . "<br>";
             $all_successful = false;
        }
    }
}

if ($all_successful) {
    echo "<h2>✅ Időbélyeg (timestamps) oszlopok sikeresen hozzáadva.</h2>";
}
$conn->close();
?>
