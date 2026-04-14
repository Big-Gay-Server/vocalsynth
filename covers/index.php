<?php
// library imports n stuff
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;
use Cocur\Slugify\Slugify;
$slugify = new Slugify();

// define function that pulls yaml frontmatter from obsidian .md files
function getFrontMatter($filePath) {
    $content = file_get_contents($filePath);
    $parts = explode('---', $content);
    if (count($parts) >= 3) {
        return Yaml::parse(trim($parts[1]));
    }
    return [];
}

// initialize an array variable that will soon hold all covers
$all_covers = [];
// set $folders variable to an array of directories
$folders = glob('*/', GLOB_ONLYDIR);

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

echo '<h1>Covers</h1>';
echo '<div class="covercontainer">';

// cover grid
foreach ($all_covers as $coverdata) {
    // link logic
    $coverpage = $coverdata['file_path'] ?? ''; // defines link to cover page
    
    // date logic
    $dateformat = 'F j, Y';
    $date = isset($coverdata['date']) ? date($dateformat, strtotime($coverdata['date'])) : '';
    $year = $coverdata['Year'] ?? '';

    // song title logic
    $origname = $coverdata['orig name'] ?? ''; // defines the original language name of song
    $romname = $coverdata['en/rom name'] ?? ''; // defines the english/romanized name of song
    $namessame = $origname == $romname ? true : false; // if they're the same, set $namessame to false for formatting
    if (!empty($origname) && !empty($romname)) { // defines the complete song name
        $song = $namessame ? $origname : "$origname / $romname";
    } else {
        $song = '';
    }
    // concatenates the title line, adding the year if there are multiple covers of the song.
    $title = $coverdata['is_multicover'] ? $song . " (" . $year . " ver)" : $song; 

    // voicebank logic
    $vb_data = $coverdata['voicebank'] ?? '';
    $vb = is_array($vb_data) ? implode(', ', $vb_data) : $vb_data;
    // loop thru voicebanks and find one that matches, to link?
    $vb_link = '';
    $all_vbs = glob('/voicebanks/*/info.yml');
        foreach ($all_vbs as $voicebank) {
            $voicebank_info = Yaml::parseFile($voicebank);
    
            if (($voicebank_info['name'] ?? '') === $vb) {
                $folder_name = basename(dirname($voicebank));
                $vb_link = '/voicebanks/' . $folder_name;
                break;
            }
        }

    // original song info logic
    $artist_data = $coverdata['music & lyrics'] ?? '';
    $artist = is_array($artist_data) ? implode(', ', $artist_data) : $artist_data;

    $ogvo_data = $coverdata['original vocals'] ?? '';
    $ogvo = is_array($ogvo_data) ? implode(', ', $ogvo_data) : $ogvo_data;

    $byline = ($artist == $ogvo) ? $artist : $artist . ' ft. ' . $ogvo;

    $lyric_data = $coverdata['lyric for desc'] ?? '';
    $lyric = is_array($lyric_data) ? implode(', ', $lyric_data): $lyric_data;
    $lyric_format = !empty($lyric) ? '"' . $lyric . '"' : '';
    
    // video logic
    $raw_video = $coverdata['video link'] ?? '';
    $video_link = is_array($raw_video) ? ($raw_video[0] ?? '') : $raw_video;
    $embed_url = preg_replace(
        "/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\s&]+)/",
        "https://youtube.com/embed/$1",
        $video_link
    );
    $video = !empty($video_link) ? '<div class="video-container"><iframe src="' . htmlspecialchars($embed_url) . '" frameborder="0" allowfullscreen></iframe></div>' : '';

    // if cover meets below requirements, output a box with the info in it.
    if (!empty($raw_video) && $year >=2016) {
        ?>
        <div class="coverbox">
            <?=$video ?> 
            <a href="<?=$coverpage ?>"><h3><?= htmlspecialchars($title) ?></h3></a>
            <a href="<?= $vb_link ?>"><h4><?= htmlspecialchars($vb) ?></h4></a>
            <h5><?= htmlspecialchars($byline) ?></h5>
            <h6><?= htmlspecialchars($date) ?></h6>
        </div>
        <?php
    }
}
echo '</div>';
