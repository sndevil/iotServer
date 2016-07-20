<?
ini_set("display_errors",1);
error_reporting(E_ALL);
$dbuser="fceu_18558357";//_user
$dbpass="iot2016";
$dbname="fceu_18558357_db";
$dbhost="sql111.freecluster.eu";
function dbConnect(){
 global $dbuser,$dbpass,$dbname,$dbhost;
 $db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
 return $db;
}
function dbConnectMysqli(){
 global $dbuser,$dbpass,$dbname,$dbhost;
 $db=mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
 return $db;
}
$db=dbConnectMysqli() or die("salam");
?>