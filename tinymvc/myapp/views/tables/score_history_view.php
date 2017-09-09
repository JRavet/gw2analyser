<?php include "static/includes/header.php"; ?>
<title> Score History </title>
<div class="container-fluid">
	<div class="widget-content nopadding">
		<form action="/table/score_history" method="POST">
			<?= $form['matchList'] ?>
			<b>OR</b>
			<?= $form['serverList'] ?>
			<? if ( isset($error) ) { ?>
				<span class="label label-warning"><?=$error?></span>
			<? } ?>
			<hr/>
			<?= $form['mapList'] ?>
			<?= $form['dateList'] ?>
			<?= $form['timeList'] ?>
			<?= $form['submitBtn'] ?>
			<?= $form['resetBtn'] ?>
		</form>
	</div>
</div>
<br/>
<span class="span3"></span>
<span class="span2" style="background-color: <?=$redServer?>"><center><b><?=$scores[0]['red_server']?></b></center></span>
<span class="span2" style="background-color: <?=$blueServer?>"><center><b><?=$scores[0]['blue_server']?></b></center></span>
<span class="span2" style="background-color: <?=$greenServer?>"><center><b><?=$scores[0]['green_server']?></b></center></span>
<span class="span3"></span>
<table class="table">
	<thead>
		<th>RKills</th>
		<th>BKills</th>
		<th>GKills</th>
		<th>RDeaths</th>
		<th>BDeaths</th>
		<th>GDeaths</th>
		<th>RKD</th>
		<th>BKD</th>
		<th>GKD</th>
	</thead>
	<tbody>
		<? foreach ($scores as $score) { ?>
			<tr>
				<td style="background-color: <?=$redServer?>"><?=$score['redkills']?></td>
				<td style="background-color: <?=$blueServer?>"><?=$score['bluekills']?></td>
				<td style="background-color: <?=$greenServer?>"><?=$score['greenkills']?></td>
				<td style="background-color: <?=$redServer?>"><?=$score['reddeaths']?></td>
				<td style="background-color: <?=$blueServer?>"><?=$score['bluedeaths']?></td>
				<td style="background-color: <?=$greenServer?>"><?=$score['greendeaths']?></td>
				<td style="background-color: <?=$redServer?>"><?=number_format($score['redkdr'],2)?></td>
				<td style="background-color: <?=$blueServer?>"><?=number_format($score['bluekdr'],2)?></td>
				<td style="background-color: <?=$greenServer?>"><?=number_format($score['greenkdr'],2)?></td>
			</tr>
		<? } ?>
	</tbody>
</table>