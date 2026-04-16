<?php
// imports
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/covermanager.php';

// initialize the class
$manager = new CoverManager();

// fetch covers
$all_covers = $manager->getCovers(['limit' => 1]); 

// display covers!!! so easy
echo '<h1>Covers</h1>';
echo '<div class="covercontainer">';
    $manager->renderGrid($all_covers); 
echo '</div>';