<?php
// Ideiglenes szkript a 'shopifyproducts' tábla javításához
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
$sql = "ALTER TABLE `shopifyproducts` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY;";
echo "Parancs futtatása: $sql <br>";
if ($conn->query($sql) === TRUE) {
    echo "✅ SIKER: A `shopifyproducts` tábla `id` oszlopa sikeresen beállítva AUTO_INCREMENT-re.<br>";
} else {
    if (strpos($conn->error, "Invalid column definition") !== false || strpos($conn->error, "check the manual") !== false) {
         echo "ℹ️ INFORMÁCIÓ: Az oszlop már helyesen van beállítva (AUTO_INCREMENT). Nincs teendő.<br>";
    } else {
         echo "❌ HIBA: " . $conn->error . "<br>";
    }
}
$conn->close();
?>
