<?php
// migrate_01_prepare_database_V3_FINAL.php
// EZ A VÉGLEGES VERZIÓ.
// Létrehoz egy `generated_sku` oszlopot, és azt teszi EGYEDIVÉ (UNIQUE).
// A `variant_sku` (a csoportosító kulcs) sima INDEX lesz.
// A `barcode` egy sima oszlop lesz, kulcs nélkül.

ini_set('max_execution_time', 300);
set_time_limit(300);

echo "<h2>Adatbázis Migrációs Szkript (V3 - Generált SKU Logika)</h2>";

// --- 1. ADATBÁZIS KAPCSOLAT ---
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = (int)getenv('DB_PORT');
$sslmode = getenv('DB_SSLMODE');
$conn = mysqli_init();
if ($sslmode === 'require') { mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false); }
if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("❌ Connection failed: " . mysqli_connect_error());
}
echo "✅ Adatbázis-kapcsolat sikeres.<br>";
mysqli_set_charset($conn, "utf8mb4");

// --- 2. OSZLOPOK HOZZÁADÁSA/MÓDOSÍTÁSA ---
$columns_to_add = [
    "handle" => "VARCHAR(255) NULL",
    "title" => "VARCHAR(255) NULL",
    "body" => "TEXT NULL",
    "vendor" => "VARCHAR(255) NULL",
    "type" => "VARCHAR(255) NULL",
    "tags" => "TEXT NULL",
    "variant_sku" => "VARCHAR(255) NULL AFTER `id`", // Ez a CSOPORTOSÍTÓ KULCS
    "generated_sku" => "VARCHAR(255) NULL AFTER `variant_sku`", // EZ LESZ AZ ÚJ EGYEDI KULCS
    "barcode" => "VARCHAR(255) NULL", // Csak adat, nem kulcs
    "grams" => "INT(11) NULL DEFAULT 0",
    "inventory_tracker" => "VARCHAR(50) NULL",
    "img_src" => "TEXT NULL",
    "img_src_2" => "TEXT NULL",
    "img_src_3" => "TEXT NULL",
    "option1_name" => "VARCHAR(100) NULL",
    "option1_value" => "VARCHAR(100) NULL",
    "option2_name" => "VARCHAR(100) NULL",
    "option2_value" => "VARCHAR(100) NULL",
    "qty_location_1" => "INT(11) NULL DEFAULT 0",
    "qty_location_2" => "INT(11) NULL DEFAULT 0",
    "price_huf" => "DECIMAL(10, 2) NULL DEFAULT 0.00",
    "price_lei" => "DECIMAL(10, 2) NULL DEFAULT 0.00",
    "price_eur" => "DECIMAL(10, 2) NULL DEFAULT 0.00",
    "last_seen_in_feed" => "DATETIME NULL",
    "needs_update" => "TINYINT(2) NOT NULL DEFAULT 2"
];

echo "<h3>1. Oszlopok hozzáadása/módosítása...</h3>";
foreach ($columns_to_add as $column => $definition) {
    $sql = "ALTER TABLE `shopifyproducts` ADD COLUMN `$column` $definition;";
    if ($conn->query($sql) === TRUE) {
        echo "✅ SIKER ('$column')<br>";
    } else {
        if (strpos($conn->error, "Duplicate column name") !== false) echo "ℹ️ INFORMÁCIÓ: A '$column' oszlop már létezik.<br>";
        else echo "❌ HIBA: " . $conn->error . "<br>";
    }
}

// --- 3. INDEXEK HOZZÁADÁSA (A VÉGLEGES LOGIKA SZERINT) ---
echo "<h3>2. Indexek hozzáadása...</h3>";
$indexes_to_add = [
    // A `generated_sku` az EGYEDI kulcs (UNIQUE)
    "idx_generated_sku_unique" => "ALTER TABLE `shopifyproducts` ADD UNIQUE KEY `idx_generated_sku_unique` (`generated_sku`);",
    // A `variant_sku` a CSOPORTOSÍTÓ kulcs (sima INDEX)
    "idx_variant_sku" => "ALTER TABLE `shopifyproducts` ADD INDEX `idx_variant_sku` (`variant_sku`);",
    "idx_needs_update" => "ALTER TABLE `shopifyproducts` ADD INDEX `idx_needs_update` (`needs_update`);"
];

foreach ($indexes_to_add as $name => $sql) {
    echo "Index futtatása: `$name` ... ";
    if ($conn->query($sql) === TRUE) {
        echo "✅ SIKER<br>";
    } else {
         if (strpos($conn->error, "Duplicate key name") !== false) echo "ℹ️ INFORMÁCIÓ: Az '$name' index már létezik.<br>";
         else echo "❌ HIBA: " . $conn->error . "<br>";
    }
}

echo "<h2>✅ Adatbázis-séma sikeresen előkészítve (V3 - Generált SKU)!</h2>";
$conn->close();
?>
