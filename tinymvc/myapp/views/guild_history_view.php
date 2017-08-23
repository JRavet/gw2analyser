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

			<? foreach($data as $a) { ?>
				<a href="#collapse<?=$a['id']?>" data-toggle="collapse">
				<div class="widget-title">
					<span class="icon"><i class="icon-arrow-down"></i></span>
					<span class="span12"><?=$a['guild_name']?> - <?=$a['claims_total']?> total claims</span>
				</div>
				</a>
			<div class="collapse" id="collapse<?=$a['id']?>">
				<div class="widget-content">
					<table class="table table-bordered table-striped">
						<thead>
							<th>EB Claims</th>
							<th>Red BL Claims</th>
							<th>Blue BL Claims</th>
							<th>Green BL Claims</th>
						</thead>
						<tbody>
							<tr>
								<td><?=$a['claims_EBG']?> (<?=number_format($a['claims_EBG']/$a['claims_total']*100, 2)?>%)</td>
								<td><?=$a['claims_RBL']?> (<?=number_format($a['claims_RBL']/$a['claims_total']*100, 2)?>%)</td>
								<td><?=$a['claims_BBL']?> (<?=number_format($a['claims_BBL']/$a['claims_total']*100, 2)?>%)</td>
								<td><?=$a['claims_GBL']?> (<?=number_format($a['claims_GBL']/$a['claims_total']*100, 2)?>%)</td>
							</tr>
						</tbody>
						<thead>
							<th>Home BL Claims</th>
							<th>Enemy BL Claims</th>
							<th>Claims under 30 minutes</th>
							<th>Claims over 3 hours</th>
						</thead>
						<tbody>
							<td><?=$a['claims_home']?> (<?=number_format($a['claims_home']/$a['claims_total']*100, 2)?>%)</td>
							<td><?=$a['claims_enemy']?> (<?=number_format($a['claims_enemy']/$a['claims_total']*100, 2)?>%)</td>
							<td><?=$a['claims_under_30min']?> (<?=number_format($a['claims_under_30min']/$a['claims_total']*100, 2)?>%)</td>
							<td><?=$a['claims_over_3hours']?> (<?=number_format($a['claims_over_3hours']/$a['claims_total']*100, 2)?>%)</td>
						</tbody>
						<thead>
							<th>Camps Claimed</th>
							<th>Towers Claimed</th>
							<th>Keeps Claimed</th>
							<th>Castles Claimed</th>
						</thead>
						<tbody>
							<tr>
								<td><?=$a['camps_claimed']?> (<?=number_format($a['camps_claimed']/$a['claims_total']*100, 2)?>%)</td>
								<td><?=$a['towers_claimed']?> (<?=number_format($a['towers_claimed']/$a['claims_total']*100, 2)?>%)</td>
								<td><?=$a['keeps_claimed']?> (<?=number_format($a['keeps_claimed']/$a['claims_total']*100, 2)?>%)</td>
								<td><?=$a['castles_claimed']?> (<?=number_format($a['castles_claimed']/$a['claims_total']*100, 2)?>%)</td>
							</tr>
						</tbody>
						<thead>
							<th>Total Claim Duration</th>
							<th>Average Claim Duration</th>
							<th>Longest Claim Duration</th>
							<th>Upgrades Done</th>
						</thead>
						<tbody>
							<tr>
								<td><?=$a['total_claim_duration']?></td>
								<td><?=$a['avg_claim_duration']?></td>
								<td><?=$a['max_claim_duration']?></td>
								<td><?=$a['upgrades_done']?></td>
							</tr>
						</tbody>
						<thead>
							<th>Most Claimed Objective</th>
						</thead>
						<tbody>
							<tr>
								<td><?=$a['most_claimed']['objective']?> (<?=$a['most_claimed']['claims']?> claims)</td>
							</tr>
						</tbody>
					</table>
					<br/>
					<table style="width:600px" class="table table-bordered table-striped">
						<thead>
							<th>Server</th>
							<th>Claims</th>
							<th>Most Recent Claim</th>
						</thead>
						<tbody>
						<?php foreach($a['servers'] as $s) { ?>
							<tr>
								<td><?=$s['server']?></td>
								<td><?=$s['server_claims']?> (<?=number_format($s['server_claims']/$a['claims_total']*100, 2)?>%)</td>
								<td><?=$s['last_claim']?></td>
						<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
			<? } ?>
			<input type="submit" class="btn" value="Set search parameters for all pages">

	</div>
</div>
<!--

-->

<?php include "static/includes/footer.php"; ?>
