<?php

$db = new PDO('sqlite:database/database.sqlite');
$stmt = $db->query('SELECT id, tenant_id, type, path, thumbnail_path, webp_path FROM media_files LIMIT 5');
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "MediaFile kayıtları:\n\n";
foreach ($results as $row) {
    echo "ID: {$row['id']}\n";
    echo "Tenant: {$row['tenant_id']}\n";
    echo "Type: {$row['type']}\n";
    echo "Path: {$row['path']}\n";
    echo "Thumbnail: {$row['thumbnail_path']}\n";
    echo "WebP: {$row['webp_path']}\n";
    echo "---\n\n";
}

// Dosya var mı kontrol et
if (!empty($results[0]['path'])) {
    $fullPath = 'storage/app/public/' . $results[0]['path'];
    echo "İlk dosya kontrolü: $fullPath\n";
    echo "Var mı? " . (file_exists($fullPath) ? 'EVET' : 'HAYIR') . "\n";
}
