<?php header('Access-Control-Allow-Origin: *'); ?>
<?
if (!isset($_SERVER["HTTP_HOST"])) {
  parse_str($argv[1], $_GET);
  parse_str($argv[1], $_POST);
}
if (isset($_GET['lat']) && isset($_GET['lon']) ) {
    $lat=$_GET['lat'];
    $lon=$_GET['lon'];
}
else {
    die("Please supply coordinents");   
}
if (isset($_GET['rad'])){
    $rad=$_GET['rad'];
}
else {
    $rad=2;
}

$sqlenv=filemtime(dirname(__FILE__) .DIRECTORY_SEPARATOR.'..'. DIRECTORY_SEPARATOR ."local_nscriber_weather_configs".DIRECTORY_SEPARATOR."sqlenv.confg");
$sqlenv=json_decode($sqlenv,true);

mysql_connect($sqlenv["ip"], $sqlenv["username"],$sqlenv["password"]);
mysql_select_db($sqlenv["database"]) or die("Unable to select database");

$query="SELECT * FROM conditions
                        WHERE (lat BETWEEN ".($lat-$rad)." AND ".($lat+$rad).")
                        AND (lon BETWEEN ".($lon-$rad)." AND ".($lon+$rad).")
                        ";
//echo $query;
$result = mysql_query($query);
mysql_close();

$return=array();
while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
     $row['data']=json_decode($row['data']);
     array_push($return, $row);
}
echo json_encode($return);


?>












