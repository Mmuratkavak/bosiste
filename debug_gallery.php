<?php
$db = new PDO('sqlite:database/database.sqlite');
foreach ($db->query('SELECT id, gallery FROM business_profiles') as $row) {
    echo 'ID=' . $row['id'] . ' gallery=' . var_export($row['gallery'], true) . PHP_EOL;
}
