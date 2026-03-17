<?php
$pdo = new PDO('mysql:host=localhost;dbname=sample_hr;charset=utf8mb4','root','', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$tables = ['employees','departments','grievances','events','feedback','recognitions','announcements','event_registrations'];
foreach($tables as $t){ $c = $pdo->query("select count(*) as c from $t")->fetch(); echo "$t: {$c['c']}\n"; }
