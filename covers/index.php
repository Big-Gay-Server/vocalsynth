<?php
echo "this page will be covers!! give me a sec lmfao";

require_once '../Spyc.php';

$entry = glob('*/*.md');

// load the base and extract frontmatter
function getFrontMatter($filePath) {
    $content = file_get_contents($filePath);
    $parts = explode('---', $content);
    if (count($parts) >= 3) {
        return Spyc::YAMLLoad(trim($parts[1]));
    }
    return [];
}

$baseyaml = __DIR__ . '/___covers.base';
$entry = glob('*/*.md');

// get columns from base
$coverBase = Spyc::YAMLLoad($baseyaml);
$views = $coverBase['views'][0] ?? null;
$columns = $views['order'] ?? [];

// table time
echo "<table id=covers><tr>";
foreach ($columns as $col) {
    echo "<th>$col</th>";
}
echo "</tr>";

usort($coverBase, function($a, $b) {
    $valA = $a['date'] ?? null;
    $valB = $b['date'] ?? null;

    if ($valA === null && $valB === null) return 0;
    if ($valA === null) return 1;
    if ($valB === null) return -1;

    return strtotime($valB) <=> strtotime($valA);
});

// iterate covers in rows
foreach ($entry as $cover) {
    $ver = basename($cover);
    $coverdata = getFrontmatter($cover);
    // if($coverdata['Year'] >= 2016) {
        echo "<tr>";
        // iterate cover info in columns
        foreach ($columns as $column) {
            echo "<td>";
            $columndata = $coverdata[$column] ?? "";
            if (is_array($columndata)) {
                echo implode(", ", $columndata);
            } else {
                echo $columndata;
            }
        }
    }
// }