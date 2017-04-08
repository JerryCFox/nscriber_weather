var fs=require('fs');


var tilemap=[];
var nogorec=[];

var tileobject={
    lat:null,
    lon:null
};

var USALATTOP=50;
var USALATBOT=25;
var USALONRIGHT=-65;
var USALONLEFT=-125;


//now define functions to remove squares or triangles
function nogo(NWlon,NWlat,SElon,SElat){
    for(var i=SElon;i>=NWlon;i--){
        for(var j=NWlat;j>=SElat;j--){
            titleobject={};
            titleobject['lon']=i;
            titleobject['lat']=j;
            console.log("removing:("+i+","+j+")");
            nogorec.push(titleobject);
        }
    }
}

//by maine
nogo(-70,50,-65,48);
nogo(-75,50,-70,48);
nogo(-80,50,-75,45);
//by greatlakes
nogo(-85,50,-80,48);
//under maine
nogo(-70,43,-65,40);
//big square from newyork to bermuda
nogo(-75,40,-65,25);
//right of florida
nogo(-79,32,-75,25);
//left of florida
nogo(-95,28,-84,25);
//left of texas to california
nogo(-126,30,-105,25);
//just left of texas
nogo(-105,27,-100,25);
//left of california
nogo(-126,36,-122,30);
nogo(-122,34,-118,30);
nogo(-118,31,-110,30);

//add usa basesquare
for(var i=USALONRIGHT;i>=USALONLEFT;i--){
    i--;
    for(var j=USALATTOP;j>=USALATBOT;j--){
        j--;
        tileobject={};
        tileobject['lon']=i;
        tileobject['lat']=j;
        kill=0;
        for(var k=0;k<nogorec.length;k++){
            if(tileobject['lon']==nogorec[k]['lon']&&tileobject['lat']==nogorec[k]['lat']){
                kill=1;
            }
        }
        if(kill==0){
            tilemap.push(tileobject);
            console.log("pushing ("+i+","+j+")");
        }
    }
}

console.log(tilemap.length);
outputfilename2="tile.json";
fs.writeFile(outputfilename2, JSON.stringify(tilemap), function(err) {
    if(err) {
      console.log(err);
    } else {
      console.log("JSON saved to " + outputfilename2);
    }
}); 
