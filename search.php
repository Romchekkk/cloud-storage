<?php

require_once "JsHttpRequest/JsHttpRequest.php";
require_once "dataBaseClass.php";
require_once "additionalFunctions.php";

session_start();

$js = new JsHttpRequest("utf-8");

global $_RESULT;
$mysql = new dataBase();
if(!$mysql->isConnect()){
    $_RESULT["error"] = true;
    die();
}

$forSearch = isset($_REQUEST['forSearch'])
    ? trim($_REQUEST['forSearch'])
    : "";
    
$usersList = "";
$usersArr = $mysql->getUsersForSearch($forSearch);
foreach($usersArr as $user){
    $accessRights = checkAccessRights($mysql, "localStorage/".$user['username'], $_SESSION['username']);
    if($accessRights === 0 || $accessRights === 1 || $accessRights === 2){
        $usersList .= "<li class=\"open\"><input type=\"button\" value=\"".$user['username']."\" onclick=\"openUser(this.value)\" /></li>";
    }
    else{
        $usersList .= "<li class=\"close\">".$user['username']."</li>";
    }
}
$_RESULT["error"] = false;
$_RESULT['usersList'] = $usersList;