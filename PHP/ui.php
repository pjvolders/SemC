<?php
/*
* Semonto Code user interface
*
* Written by Pieter-Jan Volders - pjvolders.com
*
* Version 1
*
*/




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Semonto</title>
<link href="http://www.semonto.com/css/styles.css" rel="stylesheet" type="text/css" />
<link href="layout.css" rel="stylesheet" type="text/css"></link>
<!--[if IE]><script language="javascript" type="text/javascript" src="http://historycenter.semonto.com/excanvas.pack.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="http://historycenter.semonto.com/jquery.js"></script>
<script language="javascript" type="text/javascript" src="http://historycenter.semonto.com/jquery.flot.js"></script>
<link rel="alternate" type="application/rss+xml" title="Logbook RSS Feed" href="http://www.semonto.com/rss/?h=153&s=04462e35911c937099690713dbcff328" />

</head>
<body id="status">
<div id="header">
	<div class="wrapper">
		<h1><a href="http://www.semonto.com/index" title="Semonto">Semonto</a></h1>
	</div>
</div>
<div id="contents">
	<div class="wrapper">
	
			
		<h2>Local statistics</h2>
		<div id="overview">The current page will automatically refresh with new statistics</div>
		
		<div id="main-content" class="full">
			<div class="box">

				<div class="heading">
					<h3>Current load: <span id="load"></span></h3>
					<div id="service">Service: Load</div>
				</div>
				<div id="graph" style="width:100%;height:300px;"></div>

			</div>
		</div>

		

	</div>
</div>
<div id="footer">
<div class="wrapper">
	<p>© Copyright <a href="http://www.codingmammoth.com">CodingMammoth</a>. All Rights Reserved. <a href="http://semonto.com/service">Terms of service</a> | <a href="http://semonto.com/privacy">Privacy policy</a> | <a href="http://www.semonto.com/contact">Contact us</a></p></div>
</div>
<script type="text/javascript">
	var timeout = 5000;
	var global_data = {};

	global_data.load = [];
	global_data.load[0] = { data: [], label: 'load-now' }; //load-now
	global_data.load[1] = { data: [], label: 'load-5m' }; //load-5m
	global_data.load[2] = { data: [], label: 'load-15m' }; //load-15m

	time_loop = function(callback) {
		setTimeout(function() {
			callback();
			time_loop(callback);
		}, timeout);
	}

	// do_ajax_test = function(test, succes_function) {
	// 	$.ajax({
	// 		url: 'index.php?test=' + test,
	// 		dataType: 'text',
	// 		error: function(e) {
	// 			alert(e);
	// 		},
	// 		success: function(data) {
	// 			//alert(data);
	// 			succes_function(data);
	// 		}
	// 	});
	// }

	// auto_refresh_test = function(test, succes_function) {

	// 	aa = do_ajax_test(test, succes_function);

	// 	time_loop( aa );

	// }

	var plot = $.plot($("#graph"), global_data.load, { 
		lines: { show: true },
		xaxis: { mode: "time" }, 
		points: { show: true },
		grid: { backgroundColor: { colors: ["#fff", "#eee"] } }
	});

	function plot_refresh() {
		plot.setData( global_data.load );
		plot.setupGrid()
		plot.draw();
	}

	// auto_refresh_test( 'load-now', function(data) {
	// 	var resp = data.split('::');
	// 	//$('#load').append( resp[2] + '<br/>' );
	// 	$('#load').html( resp[2] );

	// 	if ( global_data.length > 25 ) { global_data.shift(); }

	// 	var yval = resp[2];
	// 	var milliseconds = new Date().getTime();
	// 	global_data.push([milliseconds, yval]);

	// 	plot.setData([ global_data ]);
	// 	plot.setupGrid()
	// 	plot.draw();

	// });
	
	time_loop(function() {
		$.ajax({
			url: 'index.php?test=load-now',
			dataType: 'text',
			error: function(e) {
				alert(e);
			},
			success: function(data) {
				//alert(data);
				var resp = data.split('::');
				//$('#load').append( resp[2] + '<br/>' );
				$('#load').html( resp[2] );

				if ( global_data.load[0].data.length > 25 ) { global_data.load[0].data.shift(); }

				var yval = resp[2];
				var milliseconds = new Date().getTime();
				global_data.load[0].data.push([milliseconds, yval]);

				plot_refresh();

			}
		});
		$.ajax({
			url: 'index.php?test=load-5m',
			dataType: 'text',
			success: function(data) {
				var resp = data.split('::');

				if ( global_data.load[1].data.length > 25 ) { global_data.load[1].data.shift(); }

				var yval = resp[2];
				var milliseconds = new Date().getTime();
				global_data.load[1].data.push([milliseconds, yval]);

				plot_refresh()

			}
		});
		$.ajax({
			url: 'index.php?test=load-15m',
			dataType: 'text',
			success: function(data) {
				var resp = data.split('::');

				if ( global_data.load[2].data.length > 25 ) { global_data.load[2].data.shift(); }

				var yval = resp[2];
				var milliseconds = new Date().getTime();
				global_data.load[2].data.push([milliseconds, yval]);

				plot_refresh()
			}
		});
	});



	
</script>
</body>
</html>