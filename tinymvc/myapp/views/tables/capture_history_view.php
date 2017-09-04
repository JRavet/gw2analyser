<?php include "static/includes/header.php" ?>
<title> History Analyser </title>
<div class="container-fluid">
	<div class="widget-content nopadding">
		<form action="/table/capture_history" method="POST">
			<?= $form['matchList'] ?>

			<?= $form['serverList'] ?>

			<?= $form['objectiveTypeList'] ?>

			<?= $form['dateList'] ?>

			<?= $form['weekdayList'] ?>

			<?= $form['timeList'] ?>

			<?= $form['guildList'] ?>

			<?= $form['pageList'] ?>

			<?= $form['submitBtn'] ?>
			<?= $form['resetBtn'] ?>
		</form>
		<? if ( count($captureList) == $form['dataLimit'] ) { ?>
			<span>Limited to <?= $form['dataLimit'] ?> results</span>
		<? } ?>
			<table class="table table-bordered">
				<th class="span3"> Last Flipped </th>
				<th class="span2"> Name </th>
				<th class="span1"> Type </th>
				<th class="span1"> Map </th>
				<th class="span2"> Server </th>
				<th class="span4"> Duration Held </th>
			</table>
			<? $count = 0; ?>
			<? foreach($captureList as $ch) {
				if (preg_match("/Ruins|Spawn/i",$ch['place'])) continue;
				$count++;
				if ($form['listCount'] > $form['pageAmount']) {
					if ($count < $form['pageNum']*$form['pageAmount']+1) continue;
					if ($count > ($form['pageNum']+1)*$form['pageAmount']) break;
				} ?>

				<div class="widget-title" style="Background-color: <?switch ($ch["owner_color"]){
						case "Red": echo $redServer; break;
						case "Blue": echo $blueServer; break;
						case "Green": echo $greenServer; break;
						default: echo "light-grey"; break;
				}?>">
				<? if ( !empty($ch['details']) ) { ?>
					<a href="#collapse<?=$ch['id']?>" data-toggle="collapse"> 
					<span title="Click to expand" class="icon"><i class="icon-arrow-down"></i></span>
				<? } else { ?>
					<span title="No further information" class="icon"><i class="icon-lock"></i></span>
				<? } ?>
				<tr>
					<span class="span1"> (<?= $count ?>) </span>
					<span class="span2"> <?= $ch['last_flipped'] ?> </span>
					<span class="span2"> <?= $ch['name'] ?> </span>
					<span class="span1"> <?= $ch['place'] ?> </span>
					<span class="span1"> <?= $ch['map_type'] ?> </span>
					<span class="span2"> <?= $ch['server_owner'] ?> </span>
					<span class="span3"> <?= $ch['duration_owned'] ?> </span>
				</tr>
				</a>
				</div>
				<div class="collapse" id="collapse<?=$ch['id']?>">
					<div class="widget-content">
					<table class="table table-bordered data-table">
					<th class="span2"> Type </th> <th class="span4"> Timestamp </th> <th class="span4"> Name </th> <th class="span2"> Duration Claimed </th>
				<? foreach($ch['details'] as $c) {	?>
					<tr>
						<td><?= $c['type'] ?></td>
						<? foreach($c as $k=>$v) {
						if (preg_match("/type|capture_history_id|^id/i",$k)) continue; ?>
							<td><?=$v?></td>
						<? } ?>
					</tr>
				<? } ?>
				</table>
				</div>
				</div>
			<? } ?>
	</div>
</div>
<!--

advance options
	objective name 			select
	map 					select
	duration owned, between
		time 				select in 15min increments
	duration claimed, between
		time 				select in 15min increments
	owner-color				select: Green, blue, red

simple options
	match-id				select, options having text like "NA Tier 1" but values of "1-1"
								- change to some representation of internal match_detail_id
	objective type 			select: Castle, Keep, Tower, Camp, Ruin
	owner-server 			select: each server_info (pre-filtered by region, and match # if possible)
								- only allows leading servers to be chosen
	last_flipped, between
		date 				date-picker - default to current matchup's start time, going on ad infinitum
		time 				select in 15min increments - not set initially
	claimed_by
		tag					text, auto-complete optional, offers options for name
		name 				text, auto-complete optional, fills out Tag

inconclusive
	upgrade applied			select, or multi-select?
-->
<!-- 
<br/>
<div class="controls">
	<div data-date="" class="input-append date datepicker">
		<input value="" data-date-format="mm-dd-yyyy" class="span13" type="text">
		<span class="add-on"><i class="icon-th"></i></span>
	</div>
</div> -->

<?php include "static/includes/footer.php" ?>