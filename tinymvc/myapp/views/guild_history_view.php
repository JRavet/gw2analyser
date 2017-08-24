<?php include "static/includes/header.php"; ?>
<title> Guild Analyser </title>
<div class="container-fluid">
	<div class="widget-content nopadding">
		<form action="/table/guild_history" method="POST">

		<div class="row-fluid">
			<div class="control-group">
				<div class="controls span3">
				<label class="control-label"> Match Tier </label>
					<select id="matchid" name="matchid">
						<option value="NULL">All</option>
						<option <?=$formData['matchid'] == "1-1" ? 'selected' : ''?> value="1-1">NA Tier 1</option>
						<option <?=$formData['matchid'] == "1-2" ? 'selected' : ''?> value="1-2">NA Tier 2</option>
						<option <?=$formData['matchid'] == "1-3" ? 'selected' : ''?> value="1-3">NA Tier 3</option>
						<option <?=$formData['matchid'] == "1-4" ? 'selected' : ''?> value="1-4">NA Tier 4</option>
						<option <?=$formData['matchid'] == "2-1" ? 'selected' : ''?> value="2-1">EU Tier 1</option>
						<option <?=$formData['matchid'] == "2-2" ? 'selected' : ''?> value="2-2">EU Tier 2</option>
						<option <?=$formData['matchid'] == "2-3" ? 'selected' : ''?> value="2-3">EU Tier 3</option>
						<option <?=$formData['matchid'] == "2-4" ? 'selected' : ''?> value="2-4">EU Tier 4</option>
						<option <?=$formData['matchid'] == "2-5" ? 'selected' : ''?> value="2-5">EU Tier 5</option>
					</select>
				</div>
			</div>
		</div>

		<div class="row-fluid">
			<div class="control-group">
				<div class="controls span3">
				<label class="control-label"> Owner Server </label>
					<select id="serverid" name="serverid">
						<option value="NULL">All</option>
						<?php foreach($srv as $s) { ?>
							<option <?=$formData['serverid'] == $s['id'] ? 'selected' : ''?> value="<?=$s['id']?>"><?=$s['name']?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>

		<input type="submit" value="Filter">

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
							<tr>
								<td><?=$a['claims_home']?> (<?=number_format($a['claims_home']/$a['claims_total']*100, 2)?>%)</td>
								<td><?=$a['claims_enemy']?> (<?=number_format($a['claims_enemy']/$a['claims_total']*100, 2)?>%)</td>
								<td><?=$a['claims_under_30min']?> (<?=number_format($a['claims_under_30min']/$a['claims_total']*100, 2)?>%)</td>
								<td><?=$a['claims_over_3hours']?> (<?=number_format($a['claims_over_3hours']/$a['claims_total']*100, 2)?>%)</td>
							</tr>
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
							<th>Tactics Slotted</th>
						</thead>
						<tbody>
							<tr>
								<td><?=$a['total_claim_duration']?></td>
								<td><?=$a['avg_claim_duration']?></td>
								<td><?=$a['max_claim_duration']?></td>
								<td><?=$a['tactics_slotted']?></td>
							</tr>
						</tbody>
						<thead>
							<th>Most Claimed Objective</th>
						</thead>
						<tbody>
							<tr>
								<?php $o = $a['most_claimed']; ?>
								<td><?=$o['objective']?> (<?=$o['dir']?> <?=$o['type']?> on 
									<?=preg_replace(array("/Center/","/RedHome/","/BlueHome/","/GreenHome/"),
											array("EB","RBL","BBL","GBL"),$o['map'])?>)
								- <?=$a['most_claimed']['claims']?> claims</td>
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
