<?php include "static/includes/header.php" ?>
<title> History Analyser </title>
<div class="container-fluid">
	<div class="widget-content nopadding">
		<form action="#" method="POST">

		<div class="row-fluid">
			<div class="control-group">
				<div class="controls span3">
				<label class="control-label"> Match Tier </label>
					<select>
						<option value="1-1">NA Tier 1</option>
						<option value="1-2">NA Tier 2</option>
						<option value="1-3">NA Tier 3</option>
						<option value="1-4">NA Tier 4</option>
					</select>
				</div>
			</div>
		</div>

		<div class="row-fluid">
			<div class="control-group">
				<div class="controls span3">
					<label class="control-label"> Objective Type </label>
					<select>
						<option value="Castle"> Castle </option>
						<option value="Keep"> Keep </option>
						<option value="Tower"> Tower </option>
						<option value="Camp"> Camp </option>
						<option value="Ruin"> Ruin </option>
					</select>
				</div>
			</div>
		</div>

		<div class="row-fluid">
			<div class="control-group">
				<div class="controls span3">
					<label class="control-label"> Server Owner </label>
					<select>
						<option value="Maguuma"> Maguuma </option>
						<option value="Northern Shiverpeaks"> Northern Shiverpeaks </option>
						<option value="..."> ... </option>
					</select>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="control-group">
				<div class="controls span12">
					<label class="control-label"> Last Flipped - Date range </label>
					<div data-date="" class="input-append date datepicker">
						<input value="07-21-2017" data-date-format="mm-dd-yyyy" class="span10" type="text">
						<span class="add-on"><i class="icon-th"></i></span>
					</div>

					-

					<div data-date="" class="input-append date datepicker">
						<input value="07-28-2017" data-date-format="mm-dd-yyyy" class="span10" type="text">
						<span class="add-on"><i class="icon-th"></i></span>
					</div>
				</div>
			</div>

			<div class="control-group">
				<div class="controls span12">
					<label class="control-label"> Last Flipped - Time span (per day) </label>
					<input placeholder="HH:MM:SS" class="span3" type="text">
					-
					<input placeholder="HH:MM:SS" class="span3" type="text">
				</div>
			</div>
		</div>

		<div class="row-fluid">
			<div class="control-group">
				<div class="controls span3">
					<label class="control-label"> Claimed By </label>
					<input class="span7" placeholder="Guild name" type="text">
					<input class="span5" placeholder="Guild tag" type="text">
				</div>
			</div>
		</div>

		</form>

			<table class="table table-bordered">
				<th class="span3"> Last Flipped </th>
				<th class="span2"> Name </th>
				<th class="span1"> Type </th>
				<th class="span1"> Map </th>
				<th class="span2"> Server </th>
				<th class="span4"> Duration Held </th>
			</table>
			<?php
			foreach($data as $ch) {
				if (preg_match("/Ruins|Spawn/i",$ch['place'])) continue; ?>

				<a href="#collapse<?=$ch['id']?>" data-toggle="collapse"> 
				<div class="widget-title" style="Background-color: <?switch ($ch["owner_color"]){
						case "Red": echo "#ff8c95"; break;
						case "Blue": echo "#8c8fff"; break;
						case "Green": echo "#8cff9f"; break;
						default: echo "light-grey"; break;
					}?>">
				<span class="icon"><i class="icon-arrow-down"></i></span>
				<tr style="Background-color: <?switch ($ch["owner_color"]){
						case "Red": echo "#ff8c95"; break;
						case "Blue": echo "#8c8fff"; break;
						case "Green": echo "#8cff9f"; break;
						default: echo "light-grey"; break;
					}?>">
				<span class="span3"> <?= $ch['last_flipped'] ?> </span>
				<span class="span2"> <?= $ch['name'] ?> </span>
				<span class="span1"> <?= $ch['place'] ?> </span>
				<span class="span1"> <?= $ch['map_type'] ?> </span>
				<span class="span2"> <?= $ch['server_owner'] ?> </span>
				<span class="span3"> <?= $ch['duration_owned'] ?> </span>
				</tr>
				</div></a>
				<div class="collapse" id="collapse<?=$ch['id']?>">
					<div class="widget-content">
					<table class="table table-bordered data-table">
					<th class="span2"> Type </th> <th class="span4"> Timestamp </th> <th class="span4"> Name </th> <th class="span2"> Duration Claimed </th>
				<?php
				foreach($ch['details'] as $c)
				{
				?>
					<tr>
						<td><?= $c['type'] ?></td>
					<?php
					foreach($c as $k=>$v)
					{
						if (preg_match("/type|capture_history_id|^id/i",$k)) continue; ?>
						<td><?=$v?></td>
					<?php
					}
					?>
					</tr>
					<?php
				} ?>
				</table>
				</div>
				</div>
			<?php }	?>
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