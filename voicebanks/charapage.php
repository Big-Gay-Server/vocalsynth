<img src="<?= $charaInfo['slug'] ?>/logo.png" style="width:100%">
<div class="row">
    <div class="column flex50">
        <img class="columnimg" src="<?= $charaInfo['slug'] ?>/key.png"><br><br>
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
                <b><?= strtoupper($charaInfo['heightcm']); ?> cm // <?= strtoupper($charaInfo['heightft']); ?></b>
                <br><br>
                <b><?= strtoupper($charaInfo['gender']); ?></b><br>(<?= $charaInfo['pronouns']; ?>)<br><br>
            </div>
        </center>

        <?= $charaInfo['desc']; ?><br><br>

        <iframe width="100%" height="254px" src="<?= $charaInfo['coverplaylist']; ?>" title="YouTube video player"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </div>
</div>

<br>
<hr style="width: 80%;">

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
                    <a href="<?= $charaInfo['slug'] . '/' . $vb['id'] ?>"><button type="button">Downloads</button></a>
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