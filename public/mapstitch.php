<?
    set_time_limit ( 600 );
    putenv("MAGICK_MEMORY_LIMIT=1");
    putenv("MAGICK_AREA_LIMIT=30000000");
    putenv("MAGICK_MAP_LIMIT=1");
    function is_image($path)
    {
        $a = getimagesize($path);
        $image_type = $a[2];
         
        if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
        {
            return true;
        }
        return false;
    }

    echo "checking if blank file exists<br>";
    //exec('convert -size 2500x2500 xc:"rgba(0,0,0,0)" PNG32:white.png');
    exec('convert -size 2500x2500 xc:"rgba(255,255,255,1)" -transparent white PNG32:white.png');
    exec('convert PNG32:white.png -resize 30%  PNG32:whitelr.png');
    //if(!file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR ."blank.png")){
    //     exec('convert -size 2500x2500 xc:"rgba(0,0,0,0)" PNG32:blank.png');
    //}
    
    echo "Stitching<br>";
    $string=array();
    for($i=49;$i>=25;$i--){
        for($j=-124;$j<=-66;$j++){
            if(file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR ."radar".$i."_".$j.".gif")){
                 //echo "converting radar".$i."_".$j.".gif<br>";
                 exec("convert radar".$i."_".$j.".gif -resize 10%  PNG32:radar".$i."_".$j."ltr.png");
                 //exec("convert radar".$i."_".$j.".gif -resize 30%  PNG32:radar".$i."_".$j."lr.png");
                 if(file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR ."radar".$i."_".$j."lr.png")){
                     echo "adding radar".$i."_".$j."lr.png<br>";
                     array_push($string,"radar".$i."_".$j."lr.png");
                 }
                 else{
                     array_push($string, "whitelr.png" );
                 }
            }
            else{
                echo "adding blank<br>";
                array_push($string, "whitelr.png" );
            }
            $j++;
        }
        $i--;
    }
    
    echo "Finished<br>";
    $imager=array();
    $images="";
    echo count($string);
    echo "<br>";
    for($i=0;$i<count($string);$i++){
    //for($i=0;$i<30;$i++){
       // echo $string[$i];
       // echo " ";
        $images.="PNG32:";
        $images.=$string[$i];
        $images.=" ";
        if(($i+1)%30==0){
            array_push($imager,$images);
            $images="";
        }
    }
    echo "<hr>";
    putenv("MAGICK_MEMORY_LIMIT=1");
    putenv("MAGICK_AREA_LIMIT=30000000");
    putenv("MAGICK_MAP_LIMIT=1");
    
    echo("</br>");
    for($i=0;$i<count($imager);$i++){
        echo("convert +append ".$imager[$i]." PNG32:mapappend".$i.".png");
        echo("<br>");
        //exec("convert +append ".$imager[$i]." PNG32:mapappend".$i.".png 2>errorappend".$i.".log");
    }
    
    
    echo "Finished<br>";
    
    
    //exec("convert -append PNG32:mapappend0.png PNG32:mapappend1.png PNG32:mapappend2.png PNG32:mapappend3.png PNG32:mapappend4.png PNG32:mapappend5.png PNG32:mapappend6.png PNG32:mapappend7.png PNG32:mapappend8.png PNG32:mapappend9.png PNG32:mapappend10.png PNG32:mapappend11.png PNG32:mapappend12.png PNG32:mapappendfinal.png 2>errorappendfinal.log");
    //exec("convert -append PNG32:mapappend0.png PNG32:mapappend1.png PNG32:mapappendfinal01.png 2>errorappendfinal01.log");
    //exec("convert -append PNG32:mapappend2.png PNG32:mapappend3.png PNG32:mapappendfinal23.png 2>errorappendfinal23.log");
    //exec("convert -append PNG32:mapappend4.png PNG32:mapappend5.png PNG32:mapappendfinal45.png 2>errorappendfinal45.log");
    //exec("convert -append PNG32:mapappend6.png PNG32:mapappend7.png PNG32:mapappendfinal67.png 2>errorappendfinal67.log");
    //exec("convert -append PNG32:mapappend8.png PNG32:mapappend9.png PNG32:mapappendfinal89.png 2>errorappendfinal89.log");
    //exec("convert -append PNG32:mapappend10.png PNG32:mapappend11.png PNG32:mapappendfinal1011.png 2>errorappendfinal1011.log");
    //exec("convert -append PNG32:mapappend0.png PNG32:mapappend12.png PNG32:mapappendfinal01.png 2>errorappendfinal.log");
    
    echo "done<br>";
    
    
    
    
    
    
   
?>