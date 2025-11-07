<?php
// Ez a szkript "megröntgenezi" a 'shopifyproducts' tábla szerkezetét.

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
echo "✅ Adatbázis-kapcsolat sikeres.<br><br>";
mysqli_set_charset($conn, "utf8");

// 2. Az SQL parancs futtatása (A tábla szerkezetének lekérdezése)
$sql = "SHOW CREATE TABLE `shopifyproducts`;";

echo "<b>Parancs futtatása:</b> $sql <br><br>";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "--- A 'shopifyproducts' TÁBLA SZERKEZETE ---<br>";
    echo "<pre>"; // A <pre> tag segít, hogy a szöveg formázott maradjon
    print_r($row['Create Table']); // Ez kiírja a teljes CREATE TABLE parancsot
    echo "</pre>";
    echo "<br>--- VÉGE ---";
} else {
    echo "❌ HIBA: Nem sikerült lekérdezni a tábla szerkezetét. " . $conn->error . "<br>";
}

$conn->close();
?>
