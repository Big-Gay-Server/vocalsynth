<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/Spyc.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/covermanager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/voicebankmanager.php';

$urlchara = ($_GET['chara'] ?? null); // grab character name from query
$urlvb = ($_GET['vb'] ?? null); // grab voicebank name from query

$vbmanager = new VoicebankManager(); // initialize a voicebank manager

if ($urlchara) { // if url has a character specified,
    $allCharacters = $vbmanager->getCharacters(); // fetch all characters
    $charaInfo = null; // initialize character info variable

    foreach ($allCharacters as $c) { // loop thru the characters
        if ($c['slug'] === $urlchara) { // when the character matches:
            $charaInfo = $c; // set it as the character info variable
            $charaPath = $c['slug'] . '/'; // make a path variable from the slug
            $voicebanks = $vbmanager->getBanksByCharacter($c['slug']); // fetch all vbs for the selected character
            break; // and stop looping
        }
    }

    if (!$charaInfo) { // if a character was not found
        echo 'Character not found.';
        return;
    }

    if ($urlvb) { // if url has a voicebank specified,
        $currentvb = $vbmanager->getSpecificBank($voicebanks, $urlvb);
        include 'vbpage.php'; // show the vb page

    } else {
        include 'charapage.php'; // show the character page
    }
} else {
    $allCharacters = $vbmanager->getCharacters(); // fetch all characters
    $charaInfo = null; // initialize character info variable
    ?>
    <h1 class="title">Children of Dust</h1>
    <center>
        A group of UTAU voicebanks linked to characters in the
        <a href="https://lunatine.lunarconstruct.net">Lunatine</a>
        Universe.
        <br><br>
        <?php foreach ($allCharacters as $c) {
            if ($c['childrenofdust'] === true) { ?>
                <a href="<?= $c['slug'] ?>"><img src="<?= $c['slug'] ?>/preview inactive.png" class="vbpreview"
                    onmouseover="this.src='<?= $c['slug'] ?>/preview.png';" onmouseout="this.src='<?= $c['slug'] ?>/preview inactive.png'"></a>
                <?php }
        } ?>
            
    </center>
    <br />

    <br>
    <h1 class="title">Other Voicebanks</h1>
    <center>
        Other voicebanks managed by me, but not part of Lunatine.<br><br>
        <?php foreach ($allCharacters as $c) {
            if ($c['childrenofdust'] === false) { ?>
                <a href="<?= $c['slug'] ?>"><img src="<?= $c['slug'] ?>/preview inactive.png" class="vbpreview"
                    onmouseover="this.src='<?= $c['slug'] ?>/preview.png';" onmouseout="this.src='<?= $c['slug'] ?>/preview inactive.png'"></a>
                <?php }
        } ?>
    </center>
    <?php
    }

?>