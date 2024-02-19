<?php 

ini_set("display_errors", "On");//php偵錯

header("Access-Control-Allow-Origin: *"); // 允許所有來源
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // 開發環境
    require_once("connectPxzoo.php");
} else {
    // 生產環境
    require_once("connect_chd104g4.php");
}

session_start();

$login_account = empty($_GET["sta_acc"]) ? ($_POST["sta_acc"] ?? "") : $_GET["sta_acc"];
$login_psw = empty($_GET["sta_psw"]) ? ($_POST["sta_psw"] ?? "") : $_GET["sta_psw"];

if($login_account != "" && $login_psw != "") {
    $sql = "SELECT * FROM staff WHERE sta_acc = :login_account OR sta_psw = :login_psw";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':login_account' => $login_account,
        ':login_psw' => $login_psw
    ));
    $resArray = $stmt->fetch(PDO::FETCH_ASSOC);
    $mem_psw = $resArray["sta_psw"] ?? "";
    
    if($mem_psw == $login_psw) {
        $_SESSION['staInfo'] = $resArray;
        $result_array = ["code" => "1", "msg" => "登陸成功", 'staInfo' => $_SESSION['staInfo'], 'session_id' => session_id()];
        echo json_encode($result_array);
    }
    else {
        http_response_code(401); // 未授權
        $result_array = ["code" => "0", "msg" => "帳號或密碼錯誤"];
        echo json_encode($result_array);
    }
}
?>
