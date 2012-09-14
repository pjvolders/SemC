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
	<!--[if IE]><script language="javascript" type="text/javascript" src="http://historycenter.semonto.com/excanvas.pack.js"></script><![endif]-->
	<!-- <script language="javascript" type="text/javascript" src="http://historycenter.semonto.com/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="http://historycenter.semonto.com/jquery.flot.js"></script> -->
	<script language="javascript" type="text/javascript" src="/drupal/sites/default/modules/jquery_update/replace/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="/jquery.flot.min.js"></script>
	<script language="javascript" type="text/javascript" src="jquery.flot.pie.min.js"></script>
	<style type="text/css">
		.half_graph {
			float: left;
			width: 49%;
		}
		.right {
			float: right;
		}

	</style>
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

				<!-- Load --> 
				<div class="heading">
					<h3>Current load: <span id="load"></span></h3>
					<div id="service">Service: Load</div>
				</div>
				<div id="load_graph" style="width:100%;height:300px;"></div>	

				<div class="half_graph left">
					<!-- PHP Run time -->
					<div class="heading">
						<h3>Service: PHP Run time</h3>
					</div>
					<div id="php_graph" style="width:100%;height:300px;"></div>
				</div>

				<div class="half_graph right">
					<!-- MySQL -->
					<div class="heading">
						<h3>Service: MySQL</h3>
					</div>
					<div id="mysql_graph" style="width:100%;height:300px;"></div>
				</div>

				<div class="half_graph left">
					<!-- Diskspace -->
					<div class="heading">
						<h3>Service: Diskspace</h3>
					</div>
					<div id="diskspace_graph" style="width:100%;height:300px;"></div>
				</div>

				<div class="half_graph right">
					<!-- Quota -->
					<div class="heading">
						<h3>Service: Quota</h3>
					</div>
					<div id="quota_graph" style="width:100%;height:300px;"></div>
				</div>

				<div id="auto_refresh">
					<form>
						Refresh data automatically: <input type="checkbox" id="auto_refresh_toggle" checked /><br />
						Refresh every <input type="text" id="auto_refresh_time" /> seconds.
					</form>
				</div>

			</div>
		</div>

		

	</div>
</div>
<div id="footer">
<div class="wrapper">
	<p>&copy; Copyright <a href="http://www.codingmammoth.com">CodingMammoth</a>. All Rights Reserved. <a href="http://semonto.com/service">Terms of service</a> | <a href="http://semonto.com/privacy">Privacy policy</a> | <a href="http://www.semonto.com/contact">Contact us</a></p></div>
</div>
<script type="text/javascript">
	var timeout = 5000;
	var auto_refresh = true;
	var plot = {}; //hash with all the plots
	var global_data = {};

	global_data.load = [];
	global_data.load[0] = { data: [], color: '#feb490', label: 'load-now' }; //load-now
	global_data.load[1] = { data: [], color: '#ff5f11', label: 'load-5m' }; //load-5m
	global_data.load[2] = { data: [], color: '#9f3400', label: 'load-15m' }; //load-15m

	global_data.php = { data: [], label: 'php-runtime', color: '#0086f8' };

	global_data.mysql = [];
	global_data.mysql[0] = { data: [], color: '#a3d5ff', label: 'mysql-ping' }; //mysql-ping
	global_data.mysql[1] = { data: [], color: '#0086f8', label: 'mysql-select' }; //mysql-select
	global_data.mysql[2] = { data: [], color: '#002f57', label: 'mysql-execTable' }; //mysql-execTable

	global_data.diskspace = [{label: "used", data: 97},{label: "free", data: 3}];

	// auto refresh settings
	$("#auto_refresh_toggle").change(function(){
		if ( $(this).checked ) {
			auto_refresh = true;
		} else {
			auto_refresh = false;
		};
	});
	$("#auto_refresh_time").val( timeout/1000 ).change(function(){
		timeout = $(this).val() * 1000;
	});

	// generic plotting function
	function load_plot_refresh(plot, data) {
		plot.setData( data );
		plot.setupGrid()
		plot.draw();
	}

	// main looping function
	auto_loop_ajax = function(test, success_callback) {
		$.ajax({
			url: 'index.php?test=' + test,
			dataType: 'text',
			success: function(data) {
				var resp = data.split('::INFO::');
				success_callback(resp);
			}
		});
		setTimeout(function() {
			if ( auto_refresh == true ) { auto_loop_ajax(test, success_callback); }
		}, timeout);

	}


	////////////
	// Load plot
	plot.load = $.plot($("#load_graph"), global_data.load, { 
		lines: { show: true },
		xaxis: { mode: "time" }, 
		points: { show: true },
		grid: { backgroundColor: { colors: ["#fff", "#eee"] } }
	});

	auto_loop_ajax( 'load-now', function(resp) {
		//alert(data);
		//$('#load').append( resp[1] + '<br/>' );
		$('#load').html( resp[1] );

		if ( global_data.load[0].data.length > 25 ) { global_data.load[0].data.shift(); }

		var yval = resp[1];
		var milliseconds = new Date().getTime();
		global_data.load[0].data.push([milliseconds, yval]);

		load_plot_refresh(plot.load, global_data.load);
	});

	auto_loop_ajax( 'load-5m', function(resp) {

		if ( global_data.load[1].data.length > 25 ) { global_data.load[1].data.shift(); }

		var yval = resp[1];
		var milliseconds = new Date().getTime();
		global_data.load[1].data.push([milliseconds, yval]);

		load_plot_refresh(plot.load, global_data.load);
	});

	auto_loop_ajax( 'load-15m', function(resp) {

		if ( global_data.load[2].data.length > 25 ) { global_data.load[2].data.shift(); }

		var yval = resp[1];
		var milliseconds = new Date().getTime();
		global_data.load[2].data.push([milliseconds, yval]);

		load_plot_refresh(plot.load, global_data.load);
	});


	////////////////////
	// PHP Run time plot
	plot.php = $.plot($("#php_graph"), global_data.php, { 
		lines: { show: true },
		xaxis: { mode: "time" }, 
		points: { show: true },
		grid: { backgroundColor: { colors: ["#fff", "#eee"] } }
	});

	auto_loop_ajax( 'php-lus', function(resp) {
		//alert(resp[1]);
		//$('#load').append( resp[1] + '<br/>' );

		if ( global_data.php.data.length > 12 ) { global_data.php.data.shift(); }

		var yval = resp[1];
		var milliseconds = new Date().getTime();
		global_data.php.data.push([milliseconds, yval]);

		load_plot_refresh(plot.php, [ global_data.php ]);
	});

	//////////////
	// Mysql plots
	plot.mysql = $.plot($("#mysql_graph"), global_data.mysql, { 
		lines: { show: true },
		xaxis: { mode: "time" }, 
		points: { show: true },
		grid: { backgroundColor: { colors: ["#fff", "#eee"] } }
	});

	auto_loop_ajax( 'mysql-ping', function(resp) {
		//alert(resp[1]);
		//$('#load').append( resp[1] + '<br/>' );

		if ( global_data.mysql[0].data.length > 12 ) { global_data.mysql[0].data.shift(); }

		var yval = resp[1];
		var milliseconds = new Date().getTime();
		global_data.mysql[0].data.push([milliseconds, yval]);

		load_plot_refresh(plot.mysql, global_data.mysql );
	});

	auto_loop_ajax( 'mysql-select', function(resp) {
		//alert(resp[1]);
		//$('#load').append( resp[1] + '<br/>' );

		if ( global_data.mysql[1].data.length > 12 ) { global_data.mysql[1].data.shift(); }

		var yval = resp[1];
		var milliseconds = new Date().getTime();
		global_data.mysql[1].data.push([milliseconds, yval]);

		load_plot_refresh(plot.mysql, global_data.mysql );
	});

	auto_loop_ajax( 'mysql-execTable', function(resp) {
		//alert(resp[1]);
		//$('#load').append( resp[1] + '<br/>' );

		if ( global_data.mysql[2].data.length > 12 ) { global_data.mysql[2].data.shift(); }

		var yval = resp[1];
		var milliseconds = new Date().getTime();
		global_data.mysql[2].data.push([milliseconds, yval]);

		load_plot_refresh(plot.mysql, global_data.mysql );
	});

	/////////////////
	// Diskspace plot
	$.ajax({
		url: 'index.php?test=' + 'diskspace',
		dataType: 'text',
		success: function(data) {
			var resp = data.split('::INFO::');
			var arr = resp[1].split(' | ');
			var disk_used = arr[0];
			var disk_free = 100-disk_used;

			console.log(disk_used + " " + disk_free);
			global_data.diskspace =  [ {
					label: "used", 
					data: parseInt(disk_used),
					color: "#005703"
				}, {
					label: "free", 
					data: parseInt(disk_free),
					color: "#00BD06"
				}];
			plot.diskspace = $.plot($("#diskspace_graph"), global_data.diskspace, { 
				series: { pie: { show: true } },
				// grid: { backgroundColor: { colors: ["#fff", "#eee"] } },
				legend: { show: false }
				//points: { show: true }
			});
		}
	});

	/////////////////
	// Diskspace plot
	$.ajax({
		url: 'index.php?test=' + 'quota',
		dataType: 'text',
		success: function(data) {
			var resp = data.split('::INFO::');
			var arr = resp[1].split(' | ');
			var disk_used = arr[0];
			var disk_free = 100-disk_used;

			console.log(disk_used + " " + disk_free);
			global_data.quota =  [ {
					label: "used", 
					data: parseInt(disk_used),
					color: "#005703"
				}, {
					label: "free", 
					data: parseInt(disk_free),
					color: "#00BD06"
				}];
			plot.quota = $.plot($("#quota_graph"), global_data.quota, { 
				series: { pie: { show: true } },
				// grid: { backgroundColor: { colors: ["#fff", "#eee"] } },
				legend: { show: false }
				//points: { show: true }
			});
		}
	});

	
</script>
</body>
</html>