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
                    $clean_name = $this->clean($name);
                    $this->vb_map[$clean_name] = $url_path;
                }
            }
        }
    }

    // this part gets all the cover data from the yaml files
    public function getCovers(array $filters = []): array {
        $all_covers = [];
        $folders = glob($_SERVER['DOCUMENT_ROOT'] . '/covers/mycovers/*/', GLOB_ONLYDIR);
        
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

                // filter by title (checks both original and romanized names)
                if (isset($filters['title'])) {
                    $searchTitle = mb_strtolower(trim($filters['title']), 'UTF-8');
                    $orig = mb_strtolower($cover['orig name'] ?? '', 'UTF-8');
                    $rom = mb_strtolower($cover['en/rom name'] ?? '', 'UTF-8');
                    
                    $titleMatch = (str_contains($orig, $searchTitle) || str_contains($rom, $searchTitle));
                    $match = $match && $titleMatch;
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
            // Clean the name from the cover (e.g. "Canelé" -> "canele")
            $clean_vb_name = $this->clean($vb_name);
            
            // Look it up in your map
            $path = $map[$clean_vb_name] ?? '';
            
            // Clean the folder name we are searching for
            $clean_search = $this->clean($charaFolder);

            if ($path !== '' && str_contains(strtolower($path), $clean_search)) {
                return true;
            }
        }
        return false;
    }

    public function processCoverData(array $coverdata): array {
        // 1. Date Logic
        $date = isset($coverdata['date']) ? date('F j, Y', strtotime($coverdata['date'])) : '';
        $year = $coverdata['Year'] ?? '';

        // 2. Title Logic
        $origname = $coverdata['orig name'] ?? '';
        $romname = $coverdata['en/rom name'] ?? '';
        if (!empty($origname) && !empty($romname)) {
            $song = ($origname == $romname) ? $origname : "$origname / $romname";
        } else {
            $song = $origname ?: $romname ?: 'Unknown';
        }
        $isMulticover = $coverdata['is_multicover'] ?? false;
        $full_title = $isMulticover ? "$song ($year ver)" : $song;

        // 3. Voicebank Logic
        $vb_data = $coverdata['voicebank'] ?? '';
        $vb_array = is_array($vb_data) ? $vb_data : [$vb_data];
        $vb_links = [];
        foreach ($vb_array as $vb_item) {
            $clean_key = $this->clean($vb_item); 
            $link = $this->vb_map[$clean_key] ?? '';
            $vb_links[] = $link ? '<a href="' . $link . '">' . htmlspecialchars($vb_item) . '</a>' : htmlspecialchars($vb_item);
        }

        // 4. Byline Logic
        $artist = is_array($coverdata['music & lyrics'] ?? '') ? implode(', ', $coverdata['music & lyrics']) : ($coverdata['music & lyrics'] ?? '');
        $ogvo = is_array($coverdata['original vocals'] ?? '') ? implode(', ', $coverdata['original vocals']) : ($coverdata['original vocals'] ?? '');
        $byline = ($artist == $ogvo) ? $artist : "$artist ft. $ogvo";

        // 5. Video Logic
        $raw_video = $coverdata['video link'] ?? '';
        $video_link = is_array($raw_video) ? ($raw_video[0] ?? '') : $raw_video;
        $embed_url = preg_replace("/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\s&]+)/", "https://youtube.com/embed/$1", $video_link);

        // ust
        $ust = is_array($coverdata['UST'] ?? '') ? implode(', ', $coverdata['UST']) : ($coverdata['UST'] ?? '');
        $tuning = is_array($coverdata['tuning'] ?? '') ? implode(', ', $coverdata['tuning']) : ($coverdata['tuning'] ?? '');
        $mix = is_array($coverdata['mix'] ?? '') ? implode(', ', $coverdata['mix']) : ($coverdata['mix'] ?? '');
        $movie = is_array($coverdata['movie'] ?? '') ? implode(', ', $coverdata['movie']) : ($coverdata['movie'] ?? '');
        $illust = is_array($coverdata['illust'] ?? '') ? implode(', ', $coverdata['illust']) : ($coverdata['illust'] ?? '');

        // Return a nice, clean object
        return [
            'title' => $full_title,
            'url_slug' => urlencode($romname ?: $origname),
            'date' => $date,
            'vb_display' => implode(', ', $vb_links),
            'voicebank' => implode(', ', $vb_array),
            'artist' => $artist,
            'ogvo' => $ogvo,
            'byline' => $byline,
            'ust' => $ust,
            'tuning' => $tuning,
            'mix' => $mix,
            'movie' => $movie,
            'illust' => $illust,
            'embed_url' => $embed_url,
            'file_path' => $coverdata['file_path'] ?? '',
            'raw' => $coverdata // keep the original just in case
        ];
    }

    // this part displays the html.
    public function renderGrid(array $covers): void {
        foreach ($covers as $raw_data) {
            $cover = $this->processCoverData($raw_data);
            ?>
            <div class="coverbox">
                <?php if ($cover['embed_url']): ?>
                    <div class="video-container">
                        <iframe src="<?= $cover['embed_url'] ?>" frameborder="0" allowfullscreen loading="lazy"></iframe>
                    </div>
                <?php endif; ?>
                <a href="/covers?song=<?= $cover['url_slug'] ?>"><h3><?= htmlspecialchars($cover['title']) ?></h3></a>
                <h4><?= $cover['vb_display'] ?></h4>
                <h5><?= htmlspecialchars($cover['byline']) ?></h5>
                <h6><?= htmlspecialchars($cover['date']) ?></h6>
            </div>
            <?php
        }
    }

    private function clean($str) {
        // 1. Convert to Lowercase
        $str = mb_strtolower(trim((string)$str), 'UTF-8');
        // 2. Remove Accents (é -> e)
        return iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
    }
}