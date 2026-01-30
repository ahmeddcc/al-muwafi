<?php
/**
 * خدمة قاعدة البيانات
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Services;

use PDO;
use PDOException;
use Exception;

class Database {
    private static ?Database $instance = null;
    private ?PDO $pdo = null;
    private array $config;
    
    /**
     * البناء - خاص لمنع الإنشاء المباشر
     */
    private function __construct() {
        $this->config = [
            'host' => defined('DB_HOST') ? DB_HOST : 'localhost',
            'name' => defined('DB_NAME') ? DB_NAME : 'al_muwafi_db',
            'user' => defined('DB_USER') ? DB_USER : 'root',
            'pass' => defined('DB_PASS') ? DB_PASS : '',
            'charset' => defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4',
        ];
        
        $this->connect();
    }
    
    /**
     * الحصول على النسخة الوحيدة (Singleton)
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * الاتصال بقاعدة البيانات
     */
    private function connect(): void {
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $this->config['host'],
                $this->config['name'],
                $this->config['charset']
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->pdo = new PDO($dsn, $this->config['user'], $this->config['pass'], $options);
            
        } catch (PDOException $e) {
            ErrorLogger::logError('database_connection', $e->getMessage(), __FILE__, __LINE__, 'critical');
            throw new Exception('فشل الاتصال بقاعدة البيانات');
        }
    }
    
    /**
     * الحصول على اتصال PDO
     */
    public function getConnection(): PDO {
        return $this->pdo;
    }
    
    /**
     * تنفيذ استعلام مع معاملات
     */
    public function query(string $sql, array $params = []): \PDOStatement {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            ErrorLogger::logError('database_query', $e->getMessage(), __FILE__, __LINE__, 'high');
            throw new Exception('خطأ في تنفيذ الاستعلام');
        }
    }
    
    /**
     * جلب صف واحد
     */
    public function fetchOne(string $sql, array $params = []): ?array {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }
    
    /**
     * جلب جميع الصفوف
     */
    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }
    
    /**
     * جلب قيمة واحدة
     */
    public function fetchColumn(string $sql, array $params = [], int $column = 0): mixed {
        return $this->query($sql, $params)->fetchColumn($column);
    }
    
    /**
     * إدراج صف جديد
     */
    public function insert(string $table, array $data): int {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);
        
        $sql = sprintf(
            "INSERT INTO `%s` (`%s`) VALUES (%s)",
            $table,
            implode('`, `', $columns),
            implode(', ', $placeholders)
        );
        
        $this->query($sql, $data);
        return (int) $this->pdo->lastInsertId();
    }
    
    /**
     * تحديث صفوف
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int {
        $setClause = implode(', ', array_map(fn($col) => "`$col` = :$col", array_keys($data)));
        
        $sql = sprintf("UPDATE `%s` SET %s WHERE %s", $table, $setClause, $where);
        
        $stmt = $this->query($sql, array_merge($data, $whereParams));
        return $stmt->rowCount();
    }
    
    /**
     * حذف صفوف
     */
    public function delete(string $table, string $where, array $params = []): int {
        $sql = sprintf("DELETE FROM `%s` WHERE %s", $table, $where);
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * بدء معاملة
     */
    public function beginTransaction(): bool {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * تأكيد المعاملة
     */
    public function commit(): bool {
        return $this->pdo->commit();
    }
    
    /**
     * التراجع عن المعاملة
     */
    public function rollback(): bool {
        return $this->pdo->rollBack();
    }
    
    /**
     * التحقق من وجود جدول
     */
    public function tableExists(string $table): bool {
        $sql = "SHOW TABLES LIKE :table";
        $result = $this->fetchOne($sql, ['table' => $table]);
        return $result !== null;
    }
    
    /**
     * عد الصفوف
     */
    public function count(string $table, string $where = '1=1', array $params = []): int {
        $sql = sprintf("SELECT COUNT(*) FROM `%s` WHERE %s", $table, $where);
        return (int) $this->fetchColumn($sql, $params);
    }
    
    /**
     * التحقق من وجود صف
     */
    public function exists(string $table, string $where, array $params = []): bool {
        return $this->count($table, $where, $params) > 0;
    }
    
    /**
     * منع الاستنساخ
     */
    private function __clone() {}
    
    /**
     * منع إلغاء التسلسل
     */
    public function __wakeup() {
        throw new Exception("لا يمكن إلغاء تسلسل Singleton");
    }
}
