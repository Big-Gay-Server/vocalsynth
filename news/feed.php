<?php
header('Content-Type: application/xml; charset=utf-8');
$site_url = 'https://vocalsynth.lunarconstruct.net';

require_once __DIR__ . '/../includes/functions.php'; // Fixed path to your autoloader
require_once __DIR__ . '/../includes/covermanager.php';

$postdir = './posts/';
$covermanager = new CoverManager();

$default_filters = [
    'requireVideo' => true,
    'minYear' => 2016,
];

echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<rss version="2.0">
  <channel>
    <title>LunarConstruct</title>
    <link><?php echo $site_url; ?></link>
    <description>Latest posts from my server folder</description>
    <?php
    require_once 'Parsedown.php';
    $Parsedown = new Parsedown();

    $posts = glob($postdir . '*.{html,md}', GLOB_BRACE);
    $covers = $covermanager->getCovers($default_filters);

    $all_posts = [];
    foreach ($posts as $file) {
        $raw_content = file_get_contents($file);
        $parts = explode('---', $raw_content, 3);
        
        if (count($parts) >= 3) {
            $yaml = Symfony\Component\Yaml\Yaml::parse(trim($parts[1]));
            if (!isset($yaml['public']) || $yaml['public'] !== true) continue;

            $timestamp = null;

            if (isset($yaml['date'])) {
                $d = $yaml['date'];
                
                if ($d instanceof DateTime) {
                    $timestamp = $d->getTimestamp();
                } elseif (is_numeric($d)) {
                    // This fixes your current issue: 
                    // If it's already a number (timestamp), use it directly
                    $timestamp = (int)$d;
                } else {
                    $timestamp = strtotime((string)$d);
                }
            }

            // Fallback to file time if no valid date found
            if (!$timestamp || $timestamp <= 0) {
                $timestamp = filemtime($file);
            }

            $all_posts[] = [
                'type' => 'news',
                'filename' => basename($file),
                'timestamp' => $timestamp,
                'yaml' => $yaml,
                'body' => trim($parts[2])
            ];
        }
    }

    $cover_posts = [];
    foreach ($covers as $raw_cover) {
        // Process the cover data using your helper
        $processed = $covermanager->processCoverData($raw_cover);
        
        $cover_posts[] = [
            'type' => 'cover', // Add this indicator
            'timestamp' => strtotime($raw_cover['date'] ?? 'now'),
            'title' => $processed['title'],
            'voicebank' => $processed['vb_display'],
            'link' => $site_url . '/covers?song=' . $processed['url_slug'],
            'description' => "New Cover: " . $processed['title'] . " ft. " . $processed['vb_display']
        ];
    }

    $merged_feed = array_merge($all_posts, $cover_posts);

    // Sort by newest first
    usort($merged_feed, function ($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });

    foreach ($merged_feed as $item) {
        $date = date(DATE_RSS, $item['timestamp']);
        
        if ($item['type'] === 'cover') {
            $title = "[COVER] " . "【UTAU カバー】 " . $item['title'] . "【" . $item['voicebank'] . "】";
            $link = $item['link'];
            $description = htmlspecialchars($item['description']);
        } else {
            // Fix: Use $item instead of $post
            $body = $item['body'];
            $yaml = $item['yaml'];
            $filename = $item['filename'];

            // Title logic (keep your priority logic)
            if (!empty($yaml['title'])) {
                $postTitle = $yaml['title'];
            } elseif (preg_match('/^#+\s+(.+)$/m', $body, $matches)) {
                $postTitle = $matches[1];
            } else {
                $postTitle = ucwords(str_replace(['.html', '.md', '_', '-'], ['', '', ' ', ' '], $filename));
            }

            $title = "[NEWS] " . htmlspecialchars($postTitle);
            $clean_name = str_replace(['.md', '.html'], '', $filename);
            $link = $site_url . '/news/' . $clean_name;

            // Preview logic
            $html_content = $Parsedown->text($body);
            $plain_text = strip_tags($html_content);
            $clean_preview = str_replace(["\r", "\n"], ' ', $plain_text);
            $description = htmlspecialchars(mb_substr(trim($clean_preview), 0, 200)) . '...';
        }

        echo "<item>
                <title>$title</title>
                <link>$link</link>
                <description>$description</description>
                <pubDate>$date</pubDate>
                <guid>$link</guid>
            </item>";
    }
    ?>
  </channel>
</rss>
