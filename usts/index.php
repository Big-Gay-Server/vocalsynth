<body>
    <header>
        <h1>LunarConstruct's USTs</h1>
        <p>A list of UTAU USTs made by LunarConstruct. Please credit when using!</p>
        <p><strike>also u can't blame me if some of the old ones are bad i am not responsible for 14 year old me's ust making capabilities</strike>
    </header>
    <div>
        <table id="ust-table">
            <thead>
                <tr>
                    <th>download</th>
                    <th>song name</th>
                    <th>original artist</th>
                    <th>language</th>
                    <th>format</th>
                    <th>date</th>
                </tr>
            </thead>
            <tbody>
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
                    usort($sortedData, function ($a, $b) {
                        return strcmp($b['date'] ?? '', $a['date'] ?? '');
                    });

                    foreach ($sortedData as $data) {
                        echo '<tr>';
                        echo '<td><a href="./downloads/' . ($data['folder'] ?? '') . '/' . ($data['dllink'] ?? '') . '"><button id="ustdl">download</button></a></td>';
                        echo '<td>' . htmlspecialchars($data['name'] ?? 'Unknown') . '</td>';
                        echo '<td>' . htmlspecialchars($data['artist'] ?? 'Unknown') . '</td>';
                        echo '<td>' . htmlspecialchars($data['language'] ?? 'N/A') . '</td>';
                        echo '<td>' . htmlspecialchars($data['format'] ?? 'N/A') . '</td>';
                        echo '<td>' . htmlspecialchars($data['date'] ?? 'N/A') . '</td>';
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</body>