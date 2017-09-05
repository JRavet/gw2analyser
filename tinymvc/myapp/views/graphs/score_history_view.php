<?php include "static/includes/header.php"; ?>
<title> Score History </title>
<div class="container-fluid">
	<div class="widget-content nopadding">
		<form action="/graph/score_history" method="POST">
			<?= $form['matchList'] ?>
			<b>OR</b>
			<?= $form['serverList'] ?>
			<? if ( isset($error) ) { ?>
				<span class="label label-warning"><?=$error?></span>
			<? } ?>
			<hr/>
			<?= $form['dateList'] ?>
			<?= $form['timeList'] ?>
			<?= $form['submitBtn'] ?>
			<?= $form['resetBtn'] ?>
		</form>
	</div>
</div>
<br/>

<div class="row-fluid">
	<div class="span6">
		<div class="widget-box">
		<div class="widget-title"> <span class="icon"> <i class="icon-signal"></i> </span>
			<h5>PPT</h5>
		</div>
		<div class="widget-content">
			<div id="ppt" class="chart"></div>
		</div>
		</div>
	</div>
	<div class="span6">
		<div class="widget-box">
		<div class="widget-title"> <span class="icon"> <i class="icon-signal"></i> </span>
			<h5>Score</h5>
		</div>
		<div class="widget-content">
			<div id="scores" class="chart"></div>
		</div>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="span6">
		<div class="widget-box">
		<div class="widget-title"> <span class="icon"> <i class="icon-signal"></i> </span>
			<h5>Kills</h5>
		</div>
		<div class="widget-content">
			<div id="kills" class="chart"></div>
		</div>
		</div>
	</div>
	<div class="span6">
		<div class="widget-box">
		<div class="widget-title"> <span class="icon"> <i class="icon-signal"></i> </span>
			<h5>Deaths</h5>
		</div>
		<div class="widget-content">
			<div id="deaths" class="chart"></div>
		</div>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="span6">
		<div class="widget-box">
		<div class="widget-title"> <span class="icon"> <i class="icon-signal"></i> </span>
			<h5>KDR</h5>
		</div>
		<div class="widget-content">
			<div id="kdr" class="chart"></div>
		</div>
		</div>
	</div>
</div>
<?php include "static/includes/footer.php"; ?>
