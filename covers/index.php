<?php
// library imports n stuff
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;
use Cocur\Slugify\Slugify;
$slugify = new Slugify();

// initialize an array variable that will soon hold all covers
$all_covers = [];
// set $folders variable to an array of directories
$folders = glob(__DIR__ . '/*/', GLOB_ONLYDIR);

// preliminary pass through the folders
foreach ($folders as $song_folder) {
    $files = glob($song_folder . '*.md');
    $multicover = count($files) >= 2;  // if there's more than one cover of a song, mark it for later processing
    foreach ($files as $file) {
        $data = getFrontMatter($file);
        if ($data) {
            $data['file_path'] = $file;
            $data['is_multicover'] = $multicover;
            $all_covers[] = $data;
        }
    }
}

// sort through all covers by date, newest first
usort($all_covers, function($a, $b) {
    $dateA = isset($a['date']) ? strtotime($a['date']) : 0;
    $dateB = isset($b['date']) ? strtotime($b['date']) : 0;
    return $dateB <=> $dateA;
});

// preliminary pass through voicebanks to gather
$vb_map = [];
$vb_base_path = '/usr/share/nginx/html/vocalsynth/voicebanks';

$all_vbs = glob($vb_base_path . '/*/*/{info.yml,info.yaml}', GLOB_BRACE) ?: [];

if ($all_vbs) {
    foreach ($all_vbs as $vb_file) {
        $info = Yaml::parseFile($vb_file);
        $raw_names = $info['vbname'] ?? '';
        
        if ($raw_names) {
            $names_array = explode(',', $raw_names);
            $url_path = str_replace('/usr/share/nginx/html/vocalsynth', '', dirname($vb_file));
            
            foreach ($names_array as $name) {
                $vb_map[strtolower(trim($name))] = $url_path;
            }
        }
    }
}

?>
<h1>Covers</h1>
<div class="covercontainer">
    <?= renderCoverGrid($all_covers, $vb_map) ?>
</div>

