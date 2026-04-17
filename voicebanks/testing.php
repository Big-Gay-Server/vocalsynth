<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/Spyc.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/covermanager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/voicebankmanager.php';

$urlchara = ($_GET['chara'] ?? null);
$urlvb = ($_GET['vb'] ?? null);

$chara_covermanager = new VoicebankManager();

if ($urlchara) {
    $allCharacters = $chara_covermanager->getCharacters();
    $charaInfo = null;
    foreach ($allCharacters as $c) {
        if ($c['slug'] === $urlchara) {
            $charaInfo = $c;
            break;
        }
    }

    if (!$charaInfo) {
        echo 'Character not found.';
        return;
    }

    $voicebanks = $chara_covermanager->getBanksByCharacter($urlchara);
    $charaPath = '/' . $urlchara . '/';

    if ($urlvb) {
        $currentvb = $chara_covermanager->getSpecificBank($voicebanks, $urlvb);
        ?>
        <h1><?= $currentvb['vbname'] ?></h1>
			<div class="row">
				<div class="column flex50">
					<?= $currentvb['vbblurb'] ?>
				</div>
				<div class="column flex50">
                <img class="columnimg" src="<?= $urlchara ?>/<?= $currentvb['id'] ?>/key.png"><br><br>
					<center>
						Key Art: <a href="<?= $currentvb['keyartistlink'] ?>"> <?= $currentvb['keyartist'] ?> </a>
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
					    <?php endforeach;
                    endif; ?>
				</div>
				<br>
				<div id="<?= $sub['name'] ?>box" class="voicebankbox">
					<div class="row" style="display: flex; justify-content: center; gap: 20px;">
						<img src="<?= $sub['iconface'] ?>" class="vbicon column columnimg">
						<div class="column centervertical">
                            <?php 
                            // Add this IF check to prevent the crash
                            if (!empty($sub['expressions']) && is_array($sub['expressions'])): 
                                foreach ($sub['expressions'] as $expression): 
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
                                endforeach; 
                            endif; // End of the IF check
                            ?>
                        </div>
					</div>
				</div>
                
			<?php } // end of subbanks loop ?>

            <hr>
            <h1>Covers</h1>

            <?php
            $vb_covermanager = new CoverManager();

            // set filters
            $selected_vb = $charaInfo['name'] ?? $charaName;
            $filters = [
                'requireVideo' => true,
                'minYear' => 2016,
                'charaFolder' => $selected_vb,
                'voicebank' => $currentvb['vbname']
            ];

            // fetch covers
            $vbcovers = $vb_covermanager->getCovers($filters);
            ?>
            
            <div class="covercontainer">
                <?php
                $vb_covermanager->renderGrid($vbcovers);
                ?>
            </div>
            <?php
    } else {
        ?>
        <img src="<?= $charaInfo['slug'] ?>/logo.png" style="width:100%">
        <div class="row">
            <div class="column flex50">
                <img class="columnimg" src="<?= $charaInfo['slug'] ?>key.png"><br><br>
                <center>
                    Voice Provider : <a href="<?= $charaInfo['vplink']; ?>"><?= $charaInfo['vp']; ?></a><br>
                    Key Art: <a href="<?= $charaInfo['keyartistlink']; ?>"><?= $charaInfo['keyartist']; ?></a>
                </center>
            </div>
            <div class="column flex50">
                <img class="columnimg" src="/banner.png"><br><br>
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
                            <a <a href="<?= $charaInfo['slug'] . '/' . $vb['id'] ?>"><button type="button">Downloads</button></a>><button type="button">Downloads</button></a>
                        </p>
                    </div>
                </div><br>
            <?php endforeach; ?>
        </div>
        <hr>
            <h1>Covers</h1>

            <?php
        $covermanager = new CoverManager();

        // set filters
        $selected_vb = $charaInfo['slug'] ?? $charaName;
        $filters = [
            'requireVideo' => true,
            'minYear' => 2016,
            'charaFolder' => $selected_vb
        ];

        // fetch covers
        $vbcovers = $covermanager->getCovers($filters);

        echo '<div class="covercontainer">';
        echo $covermanager->renderGrid($vbcovers);
        echo '</div>';
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