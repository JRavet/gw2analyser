<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<title>Welcome to TinyMVC!</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
		<style>
		<?=$css?>
		</style>
	</head>
	<body>
	<br/>

			<table class='table table-bordered data-table'>
				<tr>
			<?php
			foreach ($data[0] as $key=>$value) {
				if (preg_match("/details|id/i",$key)) continue; ?>
				<th><?=$key?></th>
			<?php
			}
			?>
				</tr>
			<?php
			foreach($data as $ch) {
				if (preg_match("/Ruins|Spawn/i",$ch['place'])) continue; ?>

				<tr style="Background-color: <?=$ch["owner_color"]?>">
				<?php
				foreach($ch as $key=>$value) {
					if (preg_match("/details|id/i",$key)) continue; ?>
					<td><?=$value?></td>
				<?php
				}
				?>
				</tr>
				<?php
				foreach($ch['details'] as $c)
				{
				?>
					<tr>
						<td><?= $c['type'] ?></td>
					<?php
					foreach($c as $k=>$v)
					{
						if (preg_match("/type|capture_history_id|^id/i",$k)) continue; ?>
						<td><?=$k .'=>'. $v?></td>
					<?php
					}
					?>
					</tr>
					<?php
				}
			}
			?>
			</table>
	</body>
	<script>
	<?=$js?>
	</script>
</html>


