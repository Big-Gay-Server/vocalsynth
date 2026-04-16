<?php
// imports
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/covermanager.php';
?>

<h1>Welcome to LunarConstruct's UTAU Site!</h1>
Hey hey! Name's Lunar!
<span id="age"></span>, they/them
<br><br>

I've been working with Vocal Synthesis programs since around 2010! I mostly focus on UTAU projects - I am the voice provider and manager of the UTAU Merisdae, as well as the manager of the Children of Dust UTAU group which contains a bunch of voicebanks voiced by my awesome friends :) I've also dabbled in other VocalSynth programs, such as Vocaloid and DeepVocal - but my focus is definitely UTAU!<br><br>

This site was entirely coded by me by hand, so please forgive any scuff! I am hoping for it to be fully mobile friendly very soon!!<br><br>

Thanks for stopping by!!!

<br/>
<br/>
<br>

<?php
// initialize the class
$manager = new CoverManager();

// fetch covers
$all_covers = $manager->getCovers(['limit' => 3]); 

// display covers!!! so easy
echo '<h2>Latest Cover</h2>';
echo '<div class="covercontainer">';
    $manager->renderGrid($all_covers); 
echo '</div>';
