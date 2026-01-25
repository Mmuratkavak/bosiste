<?php

// İlk medya dosyasının URL'ini test et
$db = new PDO('sqlite:database/database.sqlite');
$stmt = $db->query('SELECT path FROM media_files LIMIT 1');
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $path = $result['path'];
    echo "Path: $path\n\n";
    
    $fullPath = "storage/app/public/$path";
    echo "Full Path: $fullPath\n";
    echo "Dosya var mı? " . (file_exists($fullPath) ? 'EVET' : 'HAYIR') . "\n\n";
    
    $publicPath = "public/storage/$path";
    echo "Public Path: $publicPath\n";
    echo "Public'te var mı? " . (file_exists($publicPath) ? 'EVET' : 'HAYIR') . "\n\n";
    
    echo "URL: /storage/$path\n";
}
