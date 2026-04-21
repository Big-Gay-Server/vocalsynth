<?php
// imports
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/covermanager.php';

// initialize the class
$manager = new CoverManager();

$requested_cover = ($_GET['song'] ?? null);

// set filters
$default_filters = [
    'requireVideo' => true,
    'minYear' => 2016,
];

$specific_filters = [
    'title' => $requested_cover,
];

// fetch covers
$all_covers = $manager->getCovers($default_filters);

$cover = $manager->getCovers($specific_filters); 

// display covers!!! so easy
echo '<h1>Covers</h1>';
echo '<div class="covercontainer">';
    $manager->renderGrid($cover);
    $manager->renderGrid($all_covers); 
echo '</div>';