<?php
namespace App\Core;

use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;
use PDO;

class Database{
    private static $instance = null;
    private $pdo;
    private $dsn;
    private $username;
    private $password;

    private function __construct(){
        $this->initializeConnection();
    }

    /**
     * Get the database instance (singleton pattern)
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize the database connection using AWS Secrets Manager
     */
    private function initializeConnection(): void
    {
        // Get secrets from AWS Secrets Manager
        $client = new SecretsManagerClient([
            'version' => 'latest',
            'region' => 'eu-west-1'
        ]);
        
        $result = $client->getSecretValue([
            'SecretId' => $_ENV["SECRET_NAME"],
        ]);
        
        $secretData = json_decode($result['SecretString']);
        
        // Set connection properties
        $this->dsn = "mysql:host={$secretData->host};port={$secretData->port};dbname={$secretData->dbname};charset=utf8";
        $this->username = $secretData->username;
        $this->password = $secretData->password;
        
        // Create PDO connection
        $this->connect();
    }
    
    /**
     * Connect to the database
     */
    private function connect(): void
    {
        $this->pdo = new PDO(
            $this->dsn,
            $this->username,
            $this->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }
    
    /**
     * Get the PDO instance
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
    
    /**
     * Execute a query and return the statement
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Fetch all results from a query
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }
    
    /**
     * Fetch a single row from a query
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result !== false ? $result : null;
    }
    
    /**
     * Insert data into a table
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $this->query($sql, array_values($data));
        return (int) $this->pdo->lastInsertId();
    }
}