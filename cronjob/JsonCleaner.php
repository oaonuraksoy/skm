<?php

require_once 'Database.php';

class JsonCleaner {
    private $baseUrl = 'https://sereflikuranmeali.com/meal/';
    private $temporaryFiles = [];
    private $db;

    // ANSI renk kodları
    private const COLORS = [
        'reset' => "\033[0m",
        'bold' => "\033[1m",
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'magenta' => "\033[35m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
        'bg_red' => "\033[41m",
        'bg_green' => "\033[42m",
        'bg_yellow' => "\033[43m",
        'bg_blue' => "\033[44m"
    ];

    public function __construct() {
        // Register shutdown function to clean up temporary files
        register_shutdown_function([$this, 'cleanup']);
        $this->db = Database::getInstance();
        // Add CSS styles
        echo '<style>
            .console-output {
                font-family: "Consolas", "Monaco", monospace;
                background: #1e1e1e;
                color: #d4d4d4;
                padding: 20px;
                border-radius: 5px;
                margin: 20px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            }
            .header {
                background: #2d2d2d;
                color: #fff;
                padding: 10px 15px;
                margin: 10px 0;
                border-radius: 3px;
                font-weight: bold;
            }
            .success { color: #6a9955; }
            .info { color: #569cd6; }
            .warning { color: #dcdcaa; }
            .error { color: #f14c4c; }
            .stats {
                background: #2d2d2d;
                padding: 15px;
                margin: 10px 0;
                border-radius: 3px;
            }
            .stats-row {
                display: flex;
                justify-content: space-between;
                margin: 5px 0;
            }
            .stats-label {
                color: #9cdcfe;
            }
            .stats-value {
                color: #b5cea8;
                font-weight: bold;
            }
            .divider {
                border-top: 1px solid #3d3d3d;
                margin: 10px 0;
            }
        </style>';
        echo '<div class="console-output">';
    }

    public function __destruct() {
        echo '</div>';
        $this->cleanup();
    }

    private function cleanup() {
        foreach ($this->temporaryFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    private function downloadJson($filename) {
        $url = $this->baseUrl . $filename;
        $tempFile = tempnam(sys_get_temp_dir(), 'json_');
        $this->temporaryFiles[] = $tempFile;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $content = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Download failed: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        file_put_contents($tempFile, $content);
        return $tempFile;
    }

    private function fixTurkishChars($text) {
        $charMap = [
            'ÅŸ' => 'ş', 'ÄŸ' => 'ğ', 'Ä±' => 'ı', 'Ã¶' => 'ö', 'Ã¼' => 'ü', 'Ã§' => 'ç',
            'Å' => 'Ş', 'Ä' => 'Ğ', 'Ä°' => 'İ', 'Ã' => 'Ö', 'Ã' => 'Ü', 'Ã' => 'Ç'
        ];
        return str_replace(array_keys($charMap), array_values($charMap), $text);
    }

    private function cleanHtmlWithSup($rawHtml) {
        // Return early if input is empty
        if (empty($rawHtml)) {
            return [
                'text' => '',
                'supNumbers' => []
            ];
        }

        // Decode HTML entities
        $rawHtml = html_entity_decode($rawHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Create DOM document
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        
        // Add a root element to ensure valid HTML
        $wrappedHtml = '<div>' . $rawHtml . '</div>';
        $dom->loadHTML(mb_convert_encoding($wrappedHtml, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $supNumbers = [];
        $xpath = new DOMXPath($dom);
        
        // Find all sup elements
        $supElements = $xpath->query('//sup');
        foreach ($supElements as $sup) {
            $supText = trim($sup->textContent);
            if (is_numeric($supText)) {
                $supNumber = (int)$supText;
                $replacement = $dom->createTextNode('[' . $supNumber . ']');
                $sup->parentNode->replaceChild($replacement, $sup);
                $supNumbers[] = $supNumber;
            } else {
                $replacement = $dom->createTextNode('[' . $supText . ']');
                $sup->parentNode->replaceChild($replacement, $sup);
            }
        }
        
        // Get clean text from the div we added
        $div = $xpath->query('//div')->item(0);
        $cleanText = $div ? trim(preg_replace('/\s+/', ' ', $div->textContent)) : '';
        
        return [
            'text' => $this->fixTurkishChars($cleanText),
            'supNumbers' => $supNumbers
        ];
    }

    private function htmlTableToMarkdown($htmlContent) {
        // Return early if input is empty
        if (empty($htmlContent)) {
            return '';
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        
        // Add a root element to ensure valid HTML
        $wrappedHtml = '<div>' . $htmlContent . '</div>';
        $dom->loadHTML(mb_convert_encoding($wrappedHtml, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);
        $table = $xpath->query('//table')->item(0);
        
        if (!$table) {
            return $htmlContent;
        }
        
        $rows = $xpath->query('.//tr', $table);
        if ($rows->length === 0) {
            return $htmlContent;
        }
        
        $markdown = '';
        
        // Process headers
        $headers = $xpath->query('.//td', $rows->item(0));
        $headerTexts = [];
        foreach ($headers as $header) {
            $headerTexts[] = trim($header->textContent);
        }
        
        $markdown .= '| ' . implode(' | ', $headerTexts) . " |\n";
        $markdown .= '| ' . implode(' | ', array_fill(0, count($headerTexts), '---')) . " |\n";
        
        // Process data rows
        for ($i = 1; $i < $rows->length; $i++) {
            $cells = $xpath->query('.//td', $rows->item($i));
            $cellTexts = [];
            foreach ($cells as $cell) {
                $cellTexts[] = trim($cell->textContent);
            }
            $markdown .= '| ' . implode(' | ', $cellTexts) . " |\n";
        }
        
        return $markdown;
    }

    private function processData($data, $tableName) {
        $cleanedData = [];
        
        if ($tableName === 'meal') {
            $intFields = ['id', 'kuran_ayet_no', 'sure_no', 'ayet_no'];
            $htmlFields = ['ayet_arapca', 'ayet_ie', 'ayet_ahmed_samira', 'ayet_not', 'not_1', 'not_2', 'not_3'];
            $markdownField = 'ayet_latin';
        } elseif ($tableName === 'kavramlar_sifatlar') {
            $intFields = ['kavram_id', 'kavram_no'];
            $htmlFields = ['kavram_adi', 'kavram_text', 'kavram_detay', 'not_1'];
            $markdownField = null;
        } else {
            throw new Exception("Unknown table name: $tableName");
        }

        foreach ($data as $item) {
            $cleanedItem = $item;
            
            // Convert integer fields
            foreach ($intFields as $field) {
                if (isset($cleanedItem[$field]) && $cleanedItem[$field] !== '') {
                    if (is_numeric($cleanedItem[$field])) {
                        $cleanedItem[$field] = (int)$cleanedItem[$field];
                    }
                }
            }
            
            // Process HTML fields
            $supNumbers = [];
            foreach ($htmlFields as $field) {
                if (isset($cleanedItem[$field]) && is_string($cleanedItem[$field])) {
                    $result = $this->cleanHtmlWithSup($cleanedItem[$field]);
                    $cleanedItem[$field] = $result['text'];
                    $supNumbers = array_merge($supNumbers, $result['supNumbers']);
                }
            }
            
            // Process markdown field if exists
            if ($markdownField && isset($cleanedItem[$markdownField]) && is_string($cleanedItem[$markdownField])) {
                $cleanedItem[$markdownField] = $this->htmlTableToMarkdown($cleanedItem[$markdownField]);
            }
            
            $cleanedItem['sup_numbers'] = $supNumbers;
            $cleanedData[] = $cleanedItem;
        }
        
        return $cleanedData;
    }

    private function printHeader($message) {
        echo '<div class="header">' . htmlspecialchars($message) . '</div>';
    }

    private function printSuccess($message) {
        echo '<div class="success">✓ ' . htmlspecialchars($message) . '</div>';
    }

    private function printInfo($message) {
        echo '<div class="info">ℹ ' . htmlspecialchars($message) . '</div>';
    }

    private function printWarning($message) {
        echo '<div class="warning">⚠ ' . htmlspecialchars($message) . '</div>';
    }

    private function printError($message) {
        echo '<div class="error">✗ ' . htmlspecialchars($message) . '</div>';
    }

    private function printStats($inserted, $skipped) {
        echo '<div class="stats">';
        echo '<div class="header">İşlem İstatistikleri</div>';
        echo '<div class="divider"></div>';
        echo '<div class="stats-row">';
        echo '<span class="stats-label">Yeni Eklenen:</span>';
        echo '<span class="stats-value">' . $inserted . '</span>';
        echo '</div>';
        echo '<div class="stats-row">';
        echo '<span class="stats-label">Atlanan:</span>';
        echo '<span class="stats-value">' . $skipped . '</span>';
        echo '</div>';
        echo '</div>';
    }

    private function checkVersion($type) {
        try {
            $this->printHeader("Versiyon Kontrolü");
            
            // Download version info
            $versionFile = $this->downloadJson('versionInfo.json');
            $versionContent = file_get_contents($versionFile);
            $versionData = json_decode($versionContent, true);
            
            if (!isset($versionData['mealVersion'])) {
                throw new Exception("Version bilgisi bulunamadı");
            }

            $remoteVersion = $versionData['mealVersion'];
            $currentVersion = $this->db->getCurrentVersion($type);

            // If no version in database or remote version is newer
            if (!$currentVersion || $remoteVersion > $currentVersion) {
                $this->printInfo("Yeni versiyon bulundu: " . $remoteVersion . " (Mevcut: " . $currentVersion . ")");
                return true;
            }

            $this->printInfo("Güncel versiyon kullanılıyor: " . $currentVersion);
            return false;

        } catch (Exception $e) {
            throw new Exception("Versiyon kontrolü başarısız: " . $e->getMessage());
        }
    }

    public function cleanJson($tableName) {
        try {
            $this->printHeader("İşlem Başlatılıyor: " . strtoupper($tableName));

            // Check version first
            if (!$this->checkVersion($tableName === 'meal' ? 'meal' : 'kavram')) {
                $this->printWarning("Güncelleme gerekmiyor.");
                return null;
            }

            $this->printInfo("JSON dosyası indiriliyor...");
            $jsonFile = $this->downloadJson($tableName . '.json');
            
            $this->printInfo("JSON verisi işleniyor...");
            $jsonContent = file_get_contents($jsonFile);
            $jsonData = json_decode($jsonContent, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON decode error: ' . json_last_error_msg());
            }
            
            // Find the specified table
            $tableData = null;
            foreach ($jsonData as $item) {
                if (isset($item['type']) && $item['type'] === 'table' && 
                    isset($item['name']) && $item['name'] === $tableName) {
                    $tableData = $item['data'] ?? null;
                    break;
                }
            }
            
            if (!$tableData) {
                throw new Exception("Table '$tableName' not found in JSON data");
            }
            
            $this->printInfo("Veriler temizleniyor...");
            $cleanedData = $this->processData($tableData, $tableName);
            
            $this->printInfo("Temizlenmiş veriler dosyaya kaydediliyor...");
            $outputFile = "cleaned_{$tableName}.json";
            file_put_contents(
                $outputFile,
                json_encode($cleanedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            $this->printInfo("Veritabanı güncelleniyor...");
            $this->saveToDatabase($cleanedData, $tableName);

            $this->printInfo("Versiyon bilgisi güncelleniyor...");
            $versionFile = $this->downloadJson('versionInfo.json');
            $versionContent = file_get_contents($versionFile);
            $versionData = json_decode($versionContent, true);
            $this->db->updateVersion($tableName === 'meal' ? 'meal' : 'kavram', $versionData['mealVersion']);
            
            $this->printSuccess("İşlem başarıyla tamamlandı!");
            $this->printSuccess("Çıktı dosyası: " . $outputFile);
            
            return $outputFile;
            
        } catch (Exception $e) {
            $this->printError("Hata: " . $e->getMessage());
            throw new Exception("Error processing JSON: " . $e->getMessage());
        }
    }

    private function saveToDatabase($data, $tableName) {
        $this->printHeader("Saving data to database...");
        
        $inserted = 0;
        $skipped = 0;
        
        // Get table columns
        $tableColumns = [];
        $result = $this->db->query("SHOW COLUMNS FROM $tableName");
        while ($row = $result->fetch()) {
            $tableColumns[] = $row['Field'];
        }
        
        foreach ($data as $item) {
            try {
                // Filter out columns that don't exist in the table
                $filteredItem = array_intersect_key($item, array_flip($tableColumns));
                
                // Convert sup_numbers to JSON if it exists
                if (isset($filteredItem['sup_numbers']) && is_array($filteredItem['sup_numbers'])) {
                    $filteredItem['sup_numbers'] = json_encode($filteredItem['sup_numbers'], JSON_UNESCAPED_UNICODE);
                }
                
                $columns = implode(', ', array_keys($filteredItem));
                $values = implode(', ', array_fill(0, count($filteredItem), '?'));
                
                $query = "INSERT INTO $tableName ($columns) VALUES ($values) 
                         ON DUPLICATE KEY UPDATE " . 
                         implode(', ', array_map(function($col) {
                             return "$col = VALUES($col)";
                         }, array_keys($filteredItem)));
                
                $stmt = $this->db->prepare($query);
                $stmt->execute(array_values($filteredItem));
                
                if ($stmt->rowCount() > 0) {
                    $inserted++;
                } else {
                    $skipped++;
                }
            } catch (Exception $e) {
                $this->printError("Error saving item: " . $e->getMessage());
                $skipped++;
            }
        }
        
        $this->printStats($inserted, $skipped);
    }

    public function updateCountStats() {
        $query = "UPDATE count_stats SET 
            total_kavram = (SELECT COUNT(*) FROM kavramlar_sifatlar),
            total_meal = (SELECT COUNT(*) FROM meal),
            last_updated = CURRENT_TIMESTAMP";
        
        try {
            $this->db->query($query);
            $this->printInfo("Count stats updated successfully");
        } catch (Exception $e) {
            $this->printError("Failed to update count stats: " . $e->getMessage());
        }
    }
}

// Example usage moved outside the class
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    try {
        $cleaner = new JsonCleaner();
        
        // Clean meal.json and save to database
        $mealOutput = $cleaner->cleanJson('meal');
        
        // Clean kavramlar_sifatlar.json and save to database
        $kavramOutput = $cleaner->cleanJson('kavramlar_sifatlar');
        
        // Update count stats after all updates are complete
        $cleaner->updateCountStats();
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} 