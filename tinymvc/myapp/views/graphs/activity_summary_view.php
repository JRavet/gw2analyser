<?php include "static/includes/header.php"; ?>
<title> Activity Summary </title>
<div class="container-fluid">
	<div class="widget-content nopadding">
		<form action="/graph/activity_summary" method="POST">
			<?= $form['matchList'] ?>
			<b>OR</b>
			<?= $form['serverList'] ?>
			<?php if ( isset($error) ) { ?>
				<span class="label label-warning"><?=$error?></span>
			<?php } ?>
			<hr/>
			<?= $form['submitBtn'] ?>
			<?= $form['resetBtn'] ?>
		</form>
	</div>
</div>
<br/>
<span class="label label-success"><?=$score_history[0]['green_server']?></span>
<span class="label label-info"><?=$score_history[0]['blue_server']?></span>
<span class="label label-important"><?=$score_history[0]['red_server']?></span>
<br/>
Stats in the last hour
<table class="table">
    <thead>
        <th>Map</th>
        <th>Kills</th>
        <th>Deaths</th>
        <th title="Camps=1, Towers=2, Keeps=3, Castle=4">Captures (weighted)</th>
        <th>Claims</th>
		<th>Activity Score</th>
    </thead>
    <tbody>
    <?php foreach($score_history as $score) { ?>
        <tr>
			<?php $activity_score = $score['totalkills']/100; ?>
            <td><?=$score['map_id']?></td>
            <td>
                <div class="progress">
                    <div class="bar bar-success" style="width: <?=($score['greenkills']/$score['totalkills'])*100?>%;"><?=$score['greenkills']?></div>
                    <div class="bar bar-info" style="width: <?=($score['bluekills']/$score['totalkills'])*100?>%;"><?=$score['bluekills']?></div>
                    <div class="bar bar-danger" style="width: <?=($score['redkills']/$score['totalkills'])*100?>%;"><?=$score['redkills']?></div>
                </div>
            </td>
            <td>
                <div class="progress">
                    <div class="bar bar-success" style="width: <?=($score['greendeaths']/$score['totaldeaths'])*100?>%;"><?=$score['greendeaths']?></div>
                    <div class="bar bar-info" style="width: <?=($score['bluedeaths']/$score['totaldeaths'])*100?>%;"><?=$score['bluedeaths']?></div>
                    <div class="bar bar-danger" style="width: <?=($score['reddeaths']/$score['totaldeaths'])*100?>%;"><?=$score['reddeaths']?></div>
                </div>
            </td>
			<td>
				<?php foreach($capture_history as $ch) { ?>
					<?php if ($ch['map_type'] == $score['map_id']) { ?>
						<?php $activity_score += $ch['totalcaps']/10; ?>
						<div class="progress">
							<div class="bar bar-success" style="width: <?=($ch['greencaps']/$ch['totalcaps'])*100?>%;"><?=$ch['greencaps']?></div>
							<div class="bar bar-info" style="width: <?=($ch['bluecaps']/$ch['totalcaps'])*100?>%;"><?=$ch['bluecaps']?></div>
							<div class="bar bar-danger" style="width: <?=($ch['redcaps']/$ch['totalcaps'])*100?>%;"><?=$ch['redcaps']?></div>
						</div>
				<?php break; } } ?>
			</td>
			<td>
				<?php foreach($claim_history as $ch) { ?>
					<?php if ($ch['map_type'] == $score['map_id']) { ?>
						<?php $activity_score += $ch['totalclaims']/10; ?>
						<div class="progress">
							<div class="bar bar-success" style="width: <?=($ch['greenclaims']/$ch['totalclaims'])*100?>%;"><?=$ch['greenclaims']?></div>
							<div class="bar bar-info" style="width: <?=($ch['blueclaims']/$ch['totalclaims'])*100?>%;"><?=$ch['blueclaims']?></div>
							<div class="bar bar-danger" style="width: <?=($ch['redclaims']/$ch['totalclaims'])*100?>%;"><?=$ch['redclaims']?></div>
						</div>
				<?php break; } } ?>
			</td>
			<td>
				<?= number_format($activity_score, 2) ?>
			</td>
        </tr>
    <?php } ?>
    </tbody>

</table>

<?php include "static/includes/footer.php"; ?>