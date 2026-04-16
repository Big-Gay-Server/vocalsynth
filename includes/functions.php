<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;
use Cocur\Slugify\Slugify;
$slugify = new Slugify();

// pulls yaml frontmatter from obsidian .md files
function getFrontMatter($filePath) {
    $content = file_get_contents($filePath);
    $parts = explode('---', $content);
    if (count($parts) >= 3) {
        return Yaml::parse(trim($parts[1]));
    }
    return [];
}