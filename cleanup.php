<?php
// cleanup.php
echo "Adatbázis tisztítása indul...\n";

// Adatbázis kapcsolat
$conn = mysqli_connect(
    getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'),
    getenv('DB_NAME'), getenv('DB_PORT') ?: 3306
);
if (!$conn) { die("Kapcsolati hiba: " . mysqli_connect_error()); }

$sql = "UPDATE shopifyproducts 
        SET needs_update = 0 
        WHERE (variant_sku IS NULL OR variant_sku = '') 
        AND needs_update = 2";

if ($conn->query($sql) === TRUE) {
    echo "Sikeres tisztítás. Érintett sorok: " . $conn->affected_rows . "\n";
} else {
    echo "Hiba a tisztítás közben: " . $conn->error . "\n";
}
$conn->close();
echo "Kész.\n";
?>
