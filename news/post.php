<?php
require_once __DIR__ . '/Parsedown.php';

class ParsedownAudio extends Parsedown {
    public function __construct() {
        // Add "Audio" to the list of things Parsedown looks for when it sees "!"
        // This keeps the original "Image" logic intact too.
        $this->InlineTypes['!'][] = 'Audio';
    }

    protected function inlineAudio($Excerpt) {
        if (preg_match('/^!\[\[(.+\.(wav|mp3|ogg))\]\]/', $Excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'audio',
                    'attributes' => [
                        'controls' => 'controls',
                        'src' => 'posts/' . $matches[1],
                    ],
                    // Adding this tells Parsedown the element is self-closing 
                    // and to keep processing the rest of the content
                    'text' => '', 
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
    $parts = explode('---', $markdown, 3);
    if (count($parts) >= 3) {
        $markdown = trim($parts[2]);
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