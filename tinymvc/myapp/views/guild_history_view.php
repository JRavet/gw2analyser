<?php include "static/includes/header.php"; ?>
<title> Guild Analyser </title>
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

			<table class="table table-bordered table-striped" style="width:800px">
				<th>Guild Name [Tag]</th>
				<th># of claims [EBG]</th>
				<th># of claims [RBL]</th>
				<th># of claims [BBL]</th>
				<th># of claims [GBL]</th>
			<?php foreach($data as $a) { ?>
					<tr>
						<td><?=$a['guild_name']?></td>
						<td><?=$a['claims_EBG']?> (<?=number_format($a['claims_EBG']/$a['claims_total']*100, 2)?>%)</td>
						<td><?=$a['claims_RBL']?> (<?=number_format($a['claims_RBL']/$a['claims_total']*100, 2)?>%)</td>
						<td><?=$a['claims_BBL']?> (<?=number_format($a['claims_BBL']/$a['claims_total']*100, 2)?>%)</td>
						<td><?=$a['claims_GBL']?> (<?=number_format($a['claims_GBL']/$a['claims_total']*100, 2)?>%)</td>
					</tr>
				<?php } ?>
			</table>
			<input type="submit" class="btn" value="Set search parameters for all pages">

	</div>
</div>
<!--

-->

<?php include "static/includes/footer.php"; ?>
