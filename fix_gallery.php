<?php
$db = new PDO('sqlite:database/database.sqlite');
$db->exec('UPDATE business_profiles SET gallery = NULL');
