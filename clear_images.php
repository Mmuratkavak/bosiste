<?php
$db = new PDO('sqlite:database/database.sqlite');
$db->exec('UPDATE business_profiles SET logo_path = NULL, cover_image_path = NULL, gallery = NULL');
echo "All image fields cleared.\n";
