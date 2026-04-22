<?php
setlocale(LC_ALL, 'en_US.UTF-8');
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/Spyc.php';
require_once __DIR__ . '/../vendor/autoload.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/covermanager.php';

class VoicebankManager {
    private string $baseDir = './'; // Directory containing char folders (merisdae, canele, etc)
    private string $webPath = '/voicebanks/';

    // fetch all characters
    public function getCharacters(): array {
        $charas = [];
        $folders = glob($this->baseDir . '*', GLOB_ONLYDIR);
        foreach ($folders as $dir) {
            $infoFile = $dir . '/info.yaml';
            if (file_exists($infoFile)) {
                $data = Spyc::YAMLLoad($infoFile);
                $data['slug'] = basename($dir);
                $charas[] = $data;
            }
        }
        return $charas;
    }


    // fetch all voicebanks for a specific character
    public function getBanksByCharacter(string $charaName): array {
        $banks = [];
        $folders = glob($this->baseDir . $charaName . '/*', GLOB_ONLYDIR);
        foreach ($folders as $path) {
            $info = Spyc::YAMLLoad($path . '/info.yaml');
            $info['id'] = basename($path);
            // Icon logic
            $icon = glob($path . '/icon.{png,jpg,jpeg}', GLOB_BRACE);
            $info['icon'] = !empty($icon) ? str_replace($this->baseDir, $this->webPath, $icon[0]) : '';
            $banks[] = $info;
        }
        usort($banks, fn($a, $b) => ($b['num'] ?? 0) <=> ($a['num'] ?? 0));
        return $banks;
    }

    // fetch a specific voicebank
    public function getSpecificBank(array $banks, string $vbId): ?array {
        foreach ($banks as $vb) {
            if ($vb['id'] === $vbId) return $vb;
        }
        return null;
    }
}

?>