<?php include "includes/header.php"; ?>
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

			<table>
			<th></th>
			<th>Start Time</th>
			<th>End Time</th>
			<th>Red Servers</th>
			<th>Blue Servers</th>
			<th>Green Servers</th>
			<?php foreach($data as $a) { ?>
					<tr>
						<td><input type="hidden" value='<?=$a['id']?>'></td>
						<td><?=$a['start_time']?></td>
						<td><?=$a['end_time']?></td>
						<td><?=$a['red_servers']?></td>
						<td><?=$a['blue_servers']?></td>
						<td><?=$a['green_servers']?></td>
					</tr>
				<?php } ?>
			</table>
			<input type="submit" value="Set search parameters for all pages">

	</div>
</div>
<!--

-->

<?php include "includes/footer.php"; ?>
