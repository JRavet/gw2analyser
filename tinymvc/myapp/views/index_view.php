<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<title>Welcome to TinyMVC!</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
	</head>
	<body>
		<?php
			echo "<table><tr>";
			foreach ($data[0] as $key=>$value)
			{
				if (preg_match("/details|id/i",$key)) continue;
				echo "<th>$key</th>";
			}
			echo "</tr>";
			foreach($data as $ch)
			{
				if (preg_match("/Ruins|Spawn/i",$ch['place'])) {continue;}
				echo "<tr style='Background-color: $ch[owner_color]'>";
				foreach($ch as $key=>$value)
				{
					if (preg_match("/details|id/i",$key)) continue;
					echo "<td>$value</td>";
				}
				echo "</tr>";

				foreach($ch['details'] as $c)
				{
					echo "<tr><td>".$c['type']."</td>";
					foreach($c as $k=>$v)
					{
						if (preg_match("/capture_history_id|^id/i",$k)) {continue;}
						echo "<td>$k=>$v</td>";
					}
					echo "</tr>";
				}
			}
			echo "</table>";
		?>
	</body>
</html>


