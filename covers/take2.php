<?php
echo 'this page will be covers!! give me a sec lmfao';

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$folders = glob('*/', GLOB_ONLYDIR);
$covers = glob('*/*.md');

// this function takes a variable (internally calling it $filepath) and extracts the frontmatter.
function getFrontMatter($filePath)
{
    
    $content = file_get_contents($filePath);
    $parts = explode('---', $content);
    if (count($parts) >= 3) {
        return Yaml::parseFile(trim($parts[1]));
    }
    return [];
}

echo '<div class="covercontainer">';

foreach ($folders as $song) {
    $covers = glob($song . '*.md');
    $multicover = count($covers) >= 2; 

    foreach ($covers as $cover) {
        $coverdata = getfrontmatter($cover);
        $coverpage = $cover; //im gonna use this later to link to the md files but i gotta parse em...

        $song = $coverdata['song name'];

        $vb_data = $coverdata['voicebank'] ?? '';
        $vb = is_array($vb_data) ? implode(', ', $vb_data) : $vb_data;

        $artist_data = $coverdata['music & lyrics'] ?? '';
        $artist = is_array($artist_data) ? implode(', ', $artist_data) : $artist_data;

        $ogvo_data = $coverdata['original vocals'] ?? '';
        $ogvo = is_array($ogvo_data) ? implode(', ', $ogvo_data) : $ogvo_data;

        $year = $coverdata['Year'];

        $title = $multicover ? $song . " (" . $year . ")" : $song;

        $lyric_data = $coverdata['lyric for desc'] ?? '';
        $lyric = is_array($lyric_data) ? implode(', ', $lyric_data): $lyric_data;
        $lyric_format = !empty($lyric) ? '"' . $lyric . '"' : '';
        
        $comment_data = $coverdata['comments'] ?? '';
        $comment = is_array($comment_data) ? implode(', ', $comment_data): $comment_data;

        $desc = $lyric_format . "<br>" . $comment;

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

    ?>

    <div class="coverbox">
        <?=$video ?> 
        <a href="<?=$coverpage ?>"><h2><?= $title ?></h2></a>
        <h4><?= $vb ?></h4>
        <h6><?= $date ?></h6>
        <p><?= $byline ?></p>
        <p><?= $lyric_format ?></p>
    </div>


    <?php
    }
}
echo '</div>';