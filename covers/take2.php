<?php
echo 'this page will be covers!! give me a sec lmfao';
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

function getFrontMatter($filePath) {
    $content = file_get_contents($filePath);
    $parts = explode('---', $content);
    if (count($parts) >= 3) {
        return Yaml::parse(trim($parts[1]));
    }
    return [];
}

$all_covers = [];
$folders = glob('*/', GLOB_ONLYDIR);

foreach ($folders as $song_folder) {
    $files = glob($song_folder . '*.md');
    $multicover = count($files) >= 2; 
    foreach ($files as $file) {
        $data = getFrontMatter($file);
        if ($data) {
            $data['file_path'] = $file;
            $data['is_multicover'] = $multicover; // Store this for the title logic later
            $all_covers[] = $data;
        }
    }
}

usort($all_covers, function($a, $b) {
    $dateA = isset($a['date']) ? strtotime($a['date']) : 0;
    $dateB = isset($b['date']) ? strtotime($b['date']) : 0;
    return $dateB <=> $dateA;
});

echo '<div class="covercontainer">';

// JUST ONE LOOP HERE
foreach ($all_covers as $coverdata) {
    $coverpage = $coverdata['file_path'];
    $song = $coverdata['song name'];

    $vb_data = $coverdata['voicebank'] ?? '';
    $vb = is_array($vb_data) ? implode(', ', $vb_data) : $vb_data;

    $artist_data = $coverdata['music & lyrics'] ?? '';
    $artist = is_array($artist_data) ? implode(', ', $artist_data) : $artist_data;

    $ogvo_data = $coverdata['original vocals'] ?? '';
    $ogvo = is_array($ogvo_data) ? implode(', ', $ogvo_data) : $ogvo_data;

    $year = $coverdata['Year'] ?? '';
    
    $title = $coverdata['is_multicover'] ? $song . " (" . $year . ")" : $song;

    $lyric_data = $coverdata['lyric for desc'] ?? '';
    $lyric = is_array($lyric_data) ? implode(', ', $lyric_data): $lyric_data;
    $lyric_format = !empty($lyric) ? '"' . $lyric . '"' : '';
    
    $raw_video = $coverdata['video link'] ?? '';
    $video_link = is_array($raw_video) ? ($raw_video[0] ?? '') : $raw_video;
    $embed_url = preg_replace(
        "/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\s&]+)/",
        "https://youtube.com/embed/$1",
        $video_link
    );
    $video = !empty($video_link) ? '<div class="video-container"><iframe src="' . htmlspecialchars($embed_url) . '" frameborder="0" allowfullscreen></iframe></div>' : '';
    $byline = ($artist == $ogvo) ? $artist : $artist . ' ft. ' . $ogvo;

    $dateformat = 'F j, Y';
    $date = isset($coverdata['date']) ? date($dateformat, strtotime($coverdata['date'])) : '';

    if (!empty($raw_video)) {
        ?>
        <div class="coverbox">
            <?=$video ?> 
            <a href="<?=$coverpage ?>"><h2><?= htmlspecialchars($title) ?></h2></a>
            <h4><?= htmlspecialchars($vb) ?></h4>
            <h6><?= htmlspecialchars($date) ?></h6>
            <p><?= htmlspecialchars($byline) ?></p>
            <p><?= htmlspecialchars($lyric_format) ?></p>
        </div>
        <?php
    }
}
echo '</div>';
