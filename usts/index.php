<div id="ust-container">
    <!-- Header Row -->
    <div class="ust-header">
        <div><h4>download</h4></div>
        <div><h4>song name</h4></div>
        <div><h4>original artist</h4></div>
        <div><h4>format/lang</h4></div>
        <div><h4>date</h4></div>
    </div>

    <?php 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/Spyc.php';
    
    $ustsDir = glob(__DIR__ . '/downloads/*/', GLOB_ONLYDIR);
    $sortedData = [];

    foreach ($ustsDir as $ust) {
        $infoFile = $ust . 'info.yml';
        if (file_exists($infoFile)) {
            $ustData = Spyc::YAMLLoad($infoFile);
            if (is_array($ustData) && isset($ustData['name']) && trim($ustData['name']) !== '') {
                $ustData['folder'] = basename($ust);
                $sortedData[] = $ustData;
            }
        }
    }

    if (!empty($sortedData)) {
        // Your existing sorting logic
        usort($sortedData, function ($a, $b) {
            return strcmp($b['date'] ?? '', $a['date'] ?? '');
        });

        // Loop through the data and echo DIVs instead of TRs
        foreach ($sortedData as $data) {
            $dlPath = './downloads/' . ($data['folder'] ?? '') . '/' . ($data['dllink'] ?? '');
            
            echo '<div class="ust-row" onclick="window.location=\'' . $dlPath . '\'">';
                echo '<div class="col-dl"><a href="' . $dlPath . '"><button class="ustdl-btn">download</button></a></div>';
                echo '<div class="col-name">' . htmlspecialchars($data['name'] ?? 'Unknown') . '</div>';
                echo '<div class="col-artist">' . htmlspecialchars($data['artist'] ?? 'Unknown') . '</div>';
                echo '<div class="col-format">' . htmlspecialchars($data['language'] ?? 'N/A') . '<br><small>' . htmlspecialchars($data['format'] ?? 'N/A') . '</small></div>';
                echo '<div class="col-date">' . htmlspecialchars($data['date'] ?? 'N/A') . '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No USTs found.</p>';
    }
    ?>
</div>