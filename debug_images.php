<?php
$db = new PDO('sqlite:database/database.sqlite');
foreach ($db->query('SELECT id, logo_path, cover_image_path FROM business_profiles') as $row) {
    echo 'ID=' . $row['id'] 
        . ' logo=' . var_export($row['logo_path'], true) 
        . ' cover=' . var_export($row['cover_image_path'], true) 
        . PHP_EOL;
}
