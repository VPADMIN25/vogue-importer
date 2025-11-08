<?php
echo "TISZTÍTÁS INDUL...\n";
$conn = mysqli_connect(
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_NAME'),
    getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306
);
if (!$conn) die("HIBA: " . mysqli_connect_error());

$sql = "UPDATE shopifyproducts SET needs_update = 0 
        WHERE needs_update = 2 
          AND (variant_sku IS NULL OR variant_sku = '' OR title IS NULL OR title = '')";

if ($conn->query($sql)) {
    echo "SIKER! " . $conn->affected_rows . " hibás sor javítva.\n";
} else {
    echo "HIBA: " . $conn->error . "\n";
}
$conn->close();
echo "KÉSZ! Most futtasd: php /workspace/index2new.php\n";
