<?php

require_once "JsHttpRequest/JsHttpRequest.php";
require_once "dataBaseFunctions.php";

session_start();

$js = new JsHttpRequest("utf-8");

$forSearch = isset($_REQUEST['forSearch'])
? trim($_REQUEST['forSearch'])
: "";

$ini = parse_ini_file("database/mysql.ini");
$mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
$usersList = "";
$result = mysqli_query($mysql, "SELECT `username` FROM `users` WHERE INSTR(`username`, '$forSearch')=1 ORDER BY `users`.`username` ASC");
$i = 0;
while($row = mysqli_fetch_array($result)){
    if ($i == 15){
        break;
    }
    $accessRights = checkAccessRights($mysql, "localStorage/".$user['username'], $_SESSION['username']);
    if($accessRights === 0 || $accessRights === 1 || $accessRights === 2){
        $usersHTML .= "<li class=\"open\"><input type=\"button\" value=\"".$user['username']."\" onclick=\"openUser(this.value)\" /></li>";
    }
    else{
        $usersHTML .= "<li class=\"close\">".$user['username']."</li>";
    }
    $i++;
}
mysqli_close($mysql);
global $_RESULT;
$_RESULT['usersList'] = $usersList;