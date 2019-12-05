<?php

require_once "JsHttpRequest/JsHttpRequest.php";
require_once "dataBaseClass.php";

session_start();

$js = new JsHttpRequest("utf-8");

global $_RESULT;
$mysql = new dataBase();
if(!$mysql->isConnect()){
    $_RESULT["error"] = true;
    die();
}

foreach (array('forSearch', 'filename', 'isRoot') as $parameterName) {
    $$parameterName = isset($_REQUEST[$parameterName])
        ? trim($_REQUEST[$parameterName])
        : "";
}

$file = $isRoot
    ? "localStorage/".$_SESSION['username']
    : $_SESSION['path']."/$filename";
if (!file_exists($file)){
    $_RESULT["error"] = true;
    exit();
}
$isRoot = $isRoot ? "true" : "false";
$usersList = "<table><tbody>";
$usersArr = $mysql->getUsersForSearch($forSearch);
$sharedaccess = $mysql->getFileAccessInfo($file)['sharedaccess'];
foreach($usersArr as $user){
    $id = $mysql->getUserId($user['username']);
    if (preg_match("/\/$id\//", $sharedaccess)){
        $usersList .= "<tr><td><input type=\"checkbox\" checked=\"checked\" value=\"".$user['username']."\" onclick=\"check(this.value, $isRoot)\" /></td><td><span>".$user['username']."</span></td></tr>\n";
    }
    else{
        $usersList .= "<tr><td><input type=\"checkbox\" value=\"".$user['username']."\" onclick=\"check(this.value, $isRoot)\" /></td><td><span>".$user['username']."</span></td></tr>\n";
    }
}
$_RESULT["error"] = false;
$_RESULT['usersList'] = $usersList."</tbody></table>";