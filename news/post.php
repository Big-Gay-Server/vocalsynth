<?php
require_once __DIR__ . '/Parsedown.php';

class ParsedownAudio extends Parsedown {
    public function __construct() {
        // Add "Audio" to the list of things Parsedown looks for when it sees "!"
        // This keeps the original "Image" logic intact too.
        $this->InlineTypes['!'][] = 'Audio';
    }

    protected function inlineAudio($Excerpt) {
        // Look for the ![[filename.wav]] pattern
        if (preg_match('/^!\[\[(.+\.(wav|mp3|ogg))\]\]/', $Excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'audio',
                    'attributes' => [
                        'controls' => 'controls',
                        'src' => 'posts/' . $matches[1], // Ensure the path points to your posts folder
                    ],
                ],
            ];
        }
    }
}

$Parsedown = new ParsedownAudio();

$post_dir = __DIR__ . '/posts/'; // set folder the posts are in
$file = $_GET['f'] ?? ''; // get the requested post from url and set it as $file

$path = realpath($post_dir . $file); // make the path from the directory and file

// Verify the file exists and is inside the posts folder
if ($path && strpos($path, $post_dir) === 0 && file_exists($path)) {
    $markdown = file_get_contents($path);
    $parts = explode('---', $markdown);

    // Case 1: Standard YAML (--- info --- content)
    if (count($parts) >= 3) {
        $markdown = trim($parts[2]);
    } 
    // Case 2: Only one separator (--- info [everything else])
    elseif (count($parts) == 2) {
        // We need to split the metadata from the content
        // Since there's no closing ---, we look for the first newline after the dashes
        $contentParts = explode("\n", trim($parts[1]), 2);
        
        // We skip the first few lines of YAML and take the rest
        // Or more safely, just look for the first '#' which starts your Markdown
        $markdown = trim(strstr($parts[1], '#')); 

        if (empty($markdown)) {
            $markdown = trim($parts[1]); // Fallback if no # header found
        }

        if (!empty(trim($parts[0]))) {
            $markdown = file_get_contents($path);
        }
    }

?>
    <article class='blog-post' style='max-width: 900px; margin: auto;'>
        <?= $Parsedown->text($markdown) ?>
    </article>
<?php

} else {
    echo "<h1>Post not found.</h1>";
}

// back to news page
?>
<div class='center' style='margin-top: 50px;'>
    <a href='/news' style='text-decoration: none;'>
        <button type='button'>← Back to News</button>
    </a>
</div>