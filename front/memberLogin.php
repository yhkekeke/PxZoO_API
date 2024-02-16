<?php 
/**
 * 前台會員登陸接口
 * $_SESSION 參考網站:https://ithelp.ithome.com.tw/articles/10207241
 * CORS 參考網站:https://blog.huli.tw/2021/02/19/cors-guide-3/
 * 
 * http://localhost/cgd103_g1/public/api/getConfirmMember.php?mem_account=charmy222@gmail.com&mem_psw=charmy222
 * http://localhost/cgd103_g1/public/api/getConfirmMember.php?mem_account=charmy333@gmail.com&mem_psw=charmy333
*/
ini_set("display_errors", "On");//php偵錯

header("Access-Control-Allow-Origin: *"); // 允許所有來源
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // 開發環境
    require_once("../pxzoo/connectPxzoo.php");
} else {
    // 生產環境
    require_once("https://tibamef2e.com/chd104/g4/api/connectPxzoo.php");
}




$login_account = empty( $_GET["mem_acc"] ) ? ( $_POST["mem_acc"] ?? "" ) : $_GET["mem_acc"];
$login_psw = empty( $_GET["mem_psw"] ) ? ( $_POST["mem_psw"] ?? "" ) : $_GET["mem_psw"];

if($login_account != "" && $login_psw != "") {
    $sql = " SELECT * FROM member WHERE mem_acc = '{$login_account}' OR mem_psw = '{$login_psw}'";
    $result = $pdo->query($sql);
    $resArray = $result->fetch(PDO::FETCH_ASSOC);
    $mem_psw = $resArray["mem_psw"]??"";
    if($mem_psw == $login_psw) {
        $nowTime = time();
        session_start();
        $_SESSION = $resArray;
        $result_array = ["code"=>"1", "msg"=>"登陸成功",'memInfo'=>$_SESSION,'session_id'=>session_id()];
        echo json_encode($result_array);
    }
    else {
        $result_array = ["code"=>"0", "msg"=>"帳號或密碼錯誤"];
        echo json_encode($result_array);
    }
}

?>