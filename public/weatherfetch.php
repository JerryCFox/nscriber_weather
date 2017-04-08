#!/user/local/bin/php

<?
//WEATHER UNDERGROUND METROPOLITAN AREA AND RADAR TILE FETCHER
date_default_timezone_set('America/New_York');
$timenow=time();

$sqlenv=filemtime(dirname(__FILE__) .DIRECTORY_SEPARATOR.'..'. DIRECTORY_SEPARATOR ."local_nscriber_weather_configs".DIRECTORY_SEPARATOR."sqlenv.confg");
$sqlenv=json_decode($sqlenv,true);

$weatherenv=filemtime(dirname(__FILE__) .DIRECTORY_SEPARATOR.'..'. DIRECTORY_SEPARATOR ."local_nscriber_weather_configs".DIRECTORY_SEPARATOR."weather.confg");
$weatherenv=json_decode($weatherenv,true);

//Metro
$lasttime=filemtime(dirname(__FILE__) . DIRECTORY_SEPARATOR ."metro.confg");
if($timenow-$lasttime>150){
        $string2=file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR ."metro.confg");
        $settings=json_decode($string2,true);
        $metroindex=intval($settings['index']);
        
        //lockexec
        $save=file_put_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR ."metro.confg",json_encode($settings));
        
       
        $string3=file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR ."metro.json");
        $citys=json_decode($string3,true);
        
        echo "connecting to database\n";
        mysql_connect($sqlenv["ip"], $sqlenv["username"],$sqlenv["password"]);
        mysql_select_db($sqlenv["database"]) or die("Unable to select database");
        echo "connected to database\n";
        
        echo "Fetching Conditions ".date('m/d/Y H:i:s', $timenow)."\n";
        for($i=0;$i<$settings['rate'];$i++){
            echo "saving ";
            echo $citys[$metroindex]['MPA'];
            echo "\n";
            $metroindex++;
            if($metroindex>=count($citys)){
                $metroindex=0;
            }
            //call wu
            $metrostring="";
            $metrostring.="http://api.wunderground.com/api/".$weatherenv['key']."/conditions/alerts/q/";
            $metrostring.=$citys[$metroindex]['lat'];
            $metrostring.=",";
            $metrostring.=$citys[$metroindex]['lon'];
            $metrostring.=".json";
            $json=file_get_contents($metrostring);
            $json=json_decode($json);
            $try=mysql_query("REPLACE INTO conditions (title, lat, lon, time, data) 
                            VALUES ('".$citys[$metroindex]['MPA']."',
                                    '".round($citys[$metroindex]['lat'],6)."',
                                    '".round($citys[$metroindex]['lon'],6)."',
                                    '".date("Y-m-d H:i:s",$timenow)."',
                                    '".json_encode($json)."'
                            )"
            );
            if($try){
                echo "Successfully added to DB\n";
            }
            else{
                echo "Error adding to DB\n";
            }
            //echo (json_encode($json));
            //echo "\n";
        }
        mysql_close();
        $settings['index']=$metroindex;
        echo "Conditions Fetched \n";
        $save=file_put_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR ."metro.confg",json_encode($settings));
}
else{
    echo("Rest Conditions\n");
};

//TILES
$lasttime=filemtime(dirname(__FILE__) . DIRECTORY_SEPARATOR ."tile.confg");
if($timenow-$lasttime>1200){
    $string=file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR ."tile.confg");
    $tsettings=json_decode($string,true);
    $tileindex=intval($tsettings['index']);
    
    //lockexec
    $save=file_put_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR ."tile.confg",json_encode($tsettings));
    
    $string1=file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR ."tile.json");
    $tiles=json_decode($string1,true);
    
    echo "Fetching Tiles ".date('m/d/Y H:i:s', $timenow)."\n";
    for($i=0;$i<$tsettings['rate'];$i++){
        $filestring="";
        $filestring.="http://api.wunderground.com/api/".$weatherenv['key']."/radar/satellite/image.gif?";
        $filestring.="rad.maxlat=";
        $filestring.=($tiles[$tileindex]['lat']+1);
        $filestring.="&rad.maxlon=";
        $filestring.=($tiles[$tileindex]['lon']+1);
        $filestring.="&rad.minlat=";
        $filestring.=($tiles[$tileindex]['lat']-1);
        $filestring.="&rad.minlon=";
        $filestring.=($tiles[$tileindex]['lon']-1);
        $filestring.="&rad.width=2500";
        $filestring.="&rad.height=2500";
        $filestring.="&sat.maxlat=";
        $filestring.=($tiles[$tileindex]['lat']+1);
        $filestring.="&sat.maxlon=";
        $filestring.=($tiles[$tileindex]['lon']+1);
        $filestring.="&sat.minlat=";
        $filestring.=($tiles[$tileindex]['lat']-1);
        $filestring.="&sat.minlon=";
        $filestring.=($tiles[$tileindex]['lon']-1);
        $filestring.="&sat.width=2500";
        $filestring.="&sat.height=2500";
        $filestring.="&sat.gtt=109";
        $filestring.="&sat.key=sat_vis";
        $filestring.="&sat.proj=me";
        
        //print $filestring."\n";
        $im=file_get_contents($filestring);
        echo ("saving tile".($tiles[$tileindex]['lat'])."_".($tiles[$tileindex]['lon'])."\n");
        $save = file_put_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR ."radar".($tiles[$tileindex]['lat'])."_".($tiles[$tileindex]['lon']).".gif",$im);
        exec("convert ".dirname(__FILE__) . DIRECTORY_SEPARATOR ."radar".($tiles[$tileindex]['lat'])."_".($tiles[$tileindex]['lon']).".gif PNG32:".dirname(__FILE__) . DIRECTORY_SEPARATOR ."radar".($tiles[$tileindex]['lat'])."_".($tiles[$tileindex]['lon']).".png");
        exec("convert ".dirname(__FILE__) . DIRECTORY_SEPARATOR ."radar".($tiles[$tileindex]['lat'])."_".($tiles[$tileindex]['lon']).".gif -resize 30% PNG32:".dirname(__FILE__) . DIRECTORY_SEPARATOR ."radar".($tiles[$tileindex]['lat'])."_".($tiles[$tileindex]['lon'])."lr.png");
        exec("convert ".dirname(__FILE__) . DIRECTORY_SEPARATOR ."radar".($tiles[$tileindex]['lat'])."_".($tiles[$tileindex]['lon']).".gif -resize 10% PNG32:".dirname(__FILE__) . DIRECTORY_SEPARATOR ."radar".($tiles[$tileindex]['lat'])."_".($tiles[$tileindex]['lon'])."ltr.png");
        $tileindex++;
        if($tileindex>=count($tiles)){
            $tileindex=0;
        }
    }
    $tsettings['index']=$tileindex;
    echo "tiles fetched \n";
    $save=file_put_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR ."tile.confg",json_encode($tsettings));
}
else{
    echo("Rest Tiles \n");
};


?>