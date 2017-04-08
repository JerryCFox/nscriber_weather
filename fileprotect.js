//FILEPROTECT MODULE WRITEN BY GERALD CONDON

//CHECKS IF EXT is authorized
module.exports.check=check;

//INCLUDES
var path = require("path");
var url = require("url");

//FUNCTIONS
function check(request,callback){
      var urlpath = url.parse(request.url).pathname;
      var ext=urlpath.split('.');
      ext=ext[1];
      
      switch(ext){
          case "f"://fortran
            callback(0);
            break;
          case "f90"://fortran90
            callback(0);
            break;
          case "sh"://bash
            callback(0);
            break;
          default:
            callback(1);
      }
}
