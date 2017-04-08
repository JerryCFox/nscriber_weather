var http = require("http");
var path = require("path");
var url = require("url");
var static = require('node-static');//this initializes the node-static modules for memory cashed html files
var fs = require('fs'); //standard file module
var runner = require("child_process");
var file = new(static.Server)('./public');
var fileprotect=require("./fileprotect");
var iptracer=require("./iptracer");

//create options to start an http server
var options={};
var banlist=[];
	//no options to specify at the moment
//make server the name of a http server
var demsvr=require('http').createServer(function (request, response) {
    //add basic response
    request.addListener('end', function () {
      //at the end of end continue
	    //for every file requested via http, pass this request to the static server "file"
	    //so long as the file is not a php file, if so then you need to execute the php file.
      var urlpath = url.parse(request.url).pathname;
      var ext=urlpath.split('.');
      ext=ext[ext.length-1];
      if(banlist.indexOf(request.headers['x-forwarded-for'].substr(0, request.headers['x-forwarded-for'].lastIndexOf(".")))==-1){
        if(ext=='php'){
          iptracer.request(request,function(iptrace){
            console.log(iptrace.geoplugin_request+" is from "+iptrace.geoplugin_city+" "+iptrace.geoplugin_region+" "+iptrace.geoplugin_countryName);
            if(iptrace.geoplugin_countryName=="China"){
              console.log("banning "+iptrace.geoplugin_request);
              banlist.push(iptrace.geoplugin_request.substr(0, iptrace.geoplugin_request.lastIndexOf(".")));
            }
          });
          //console.log(request.url);
          var param = url.parse(request.url).query;
          var localpath = path.join(process.cwd()+"/public", urlpath);
          var now=new Date();
          console.log(request.headers['x-forwarded-for']+"\n"+now.toString()+"\n"+ localpath);
          fs.exists(localpath, function(result) {
            runScript(result, localpath, param, response);
          });
        }
        else{
          fileprotect.check(request,function(auth){
            var localpath = path.join(process.cwd()+"/public", urlpath);
            if(ext!="png"&&ext!="jpg"&&ext!="js"&&ext!="css"&&ext!="gif"&&ext!="PNG"&&ext!="woff"){
              var now=new Date();
              console.log(request.headers['x-forwarded-for']+"\n"+now.toString()+"\n"+ localpath);
              iptracer.request(request,function(iptrace){
                console.log(iptrace.geoplugin_request+" is from "+iptrace.geoplugin_city+" "+iptrace.geoplugin_region+" "+iptrace.geoplugin_countryName);
                if(iptrace.geoplugin_countryName=="China"){
                  console.log("banning "+iptrace.geoplugin_request);
                  banlist.push(iptrace.geoplugin_request.substr(0, iptrace.geoplugin_request.lastIndexOf(".")));
                }
              });
            }
            if(auth){
              file.serve(request, response);
            }
            else{
              sendError(404, 'You are Unauthorized', response);
            }
          });
        }
      }
      else{
        console.log("blocked a china attack");
      }
    }).resume();
});
demsvr.listen(8085);
console.log("Server Thinks its ready!")


setInterval(function(){
  console.log("Running script");
   runner.exec("php ./public/weatherfetch.php",{maxBuffer: 20000 * 1024},
   function(err, stdout, stderr) { 
     console.log(stdout);
   });
},30000)


function runScript(exists, file, param, response)
{
  if(!exists) return sendError(404, 'File not found', response);
  runner.exec("php " + file + " '" + param +"'",{maxBuffer: 20000 * 1024},
   function(err, stdout, stderr) { 
     sendData(err, stdout, stderr, response); 
     //console.log(stdout);
   });
}


function sendError(errCode, errString, response)
{
  response.writeHead(errCode, {"Content-Type": "text/plain;charset=utf-8"});
  response.write(errString + "\n");
  response.end();
  return false;
}

function sendData(err, stdout, stderr, response)
{
  if (err) return sendError(500, stderr, response);
  response.writeHead(200,{"Content-Type": "text/html;charset=utf-8"});
  //response.writeHead(200);
  response.write(stdout);
  response.end();
}


