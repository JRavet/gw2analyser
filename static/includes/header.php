<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" /> 
		<link rel="stylesheet" href="/static/css/bootstrap.min.css" />
		<link rel="stylesheet" href="/static/css/bootstrap-responsive.min.css" />
		<link rel="stylesheet" href="/static/css/datepicker.css" />
		<link rel="stylesheet" href="/static/css/uniform.css" />
		<link rel="stylesheet" href="/static/css/select2.css" />
		<link rel="stylesheet" href="/static/css/matrix-style.css" />
		<link rel="stylesheet" href="/static/css/matrix-media.css" />
		<link href="/static/font-awesome/css/font-awesome.css" rel="stylesheet" />
		<link href="/static/css/override.css" rel="stylesheet" />
	</head>
	<body style="background-color:<?=$colorPrefs['bgColor1']?>">
	<div class="row">
		<div class="span3"></div>
		<div class="navbar span6">
			<ul class="nav">
				<li class="dropdown br bb bl" id="table-analyser-dropdown" ><a href="#" data-toggle="dropdown" data-target="#table-analyser-dropdown" class="dropdown-toggle"><span class="text">Analyser Tables</span><b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="/table/capture_history">Capture History</a></li>
					<li class="divider"></li>
					<li><a href="/table/guild_history">Guild History</a></li>
					<li class="divider"></li>
					<li><a href="/table/match_history">Match History</a></li>
				  </ul>
				</li>

				<li class="dropdown br bb" id="graphical-analyser-dropdown" ><a href="#" data-toggle="dropdown" data-target="#graphical-analyser-dropdown" class="dropdown-toggle"><span class="text">Analyser Charts</span><b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="/graph/score_history">Score History</a></li>
					<li class="divider"></li>
					<li><a href="/graph/activity_summary"> Last-hour Activity Summary</a></li>
					<li><a href="#">NOT AVAILABLE Historical Map</a></li>
				  </ul>
				</li>

				<li class="dropdown br bb" id="misc-dropdown" ><a href="#" data-toggle="dropdown" data-target="#misc-dropdown" class="dropdown-toggle"><span class="text">Miscellaneous</span><b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="/table/tomorrows_dailies">Tomorrow's Dailies</a></li>
				  </ul>
				</li>

				<?php if (isset($username)) { ?>
					<li class="dropdown br bb" id="user-profile"><a href="#" data-toggle="dropdown" data-target="#user-profile" class="dropdown-toggle"><span class="text"><?=$username?></span><b class="caret"></b></a>
					  <ul class="dropdown-menu">
						<li><a href="/users/preferences"><i class="icon-user"></i>Preferences</a></li>
						<li class="divider"></li>
						<li><a href="/users/logout"><i class="icon-key"></i>Log Out</a></li>
					  </ul>
					</li>
				<?php } else { ?>
					<li class="br bb"><a href="/users/login">Log in</a></li>
				<?php } ?>
			</ul>
		</div>
		<div class="span3"></div>
	</div>