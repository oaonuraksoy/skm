<?php

class Database {
    private $pdo;
    private static $instance = null;

    private function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=localhost;dbname=skm;charset=utf8mb4",
                "root",
                "",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            throw new Exception("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function getCurrentVersion($type) {
        $stmt = $this->pdo->query("SELECT meal_version, kavram_version FROM version_info ORDER BY id DESC LIMIT 1");
        $result = $stmt->fetch();
        if (!$result) {
            return null;
        }
        return $type === 'meal' ? $result['meal_version'] : $result['kavram_version'];
    }

    public function updateVersion($type, $version) {
        $stmt = $this->pdo->prepare("INSERT INTO version_info (meal_version, kavram_version) VALUES (?, ?)");
        $currentVersions = $this->getCurrentVersions();
        $mealVersion = $type === 'meal' ? $version : ($currentVersions['meal_version'] ?? null);
        $kavramVersion = $type === 'kavram' ? $version : ($currentVersions['kavram_version'] ?? null);
        return $stmt->execute([$mealVersion, $kavramVersion]);
    }

    private function getCurrentVersions() {
        $stmt = $this->pdo->query("SELECT meal_version, kavram_version FROM version_info ORDER BY id DESC LIMIT 1");
        return $stmt->fetch() ?: ['meal_version' => null, 'kavram_version' => null];
    }

    public function checkRecordExists($table, $id) {
        $idField = $table === 'kavramlar_sifatlar' ? 'kavram_id' : 'id';
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE {$idField} = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    public function insertMeal($data) {
        $sql = "INSERT INTO meal (
            id, kuran_ayet_no, sure_no, ayet_no, 
            ayet_arapca, ayet_ie, ayet_ahmed_samira, 
            ayet_latin, ayet_not, not_1, not_2, not_3, 
            sup_numbers
        ) VALUES (
            :id, :kuran_ayet_no, :sure_no, :ayet_no,
            :ayet_arapca, :ayet_ie, :ayet_ahmed_samira,
            :ayet_latin, :ayet_not, :not_1, :not_2, :not_3,
            :sup_numbers
        )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $data['id'],
            'kuran_ayet_no' => $data['kuran_ayet_no'],
            'sure_no' => $data['sure_no'],
            'ayet_no' => $data['ayet_no'],
            'ayet_arapca' => $data['ayet_arapca'],
            'ayet_ie' => $data['ayet_ie'],
            'ayet_ahmed_samira' => $data['ayet_ahmed_samira'],
            'ayet_latin' => $data['ayet_latin'],
            'ayet_not' => $data['ayet_not'],
            'not_1' => $data['not_1'],
            'not_2' => $data['not_2'],
            'not_3' => $data['not_3'],
            'sup_numbers' => json_encode($data['sup_numbers'])
        ]);
    }

    public function insertKavram($data) {
        $sql = "INSERT INTO kavramlar_sifatlar (
            kavram_id, kavram_no, kavram_adi, 
            kavram_text, kavram_detay, not_1, 
            sup_numbers
        ) VALUES (
            :kavram_id, :kavram_no, :kavram_adi,
            :kavram_text, :kavram_detay, :not_1,
            :sup_numbers
        )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'kavram_id' => $data['kavram_id'],
            'kavram_no' => $data['kavram_no'],
            'kavram_adi' => $data['kavram_adi'],
            'kavram_text' => $data['kavram_text'],
            'kavram_detay' => $data['kavram_detay'],
            'not_1' => $data['not_1'],
            'sup_numbers' => json_encode($data['sup_numbers'])
        ]);
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollback() {
        return $this->pdo->rollBack();
    }

    public function prepare($query) {
        return $this->pdo->prepare($query);
    }

    public function query($query) {
        try {
            $result = $this->pdo->query($query);
            return $result;
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage() . "\n";
            echo "Query: " . $query . "\n";
            throw $e;
        }
    }
} 