<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

class CoverManager {
    // this is data the class will persist and keep in its memory,
    // so i dont have to define it every time i use it.
    private array $vb_map = [];
    private string $vb_base_path = '/usr/share/nginx/html/vocalsynth/voicebanks';

    // this is the "constructor". i dont fully understand this, but it runs immediately i guess?
    // i dont really get what this does, what is $this variable? i also dont know what the -> does or means
    public function __construct() {
        $this->loadVoicebankMap();
    }

    // this loads the voicebank map - aka i think an array with all covers belonging to each voicebank
    private function loadVoicebankMap(): void {
        $all_vbs = glob($this->vb_base_path . '/*/*/{info.yml,info.yaml}', GLOB_BRACE) ?: [];
        foreach ($all_vbs as $vb_file) {
            $info = Yaml::parseFile($vb_file);
            $raw_names = $info['vbname'] ?? '';
            if ($raw_names) {
                $names_array = explode(',', $raw_names);
                $url_path = str_replace('/usr/share/nginx/html/vocalsynth', '', dirname($vb_file));
                foreach ($names_array as $name) {
                    $this->vb_map[strtolower(trim($name))] = $url_path;
                }
            }
        }
    }

    // this part gets all the cover data from the yaml files
    public function getCovers(array $filters = []): array {
        $all_covers = [];
        $folders = glob($_SERVER['DOCUMENT_ROOT'] . '/covers/*/', GLOB_ONLYDIR);
        
        foreach ($folders as $song_folder) {
            $files = glob($song_folder . '*.md');
            $multicover = count($files) >= 2;
            foreach ($files as $file) {
                $data = getFrontMatter($file);
                if ($data) {
                    $data['file_path'] = $file;
                    $data['is_multicover'] = $multicover;
                    $all_covers[] = $data;
                }
            }
        }

        if (!empty($filters)) {
            $currentMap = $this->vb_map;
            $all_covers = array_filter($all_covers, function($cover) use ($filters, $currentMap) {
                $match = true;

                // filter by character
                if (isset($filters['charaFolder'])) {
                    $match = $match && $this->matchCharacter($cover, $filters['charaFolder'], $currentMap);
                }

                // filter by voicebank
                if (isset($filters['voicebank'])) {
                    $vbs_in_cover = (array)($cover['voicebank'] ?? []);
                    // Check if the specific name is in this cover's list
                    $match = $match && in_array($filters['voicebank'], $vbs_in_cover);
                }

                // filter by year
                if (isset($filters['year'])) {
                    $match = $match && ($cover['Year'] == $filters['year']);
                }

                // filter out anything before a specific year (defaults to 2016 for modernity's sake)
                $minYear = $filters['minYear'] ?? 2016; 
                $match = $match && (($cover['Year'] ?? 0) >= $minYear);


                // filter out covers without video links
                if (isset($filters['requireVideo']) && $filters['requireVideo'] === true) {
                    $match = $match && !empty($cover['video link'] ?? '');
                }

                // other filters i might wanna add
                // // usts, for example to filter out my usts for the ust dl page

                return $match;
            });
        }

        // sort the filtered results
        usort($all_covers, function($a, $b) {
            $dateA = isset($a['date']) ? strtotime($a['date']) : 0;
            $dateB = isset($b['date']) ? strtotime($b['date']) : 0;
            return $dateB <=> $dateA;
        });

        if (isset($filters['limit'])) {
            $all_covers = array_slice($all_covers, 0, (int)$filters['limit']);
        }

        return $all_covers; 
    }

    // logic for matching character
    private function matchCharacter(array $cover, string $charaFolder, array $map): bool {
        $vbs = (array)($cover['voicebank'] ?? []);
        foreach ($vbs as $vb_name) {
            $path = $map[strtolower(trim((string)$vb_name))] ?? '';
            if ($path !== '' && str_contains(strtolower($path), strtolower($charaFolder))) {
                return true;
            }
        }
        return false;
    }

    // this part displays the html.
    public function renderGrid(array $covers): void {
        foreach ($covers as $coverdata) {
            // link logic
            $coverpage = $coverdata['file_path'] ?? ''; // defines link to cover page
            
            // date logic
            $dateformat = 'F j, Y';
            $date = isset($coverdata['date']) ? date($dateformat, strtotime($coverdata['date'])) : '';
            $year = $coverdata['Year'] ?? '';

            // song title logic
            $origname = $coverdata['orig name'] ?? ''; // defines the original language name of song
            $romname = $coverdata['en/rom name'] ?? ''; // defines the english/romanized name of song
            $namessame = $origname == $romname ? true : false; // if they're the same, set $namessame to false for formatting
            if (!empty($origname) && !empty($romname)) { // defines the complete song name
                $song = $namessame ? $origname : "$origname / $romname";
            } else {
                $song = '';
            }
            // concatenates the title line, adding the year if there are multiple covers of the song.
            $isMulticover = $coverdata['is_multicover'] ?? false;
            $title = $isMulticover ? $song . " (" . $year . " ver)" : $song;

            // voicebank logic
            $vb_data = $coverdata['voicebank'] ?? ''; // grab vb data
            
            
            $vb_array = is_array($vb_data) ? $vb_data : [$vb_data]; // convert to array if it isn't one already
            $vb_links_html = [];

            foreach ($vb_array as $vb_item) {        
                $link = $this->vb_map[strtolower(trim($vb_item))] ?? ''; // look up the vb
                
                if ($link) {
                    // If we found a match, make it a link
                    $vb_links_html[] = '<a href="' . $link . '">' . htmlspecialchars($vb_item) . '</a>';
                } else {
                    // If no match, just show the plain text
                    $vb_links_html[] = htmlspecialchars($vb_item);
                }
            }
            $vb_display = implode(', ', $vb_links_html);

            // original song info logic
            $artist_data = $coverdata['music & lyrics'] ?? '';
            $artist = is_array($artist_data) ? implode(', ', $artist_data) : $artist_data;

            $ogvo_data = $coverdata['original vocals'] ?? '';
            $ogvo = is_array($ogvo_data) ? implode(', ', $ogvo_data) : $ogvo_data;

            $byline = ($artist == $ogvo) ? $artist : $artist . ' ft. ' . $ogvo;

            $lyric_data = $coverdata['lyric for desc'] ?? '';
            $lyric = is_array($lyric_data) ? implode(', ', $lyric_data): $lyric_data;
            $lyric_format = !empty($lyric) ? '"' . $lyric . '"' : '';
            
            // video logic
            $raw_video = $coverdata['video link'] ?? '';
            $video_link = is_array($raw_video) ? ($raw_video[0] ?? '') : $raw_video;
            $embed_url = preg_replace(
                "/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\s&]+)/",
                "https://youtube.com/embed/$1",
                $video_link
            );
            $video = !empty($video_link) ? '<div class="video-container"><iframe src="' . htmlspecialchars($embed_url) . '" frameborder="0" allowfullscreen loading="lazy"></iframe></div>' : '';

            // if cover meets below requirements, output a box with the info in it.
            if (!empty($raw_video) && $year >=2016) {
                ?>
                <div class="coverbox">
                    <?=$video ?> 
                    <a href="<?=$coverpage ?>"><h3><?= htmlspecialchars($title) ?></h3></a>
                    <h4><?= $vb_display ?></h4> <!-- Removed the outer <a> tag so each name has its own link -->
                    <h5><?= htmlspecialchars($byline) ?></h5>
                    <h6><?= htmlspecialchars($date) ?></h6>
                </div>
                <?php
            }
        }
    }
}