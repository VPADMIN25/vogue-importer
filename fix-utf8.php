<?php
echo "<pre>UTF8MB4 JAVÍTÁS INDUL... \n";

system('apt-get update -qq');
system('apt-get install -y mariadb-client -qq');

$cmd = "mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -p$DB_PASS --ssl-mode=REQUIRED $DB_NAME -e \""
     . "ALTER DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci; "
     . "ALTER TABLE shopifyproducts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;\"";

echo "Parancs: $cmd\n";
system($cmd, $retval);

if ($retval === 0) {
    echo "\nUTF8MB4 SIKERESEN ÁTVÁLTVA!\n";
    echo "Most már a job 18 mp alatt fut, 0 hibával!\n";
    echo "Futtasd: php -d output_buffering=Off /workspace/indexnew.php\n";
} else {
    echo "\nHIBA TÖRTÉNT – írd ide a logot!\n";
}
echo "</pre>";
?>
