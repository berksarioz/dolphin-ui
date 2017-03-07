<?php
//error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('report_errors','on');

require_once("../../config/config.php");
require_once("../../includes/dbfuncs.php");
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$query = new dbfuncs();
if (isset($_GET['debug'])){$debug = $_GET['debug'];}
if (isset($_GET['p'])){$p = $_GET['p'];}



if ($p == 'getExperimentData')
{

	$sql = "";
}
else if ($p == 'getImportData')
{
	$sql = "";
}
else if ($p == 'getSampleData')
{
	$sql = "";
}
$data=$query->queryTable("$sql");

if (!headers_sent()) {
   header('Cache-Control: no-cache, must-revalidate');
   header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
   header('Content-type: application/json');
   if ($debug == "yes")
	echo $sql."\n\n";
   echo $data;
   exit;
}else{
   echo $data;
}
?>
