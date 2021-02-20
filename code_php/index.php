<?php
/*
 * EXAMPLE DASHBOARD - Arduino PM5350
 * ***********************************************************************************
 * Code by : fahroni|ganteng
 * contact me : fahroniganteng@gmail.com
 * Date : feb 2021
 * License :  MIT
 * 
 */
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Arduino - PM5350</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <link href="css/dashboard.css" rel="stylesheet">
  </head>
  <body>
	<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
		<a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#">Arduino - PM5350</a>
		<button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<ul class="navbar-nav px-3">
			<li class="nav-item text-nowrap">
				<a class="nav-link" href="#">this is a sample app for displaying recording</a>
			</li>
		</ul>
	</nav>

<div class="container-fluid">
  <div class="row">
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
      <div class="sidebar-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link active" href="#">Real Time</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Record avg 15m</a>
          </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
          <span>Tool</span>
        </h6>
        <ul class="nav flex-column mb-2">
          <li class="nav-item">
            <a class="nav-link" href="#">Basic Auth Generator</a>
          </li>
        </ul>
		
		<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
          <span>INFO</span>
        </h6>
        <ul class="nav flex-column mb-2">
          <li class="nav-item">
            <a class="nav-link" href="#">About this App</a>
          </li>
        </ul>
		
      </div>
    </nav>

    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h2 class="mb-0">Loading...</h2>
      </div>
	  <div id="content" class="overflow-auto">Plz wait, load html data...</div>
    </main>
  </div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
	var timerRefresh;
	var ronn = {
		init : ()=>{
			$(document).on('click','.sidebar-sticky li>a',function() {
				$(this).closest('nav').find('li>a').removeClass('active');
				$(this).addClass('active');
				let req = $(this).text();//request
				ronn.getPage(req,false);
				return false;
			});
			$('.sidebar-sticky li>a.active').trigger('click');
		},
		getPage : (req,update)=>{
			clearTimeout(timerRefresh);
			if(!update){
				$('main h2').html(req);
				$('#content').html('Loading...');
			}
			$.ajax({  
				type    : "POST",  
				url     : "process.php",
				data    : {
					id			: 'getPage',
					pageRequest	: req
				}
			}).done(function(dt){
				$('#content').html(dt);
				if($('#idForCheckRefresh').length)// page 'Real Time' active
					timerRefresh = setTimeout(function(){ronn.getPage(req,true)},5000);
			}).fail(function(msg){
				$('#content').html('Failed to get content...');
				alert(msg.status+"\n"+msg.statusText);
				if($('#idForCheckRefresh').length)// page 'Real Time' active
					timerRefresh = setTimeout(function(){ronn.getPage(req,true)},5000);
			});
		},
		generateAuth : (self)=>{
			$('#authCode').html('Loading...');
			$(self).find('button').attr('disabled','disabled');
			let postData = { id : 'generateAuth'};
			$(self).find(".form-control").each(function(){
				postData[$(this).attr('id')] = $(this).val();
			});
			$.ajax({  
				type    : "POST",  
				url     : "process.php",
				data    : postData
			}).done(function(dt){
				$('#authCode').html(dt);
				$(self).find('button').removeAttr('disabled');
			}).fail(function(msg){
				$('#authCode').html('Failed to get content...');
				alert(msg.status+"\n"+msg.statusText);
				$(self).find('button').removeAttr('disabled');
			});
		},
	};
	$(function() {
		ronn.init();
	});
</script>
</body>
</html>








