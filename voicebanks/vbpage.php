<?php
require_once 'index.php';
?>

<h1>
    <?= $currentvb['vbname'] ?>
</h1>
<div class="row">
    <div class="column flex50">
        <?= $currentvb['vbblurb'] ?>
    </div>
    <div class="column flex50">
        <img class="columnimg" src="/voicebanks/<?= $urlchara ?>/<?= $currentvb['id'] ?>/key.png"><br><br>
        <center>
            Key Art:
            <a href="<?= $currentvb['keyartistlink'] ?>">
                <?= $currentvb['keyartist'] ?>
            </a>
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
            <h2>-
                <?= $sub['name'] ?>-
            </h2>
            <center>
                <p>
                    <?= $sub['desc'] ?>
                </p>
                <center>
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
                foreach ($sub['expressions'] as $expression) {
                ?>
                    <div id="<?= $sub['name'] ?>_<?= $expression['name'] ?>" class="row">
                        <div class="column expressioninfo flex33">
                            <b>
                                <?= $expression['name'] ?>
                            </b>
                            (
                            <?= $expression['type'] ?>)<br>Pitches:
                            <?= $expression['pitches'] ?>
                        </div>
                        <div class="column">
                            <audio controls>
                                <source src="<?= $expression['demo'] ?>" type="audio/wav">
                            </audio>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<hr>
<h1>Covers</h1>

<?php
$vbmanager = new CoverManager();

// set filters
$selected_vb = $charaInfo['name'] ?? $charaName;
$filters = [
    'requireVideo' => true,
    'minYear' => 2016,
    'charaFolder' => $selected_vb,
    'voicebank' => $currentvb['vbname']
];

// fetch covers
$vbcovers = $vbmanager->getCovers($filters);
?>

<div class="covercontainer">
    <?php $vbmanager->renderGrid($vbcovers); ?>
</div>
