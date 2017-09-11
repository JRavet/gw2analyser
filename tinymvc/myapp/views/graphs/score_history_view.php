<?php include "static/includes/header.php"; ?>

<?  function generate_jsontable($resultSet, $varNames)
    {
        $rows = array();
        $table = array();
        $table['cols'] = array(array('label' => 'Time Stamp', 'type' => 'string'));
        foreach ($varNames as $v)
        {
            array_push($table['cols'],array('label' => $v, 'type' => 'number'));
        }
        foreach($resultSet as $r)
        {
            $temp = array();
            $temp[] = array('v' => (string) $r['timeStamp']);
            foreach ($varNames as $v)
            {
                $temp[] = array('v' => (int) $r["$v"]);
            }
            $rows[] = array('c' => $temp);
        }
        $table['rows'] = $rows;
        return $jsonTable = json_encode($table);
    }
    function generate_googleChart($data, $title, $idName, $options, $redServer, $blueServer, $greenServer)
    {
        echo "<script type=\"text/javascript\">
        google.load('visualization', '1', {'packages':['corechart']});
        google.setOnLoadCallback(drawChart);
        function drawChart()
        {
            var data = new google.visualization.DataTable($data);
            var options = {
                title: \"$title\",
                width: 600,
                $options
                height: 300,
                hAxis: {
                    textPosition: 'none'
                },
                pointSize:1.5,
                legend: {position: 'bottom'},
                colors: ['".$greenServer."','".$blueServer."','".$redServer."']
            };
            new google.visualization.LineChart(document.getElementById('$idName')).draw(data,options);
        }
        </script>
        <div id=\"$idName\"></div>";
    }
?>

<title> Score History </title>
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
			<?= $form['mapList'] ?>
			<?= $form['dateList'] ?>
			<?= $form['timeList'] ?>
			<?= $form['submitBtn'] ?>
			<?= $form['resetBtn'] ?>
		</form>
	</div>
</div>
<br/>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>

<div class="row">
    <div class="span6">
        <?= generate_googleChart(generate_jsontable($scores,array("greenppt","blueppt","redppt")),"PPT","ppt_chart", NULL, $colorPrefs['redServer'], $colorPrefs['blueServer'], $colorPrefs['greenServer']); ?>
    </div>
    <div class="span6">
        <?= generate_googleChart(generate_jsontable($scores,array("greenscore","bluescore","redscore")),"Score","score_chart", NULL, $colorPrefs['redServer'], $colorPrefs['blueServer'], $colorPrefs['greenServer']); ?>
    </div>
</div>

<div class="row">
    <div class="span6">
        <?= generate_googleChart(generate_jsontable($scores,array("greenkills","bluekills","redkills")),"Kills","kills_chart", NULL, $colorPrefs['redServer'], $colorPrefs['blueServer'], $colorPrefs['greenServer']); ?>
    </div>
    <div class="span6">
        <?= generate_googleChart(generate_jsontable($scores,array("greendeaths","bluedeaths","reddeaths")),"Deaths","deaths_chart", NULL, $colorPrefs['redServer'], $colorPrefs['blueServer'], $colorPrefs['greenServer']); ?>
    </div>
</div>

<div class="row">
    <div class="span6">
        <?= generate_googleChart(generate_jsontable($scores,array("greenkdr","bluekdr","redkdr")),"KDR","kdr_chart", NULL, $colorPrefs['redServer'], $colorPrefs['blueServer'], $colorPrefs['greenServer']); ?>
    </div>
    <div class="span6">
        <?= generate_googleChart(generate_jsontable($skirmish_points,array("green_skirmish_score","blue_skirmish_score","red_skirmish_score")),"Skirmish Points","skirmish_chart", NULL, $colorPrefs['redServer'], $colorPrefs['blueServer'], $colorPrefs['greenServer']); ?>
    </div>
</div>

<?php include "static/includes/footer.php"; ?>