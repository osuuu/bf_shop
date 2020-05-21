<?php
error_reporting(0);

define('IN_CRONLITE', true);

define('ROOT', dirname(__FILE__).'/');

define('TEMPLATE_ROOT', ROOT.'/template/');

require ROOT.'version.php';

session_start();


// header('Content-Type: text/html; charset=utf-8');
// header('Access-Control-Allow-Origin:*');
// header('Access-Control-Allow-Method:POST,GET');//允许访问的方式 　
// header('Access-Control-Allow-Headers:x-requested-with,content-type'); 

date_default_timezone_set("PRC");

$date = date("Y-m-d H:i:s");



// if(is_file(ROOT.'360safe/360webscan.php')){//360网站卫士

//     require_once(ROOT.'360safe/360webscan.php');

// }



require ROOT.'config.php';

if(!$dbconfig['host'] || !$dbconfig['port'] || !$dbconfig['user'] || !$dbconfig['pwd'] || !$dbconfig['dbname']){
	echo(json_encode(array("status" => 404)));
	exit();
}



if(!isset($port))$port='3306';
$dbname=$dbconfig['dbname'];
$host=$dbconfig['host'];
$pwd=$dbconfig['pwd'];
$user=$dbconfig['user'];
$dsn = "mysql:dbname=$dbname;host=$host";
// $user = $user;
$password = $pwd;
try
{
    $dbh = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
}catch(PDOException $e)
{
    echo '数据库连接失败' . $e->getMessage();
}

$resn = $dbh->prepare("select * from bf_config where id =1");
$resn->execute();
$title = $resn->fetch(PDO::FETCH_ASSOC);

$resp = $dbh->prepare("select * from bf_pay where id =1");
$resp->execute();
$conf = $resp->fetch(PDO::FETCH_ASSOC);

$password_hash='!@#%!s!0';
include_once(ROOT."function.php");



