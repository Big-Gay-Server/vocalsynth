<?php
require_once '../Spyc.php';

$charaName = ($_GET['chara'] ?? null);
$urlvb = ($_GET['vb'] ?? null);
$charaPath = '/voicebanks/' . $charaName . '/';
$dirPath = './' . $charaName . '/';

$charaInfo = Spyc::YAMLLoad($dirPath . 'info.yaml');

$voicebanks = [];
$folders = glob($dirPath . '/*', GLOB_ONLYDIR);

if ($charaName) {
	foreach ($folders as $path) {
		$data = Spyc::YAMLLoad($path . '/info.yaml');
		$data['path'] = $path;
		$data['id'] = basename($path);
		$iconFiles = glob($path . '/icon.{png,jpg,jpeg}', GLOB_BRACE);
		$data['icon'] = !empty($iconFiles) ? str_replace($dirPath, $charaPath, $iconFiles[0]) : $charaPath . $data['id'] . '/icon.png';
		$voicebanks[] = $data;
	}

	usort($voicebanks, fn($a, $b) => $b['num'] <=> $a['num']);

	if ($urlvb) {
		// VOICEBANK PAGE
		$currentvb = null;
		foreach ($voicebanks as $vb) {
			if ($vb['id'] === $urlvb) {
				$currentvb = $vb;
				break;
			}
		}
		if ($currentvb) {
			?>

			<h1><?= $currentvb['vbname'] ?></h1>
			<div class="row">
				<div class="column flex50">
					<?= $currentvb['vbblurb'] ?>
				</div>
				<div class="column flex50">
					<img class="columnimg" src="<?= $charaPath . $vb['id'] . '/key.png' ?>"><br><br>
					<center>
						Key Art:
						<a href="<?= $currentvb['keyartistlink'] ?>"> <?= $currentvb['keyartist'] ?> </a>
					</center>
				</div>
			</div>

			<?php

			// main download buttons
			if ($currentvb['dllink']) {
				echo "<div class='center row' style='display: flex; justify-content: center; gap: 20px;'>";
				foreach ($currentvb['dllink'] as $dl) {
					?>
				
					<div class="column flex33">
						<a href="<?= htmlspecialchars($dl['url']) ?>">
							<img src="/_assets/<?= htmlspecialchars($dl['type']) ?> dl.png" class="columnimg">
						</a>
					</div>
				
				<?php
				}
				echo '</div>';
			}
			echo '<hr><br>';

			// subbanks list
			foreach ($currentvb['subbanks'] as $sub) {
				?>
				<div class="center row" style="display: flex; justify-content: center; gap: 20px;">
					<div class="column flex10"><br><img src="<?= $sub['icondeco'] ?>" class="columnimg"></div>
					<div class="column flex80">
						<h2>-<?= $sub['name'] ?>-</h2>
						<center><p><?= $sub['desc'] ?></p><center>
					</div>
					<div class="column flex10"><br><img src="<?= $sub['icondeco'] ?>" class="columnimg"></div>
				</div>
				<div class="center row" style="display: flex; justify-content: center; gap: 20px;">
					<?php if (!empty($sub['dllink']) && is_array($sub['dllink'])):
						foreach ($sub['dllink'] as $dl): ?>
					<div class="column flex33">
					<a href="<?= htmlspecialchars($dl['url']) ?>">
						<img src="/_assets/<?= htmlspecialchars($dl['type']) ?> dl.png" class="columnimg">
					</a>
					</div>
					<?php
						endforeach;
					endif;
					?>
					
				</div>
				<br>
				<div id="<?= $sub['name'] ?>box" class="voicebankbox">
					<div class="row" style="display: flex; justify-content: center; gap: 20px;">
						<img src="<?= $sub['iconface'] ?>" class="vbicon column columnimg">
						<div class="column centervertical">
							<?php
							foreach ($sub['expressions'] as $expression) {
								?>
							<div id="<?= $sub['name'] ?>_<?= $expression['name'] ?>" class="row">
								<div class="column expressioninfo flex33">
									<b><?= $expression['name'] ?></b>
									(<?= $expression['type'] ?>)<br>Pitches: <?= $expression['pitches'] ?>
								</div>
								<div class="column">
									<audio controls>
										<source src="<?= $expression['demo'] ?>" type="audio/wav">
									</audio>
								</div>
							</div>
							<?php
							}
							?>
						</div>
					</div>
				</div>

				<br>
				<hr><br>
				<?php
			}
		}
	} else {
		// CHARACTER PAGE
		?>

	<img src="<?= $charaPath ?>logo.png" style="width:100%">
	<div class="row">
		<div class="column flex50">
			<img class="columnimg" src="<?= $charaPath ?>key.png"><br><br>
			<center>
				Voice Provider : <a href="<?= $charaInfo['vplink']; ?>"><?= $charaInfo['vp']; ?></a><br>
				Key Art: <a href="<?= $charaInfo['keyartistlink']; ?>"><?= $charaInfo['keyartist']; ?></a>
			</center>
		</div>
		<div class="column flex50">
			<img class="columnimg" src="../../banner.png"><br><br>
			<center>
				<div id="bio">
					<b><?= strtoupper($charaInfo['title']); ?></b><br><br>
					<b><?= strtoupper($charaInfo['age']); ?></b><br>(<?= $charaInfo['appears']; ?>)<br><br>
					<b><?= strtoupper($charaInfo['birthday']); ?></b> <br><br>
					<b><?= strtoupper($charaInfo['heightcm']); ?> cm // <?= strtoupper($charaInfo['heightft']); ?></b> <br><br>
					<b><?= strtoupper($charaInfo['gender']); ?></b><br>(<?= $charaInfo['pronouns']; ?>)<br><br>
				</div>
			</center>

			<?= $charaInfo['desc']; ?><br><br>
			
			<iframe width="100%" height="254px" src="<?= $charaInfo['coverplaylist']; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
		</div>
	</div>

		<br><hr style="width: 80%;">
		
	<div id="voicebanks">
		<h1>Voicebanks</h1>
		<?php foreach ($voicebanks as $vb): ?>
			<div id="<?= $vb['id'] ?>" class="voicebankbox">
				<h2><?= $vb['vbname'] ?></h2>
				<div class="row">
					<img src="<?= $vb['icon'] ?>" width="20%" height="20%" class="vbicon column">
					<p class="column center vbinfo">
						<b><?= $vb['vbsub'] ?></b><br><br>
						<?= $vb['vbdesc'] ?><br><br>
						<a href="<?= $charaPath . $vb['id'] ?>"><button type="button">Downloads</button></a>
					</p>
				</div>
			</div><br>
		<?php endforeach; ?>
	</div>
	<?php
	}
} else {
	?>
	<h1 class="title">Children of Dust</h1>
		<center>
			A group of UTAU voicebanks linked to characters in the
			<a href="https://lunatine.lunarconstruct.net">Lunatine</a>
			Universe.
			<br><br>
			<a href="merisdae"><img src="merisdae/preview inactive.png" class="vbpreview" onmouseover="this.src='merisdae/preview.png';" onmouseout="this.src='merisdae/preview inactive.png'"></a>
			<a href="canele"><img src="canele/preview inactive.png" class="vbpreview" onmouseover="this.src='canele/preview.png';" onmouseout="this.src='canele/preview inactive.png'"></a>
			<a href="yudora"><img src="yudora/preview inactive.png" class="vbpreview" onmouseover="this.src='yudora/preview.png';" onmouseout="this.src='yudora/preview inactive.png'"></a>
		</center>
		<br/>

		<br>
		<h1 class="title">Other Voicebanks</h1>
		<center>
			Other voicebanks managed by me, but not part of Lunatine.<br><br>
			<a href="hawa"><img src="hawa/preview inactive.png" class="vbpreview" onmouseover="this.src='hawa/preview.png';" onmouseout="this.src='hawa/preview inactive.png'"></a>
			<a href="ayda"><img src="ayda/preview inactive.png" class="vbpreview" onmouseover="this.src='ayda/preview.png';" onmouseout="this.src='ayda/preview inactive.png'"></a>
			<a href="aurora"><img src="aurora/preview inactive.png" class="vbpreview" onmouseover="this.src='aurora/preview.png';" onmouseout="this.src='aurora/preview inactive.png'"></a>
			<a href="boofer"><img src="boofer/preview inactive.png" class="vbpreview" onmouseover="this.src='boofer/preview.png';" onmouseout="this.src='boofer/preview inactive.png'"></a>
		</center>
	<?php
}
?>