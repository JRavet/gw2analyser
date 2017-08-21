<?php include "static/includes/header.php"; ?>
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
					<label class="control-label"> Match Start Date </label>
					<div data-date="" class="input-append date datepicker">
						<input value="07-21-2017" data-date-format="mm-dd-yyyy" class="span10" type="text">
						<span class="add-on"><i class="icon-th"></i></span>
					</div>
				</div>
			</div>
		</div>

		</form>

			<table class="table table-bordered table-striped with-check">
				<th><input type="checkbox" id="title-table-checkbox" name="title-table-checkbox" /></th>
				<th>Tier and region</th>
				<th>Start Time</th>
				<th>End Time</th>
				<th>Red Servers</th>
				<th>Blue Servers</th>
				<th>Green Servers</th>
			<?php foreach($data as $a) { ?>
				<tr>

					<td><input type="checkbox" value='<?=$a['id']?>'></td>
					<td><?=preg_replace(array("/1\-/","/2\-/"), array("NA Tier ","EU Tier "), $a['match_id'])?></td>
					<td><?=$a['start_time']?></td>
					<td><?=$a['end_time']?></td>
					<td><?=$a['red_servers']?><br/><?=$a['red_skirmish_score']?></td>
					<td><?=$a['blue_servers']?><br/><?=$a['blue_skirmish_score']?></td>
					<td><?=$a['green_servers']?><br/><?=$a['green_skirmish_score']?></td>
				</tr>
			<?php } ?>
			</table>
			<form method="POST" action="/table/match_details">
			<input type="submit" class="btn" value="Set search parameters for all pages">
			</form>

	</div>
</div>
<!--

-->

<?php include "static/includes/footer.php"; ?>
