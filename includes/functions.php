<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

// pulls yaml frontmatter from obsidian .md files
function getFrontMatter($filePath) {
    $content = file_get_contents($filePath);
    $parts = explode('---', $content);
    if (count($parts) >= 3) {
        return Yaml::parse(trim($parts[1]));
    }
    return [];
}
function getPostData($filePath) {
    $content = file_get_contents($filePath);
    // Limit to 3 parts: [0] empty/before, [1] YAML, [2] Body
    $parts = explode('---', $content, 3);

    if (count($parts) === 3) {
        return [
            'meta' => Symfony\Component\Yaml\Yaml::parse(trim($parts[1])),
            'body' => trim($parts[2])
        ];
    }

    // Fallback if no frontmatter exists
    return [
        'meta' => [],
        'body' => $content
    ];
}