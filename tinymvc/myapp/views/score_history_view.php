<?php include "static/includes/header.php"; ?>
<title> Score History </title>
<div class="container-fluid">
	<div class="widget-content nopadding">
		<form action="/graph/score_history" method="POST">
			<!-- Match region and tier -->
			<div class="row-fluid">
				<div class="control-group">
					<div class="controls span2">
					<label class="control-label"> Match Tier </label>
						<select id="matchid" name="matchid">
							<option value="NULL">All</option>
							<?php foreach($matches as $k=>$v) { ?>
								<option <?=$formData['matchid'] == $v ? 'selected' : ''?> value="<?=$v?>"><?=$k?></option>
							<?php } ?>
						</select>
					</div>
					<div class="span1">
					- OR -
					</div>
					<div class="controls span2">
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

			<!-- Week number -->
			<div class="row-fluid">
				<div class="control-group">
					<div class="controls span3">
						<label class="control-label"> Week number </label>
						<select name="weekNum">
							<?php foreach($week_numbers as $n) { ?>
								<option <?=$formData['weekNum'] == $n['week_num'] ? 'selected' : ''?> value="<?=$n['week_num']?>"><?=$n['week_num']?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>

			<!-- Time span -->
			<div class="row-fluid">
				<div class="control-group">
					<div class="controls span12">
						<!-- TODO: slider; selecting weeknum ajax's to filter it properly -->
					</div>
				</div>
			</div>

			<input type="submit" value="Filter">
			<a class="btn" style="margin-top:5px" href="/graph/score_history">Reset Filter</a>
		</form>
	</div>
</div>
<br/>
<div>
	<div>

	</div>
</div>
<!--

-->

<?php include "static/includes/footer.php"; ?>
