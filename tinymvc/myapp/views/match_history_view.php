<?php include "static/includes/header.php"; ?>
<title> Match History </title>
<div class="container-fluid">
	<div class="widget-content nopadding">
		<form action="/table/match_history" method="POST">

		<?= $form['matchList'] ?>

		<?= $form['serverList'] ?>

		<?= $form['dateList'] ?>

		<?= $form['submitBtn'] ?>
		<?= $form['resetBtn'] ?>
		</form>
		<form method="POST" action="/table/match_history">
			<table class="table table-bordered table-striped with-check">
				<th><input type="checkbox" id="title-table-checkbox" name="title-table-checkbox" /></th>
				<th>Tier and region</th>
				<th>Start Time</th>
				<th>End Time</th>
				<th>Red Servers</th>
				<th>Blue Servers</th>
				<th>Green Servers</th>
			<?php foreach($matches as $match) { ?>
				<tr>

					<td><input type="checkbox" <?= in_array($match['id'], $form['matchids']) ? 'checked' : '' ?> name="matchids[]" value='<?=$match['id']?>'></td>
					<td><?=preg_replace(array("/1\-/","/2\-/"), array("NA Tier ","EU Tier "), $match['match_id'])?></td>
					<td><?=$match['start_time']?></td>
					<td><?=$match['end_time']?></td>
					<td><?=$match['red_servers']?><br/><?=$match['red_skirmish_score']?></td>
					<td><?=$match['blue_servers']?><br/><?=$match['blue_skirmish_score']?></td>
					<td><?=$match['green_servers']?><br/><?=$match['green_skirmish_score']?></td>
				</tr>
			<?php } ?>
			</table>
			<input type="submit" class="btn" value="Set search parameters for all pages">
		</form>
	</div>
</div>

<?php include "static/includes/footer.php"; ?>
