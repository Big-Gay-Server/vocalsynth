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
if ($requested_cover) {
    // Check if the array is NOT empty before accessing index 0
    if (!empty($cover)) {
        $manager->coverDetails($cover[0]);
    } else {
        echo '<h1>Cover not found</h1>';
        var_dump($requested_cover); 
        echo '<p><a href="?">Back to all covers</a></p>';
    }
} else {
    echo '<h1>Covers</h1>';
    echo '<div class="covercontainer">';
        $manager->renderGrid($all_covers);
    echo '</div>';
}

