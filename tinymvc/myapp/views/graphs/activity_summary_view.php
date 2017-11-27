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
        <th>Captures</th>
        <th>Claims</th>
    </thead>
    <tbody>
    <?php foreach($score_history as $score) { ?>
        <tr>
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
        </tr>
    <?php } ?>
    </tbody>

</table>

<?php include "static/includes/footer.php"; ?>