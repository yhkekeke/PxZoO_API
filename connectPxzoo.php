
<?php
    $dbname = "pxzoo";   // 資料庫名稱改為 pxzoo
    $user = "root";
    $password = "";
    $port = 3306;

    $dsn = "mysql:host=localhost;port={$port};dbname=$dbname;charset=utf8";
    $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_CASE => PDO::CASE_NATURAL);

    // 建立 PDO 物件
    $pdo = new PDO($dsn, $user, $password, $options);
?>
