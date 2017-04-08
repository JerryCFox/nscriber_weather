<?



$lat=array();
$lon=array();

//florida lat lons by 2
array_push($lat,30);
array_push($lon,-86);

array_push($lat,30);
array_push($lon,-84);

array_push($lat,30);
array_push($lon,-82);

array_push($lat,28);
array_push($lon,-83);

array_push($lat,28);
array_push($lon,-81);

array_push($lat,26);
array_push($lon,-82);

array_push($lat,26);
array_push($lon,-80);
//florida done;

$timenow=time();
$lasttime=filemtime("radar26_-80.gif");
//print $timenow-$lasttime;

if($timenow-$lasttime>1200){
//if(1){
    //gather radar map for each of the coordinants.
    for($i=0;$i<sizeof($lat);$i++){
        //print ($lat[$i]);
        $filestring="";
        $filestring.="http://api.wunderground.com/api/84f8aefc34f3904c/radar/satellite/image.gif?";
        $filestring.="rad.maxlat=";
        $filestring.=($lat[$i]+1);
        $filestring.="&rad.maxlon=";
        $filestring.=($lon[$i]+1);
        $filestring.="&rad.minlat=";
        $filestring.=($lat[$i]-1);
        $filestring.="&rad.minlon=";
        $filestring.=($lon[$i]-1);
        $filestring.="&rad.width=2500";
        $filestring.="&rad.height=2500";
        $filestring.="&sat.maxlat=";
        $filestring.=($lat[$i]+1);
        $filestring.="&sat.maxlon=";
        $filestring.=($lon[$i]+1);
        $filestring.="&sat.minlat=";
        $filestring.=($lat[$i]-1);
        $filestring.="&sat.minlon=";
        $filestring.=($lon[$i]-1);
        $filestring.="&sat.width=2500";
        $filestring.="&sat.height=2500";
        $filestring.="&sat.gtt=109";
        $filestring.="&sat.key=sat_vis";
        $filestring.="&sat.proj=me";
        
        //print $filestring."\n";
        $im=file_get_contents($filestring);
        $save = file_put_contents("radar".($lat[$i])."_".($lon[$i]).".gif",$im);
    }
};

print "<script> var rlat=".json_encode($lat)."; var rlon=".json_encode($lon).";</script>";


?>


<!DOCTYPE html>
<html>
  <head>
    <title>Weather Map</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <script>
    
var woverlay;
var weatherOverlay;
USGSOverlay.prototype=new google.maps.OverlayView();

var orlando={};
orlando.lat=28.4158;
orlando.lon=-81.2989;

var map;
function initialize() {
  var mapOptions = {
    zoom: 8,
    center: new google.maps.LatLng(orlando.lat, orlando.lon),
    //mapTypeId: google.maps.MapTypeId.SATELLITE
  };
  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
      
  
 for(var i=0;i<rlon.length;i++){
     var swBound = new google.maps.LatLng(rlat[i]-1, rlon[i]-1);
      var neBound = new google.maps.LatLng(rlat[i]+1, rlon[i]+1);
      var bounds = new google.maps.LatLngBounds(swBound, neBound);
     srcImage="./radar"+rlat[i]+"_"+rlon[i]+".gif";
     woverlay = new USGSOverlay(bounds, srcImage, map);
 }
  
}

google.maps.event.addDomListener(window, 'load', initialize);

function USGSOverlay(bounds, image, map) {

  // Initialize all properties.
  this.bounds_ = bounds;
  this.image_ = image;
  this.map_ = map;

  // Define a property to hold the image's div. We'll
  // actually create this div upon receipt of the onAdd()
  // method so we'll leave it null for now.
  this.div_ = null;

  // Explicitly call setMap on this overlay.
  this.setMap(map);
}

USGSOverlay.prototype.onAdd = function() {

  var div = document.createElement('div');
  div.style.borderStyle = 'none';
  div.style.borderWidth = '0px';
  div.style.position = 'absolute';

  // Create the img element and attach it to the div.
  var img = document.createElement('img');
  img.src = this.image_;
  img.style.width = '100%';
  img.style.height = '100%';
  img.style.position = 'absolute';
  div.appendChild(img);

  this.div_ = div;

  // Add the element to the "overlayLayer" pane.
  var panes = this.getPanes();
  panes.overlayLayer.appendChild(div);
};

USGSOverlay.prototype.draw = function() {

  // We use the south-west and north-east
  // coordinates of the overlay to peg it to the correct position and size.
  // To do this, we need to retrieve the projection from the overlay.
  var overlayProjection = this.getProjection();

  // Retrieve the south-west and north-east coordinates of this overlay
  // in LatLngs and convert them to pixel coordinates.
  // We'll use these coordinates to resize the div.
  var sw = overlayProjection.fromLatLngToDivPixel(this.bounds_.getSouthWest());
  var ne = overlayProjection.fromLatLngToDivPixel(this.bounds_.getNorthEast());

  // Resize the image's div to fit the indicated dimensions.
  var div = this.div_;
  div.style.left = sw.x + 'px';
  div.style.top = ne.y + 'px';
  div.style.width = (ne.x - sw.x) + 'px';
  div.style.height = (sw.y - ne.y) + 'px';
  div.style.opacity = 0.7;
  //div.style['-webkit-filter'] = "blur(1px)";
};

// The onRemove() method will be called automatically from the API if
// we ever set the overlay's map property to 'null'.
USGSOverlay.prototype.onRemove = function() {
  this.div_.parentNode.removeChild(this.div_);
  this.div_ = null;
};


    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
  </body>
</html>
