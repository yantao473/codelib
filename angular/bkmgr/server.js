var express = require('express');

var app = express();

app.use('/', express.static(__dirname + '/app'));
// app.all('*', function(req, res, next){
    // console.log('in');
    // next();
// });
// 
// app.get('/', function (req, res) {
	// // 连接共享型MySQL
	// var connection = mysql.createConnection({
		// host     : process.env.MYSQL_HOST,
		// port     : process.env.MYSQL_PORT,
		// user     : process.env.ACCESSKEY,
		// password : process.env.SECRETKEY,
		// database : 'app_' + process.env.APPNAME
	// });

	// connection.query('show status', function(err, rows) {
		// if (err) {
			// res.send(err);
			// return;
		// }

		// res.send(rows);
	// });

	// connection.end();
// });

app.post('/dologin', function(req, res){
    // if(req.name === 'yanqing4' && req.passwd === 'abc'){
        res.json({'status': '0', 'msg': 'Login success'});
    // }
});

app.listen(5050, function(){
    console.log('listen port 5050');
});
