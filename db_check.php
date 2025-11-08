<?php
// db_check.php – DB ELLENŐRZÉS
echo "<pre style='font-family:Consolas;font-size:14px'>";
echo "<h2>DB ELLENŐRZÉS – UTOLSÓ 10 SOR</h2>";

$env = [
    'DB_HOST' => getenv('DB_HOST'),
    'DB_USER' => getenv('DB_USER'),
    'DB_PASS' => getenv('DB_PASS'),
    'DB_NAME' => getenv('DB_NAME'),
    'DB_PORT' => (int)getenv('DB_PORT')
];

$conn = @mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME'], $env['DB_PORT']);
if (!$conn) die("MySQL hiba: " . mysqli_connect_error());
mysqli_set_charset($conn, "utf8mb4");

$sql = "SELECT 
    generated_sku, price_huf, grams, barcode, 
    option1_value, option2_value, img_src, tags, 
    qty_location_1, needs_update 
    FROM shopifyproducts 
    ORDER BY id DESC LIMIT 10";

$result = $conn->query($sql);
if (!$result) die("SQL hiba: " . $conn->error);

echo "<table border='1' cellpadding='5' cellspacing='0' style='font-size:12px'>";
echo "<tr style='background:#000;color:#0f0'>
    <th>SKU</th><th>Ár</th><th>Súly(g)</th><th>Barcode</th>
    <th>Opt1</th><th>Opt2</th><th>Kép</th><th>Tags</th>
    <th>Készlet1</th><th>needs_update</th>
</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['generated_sku']) . "</td>";
    echo "<td>" . $row['price_huf'] . "</td>";
    echo "<td>" . $row['grams'] . "</td>";
    echo "<td>" . $row['barcode'] . "</td>";
    echo "<td>" . htmlspecialchars($row['option1_value']) . "</td>";
    echo "<td>" . htmlspecialchars($row['option2_value']) . "</td>";
    echo "<td><a href='" . $row['img_src'] . "' target='_blank'>Link</a></td>";
    echo "<td>" . htmlspecialchars($row['tags']) . "</td>";
    echo "<td>" . $row['qty_location_1'] . "</td>";
    echo "<td><b>" . $row['needs_update'] . "</b></td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
echo "<h3>KÉSZ – Futtasd: <code>php /workspace/db_check.php</code></h3></pre>";
?>
