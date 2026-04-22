<?php
header('Content-Type: application/xml; charset=utf-8');
$site_url = 'https://vocalsynth.lunarconstruct.net';
$dir = './posts/';
require_once __DIR__ . '/../includes/functions.php'; // Fixed path to your autoloader

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

    $files = glob($dir . '*.{html,md}', GLOB_BRACE);

    $all_posts = [];
    foreach ($files as $file) {
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
                'filename' => basename($file),
                'timestamp' => $timestamp,
                'yaml' => $yaml,
                'body' => trim($parts[2])
            ];
        }
    }

    // Sort by newest first
    usort($all_posts, function ($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });

    foreach ($all_posts as $post) {
      $body = $post['body'];
      $yaml = $post['yaml'];
      $date = date(DATE_RSS, $post['timestamp']);
      $filename = $post['filename'];

      // 1. Title Priority: YAML > # Header > Filename
      if (!empty($yaml['title'])) {
          $title = htmlspecialchars($yaml['title']);
      } elseif (preg_match('/^#+\s+(.+)$/m', $body, $matches)) {
          $title = htmlspecialchars($matches[1]);
      } else {
          $title = ucwords(str_replace(['.html', '.md', '_', '-'], ['', '', ' ', ' '], $filename));
      }

      $clean_name = str_replace(['.md', '.html'], '', $filename);
      $link = $site_url . '/news/' . $clean_name;

      // Clean for preview using the body only
      $html_content = $Parsedown->text($body);
      $plain_text = strip_tags($html_content);
      $clean_preview = str_replace(["\r", "\n"], ' ', $plain_text);
      $clean_preview = str_replace($title, '', $clean_preview);
      $description = htmlspecialchars(mb_substr(trim($clean_preview), 0, 200)) . '...';

      echo "<item><title>$title</title><link>$link</link><description>$description</description><pubDate>$date</pubDate><guid>$link</guid></item>";
    }
    ?>
  </channel>
</rss>
