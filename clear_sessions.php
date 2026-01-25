<?php
$db = new PDO('sqlite:database/database.sqlite');
$db->exec('DELETE FROM sessions');
echo "Sessions cleared.\n";
