<?php include "static/includes/header.php"; ?>
<title> Activity Summary </title>
<div class="container-fluid">
	<div class="widget-content nopadding">
		<form action="/graph/score_history" method="POST">
			<?= $form['matchList'] ?>
			<b>OR</b>
			<?= $form['serverList'] ?>
			<? if ( isset($error) ) { ?>
				<span class="label label-warning"><?=$error?></span>
			<? } ?>
			<hr/>
			<?= $form['submitBtn'] ?>
			<?= $form['resetBtn'] ?>
		</form>
	</div>
</div>
<br/>

Eternal Battlegrounds in the last hour
<table class="table">
    <thead>
        <th>Kills</th>
        <th>Deaths</th>
        <th>Captures</th>
        <th>Claims</th>
    </thead>
    <tbody>

    </tbody>

</table>

<?php include "static/includes/footer.php"; ?>