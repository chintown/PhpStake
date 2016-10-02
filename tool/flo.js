var flo = require('fb-flo'),
    fs = require('fs'),
    path = require('path'),
    exec = require('child_process').exec;

var ROOT = process.argv[2];
//console.log(process.argv);
var server = flo(ROOT, {
  port: 8888,
  glob: ['script/less/*.less']
}, resolver);

server.once('ready', function() {
  console.log('Ready!');
});

function resolver(filepath, callback) {
    var targetFile = path.basename(filepath, '.less')+'.css';
    var webInput = 'css/'+targetFile;
    var lessInput = ROOT+'/'+filepath;
    var lessOutput = ROOT+'/htdoc/css/'+targetFile;
    var cmd = '/usr/local/bin/lessc -x --no-color --include-path=/home/chintown/proj/php/PhpStake/script/less/ '+lessInput+' > '+lessOutput;
    //console.log(cmd);
    exec(cmd, function (err, stdout) {
      if (err) {
          throw err;
      }
      callback({
        resourceURL: webInput,
        contents: fs.readFileSync(lessOutput).toString()
      })
    });
}
