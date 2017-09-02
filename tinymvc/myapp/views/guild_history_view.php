<?php include "static/includes/header.php"; ?>
<title> Guild Analyser </title>
<div class="container-fluid">
	<div class="widget-content nopadding">
		<form action="/table/guild_history" method="POST">
			<?= $form['matchList'] ?>
			<?= $form['serverList'] ?>
			<!-- Date span -->
			<div class="row-fluid">
				<div class="control-group">
					<div class="controls span12">
						<label class="control-label"> Claim date-range </label>
						<div data-date="" class="input-append date datepicker">
							<input value="<?=$formData['startDate']?>" data-date-format="mm/dd/yyyy" name="startDate" type="text">
							<span class="add-on"><i class="icon-th"></i></span>
						</div>
						-
						<div data-date="" class="input-append date datepicker">
							<input value="<?=$formData['endDate']?>" data-date-format="mm/dd/yyyy" name="endDate" type="text">
							<span class="add-on"><i class="icon-th"></i></span>
						</div>
					</div>
				</div>
			</div>

			<?= $form['weekdayList'] ?>

			<?= $form['timeList'] ?>

			<?= $form['guildList'] ?>

			<?= $form['pageList'] ?>

			<input type="submit" value="Filter">
			<a class="btn" style="margin-top:5px" href="/table/guild_history">Reset Filter</a>
		</form>
	</div>
</div>
<br/>
<div>
	<div>
		<?php $count = 0; ?>
			<? foreach($data as $a) {
				$count++;
				if ($form['listCount'] > 100) {
					if ($count < $formData['page']*100+1) continue;
					if ($count > ($formData['page']+1)*100) break;
				}
			?>
			<a href="#collapse<?=$a['id']?>" data-toggle="collapse">
				<div class="widget-title">
					<span class="icon"><i class="icon-arrow-down"></i></span>
					<span class="span12">(<?=$count?>) <?=$a['guild_name']?> - <?=$a['claims_total']?> total claims</span>
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
					</table>
					<table class="table table-bordered" style="width:600px">
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
								<td><?=$s['server_claims']?> (<?=number_format($s['server_claims']/$s['claims_total']*100, 2)?>%)</td>
								<td><?=$s['last_claim']?></td>
						<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		<? } ?>
	</div>
</div>
<!--

-->

<?php include "static/includes/footer.php"; ?>
