//IP TRACER using geoplugin
//Written by Gerald Condon 8/19/2015

//HOW TO USE THIS MODULE
module.exports.request=strip_ip_from_request_then_trace;
module.exports.trace=tracebyip;

//Internal Settings
var url="http://www.geoplugin.net/json.gp?ip=";

//INCLUDES MODULES
var request = require("request");

function strip_ip_from_request_then_trace(req,callback){
    var ip=req.headers['x-forwarded-for'];
    if(ip.indexOf(":")!=-1){
        console.log("ipv6 traceback not supported.");
    }
    else{
        tracebyip(ip,callback);
    }
}

function tracebyip(ip,callback){
    request(
        {
            url: url+ip,
            json: true
        },
        function (error, response, body) {
            if (!error && response.statusCode === 200) {
                callback(body);
            }
        }
    );
}

