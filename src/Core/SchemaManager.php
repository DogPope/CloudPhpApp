<?php
namespace App\Core;

use PDO;
use PDOException;

class SchemaManager{
    private $db;
    
    public function __construct(Database $database){
        $this->db = $database;
    }
    /**
     * Initialize the database schema
     */
    public function initializeSchema(): void
    {
        $this->createCustomersTable();
        $this->createGamesTable();
        $this->createOrdersTable();
        // Add more table creation methods as needed
    }
    /**
     * Check if a table exists in the database
     */
    public function tableExists(string $tableName): bool
    {
        $pdo = $this->db->getPdo();
        $result = $pdo->query("SHOW TABLES LIKE '$tableName'");
        return $result->rowCount() > 0;
    }
    /**
     * Create the customers table if it doesn't exist
     */
    private function createCustomersTable(): void
    {
        if (!$this->tableExists('customers')) {
            $sql = "CREATE TABLE customers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(50) NOT NULL,
                last_name VARCHAR(50) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                phone VARCHAR(20),
                address TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            $this->db->getPdo()->exec($sql);
        }
    }
    /**
     * Create the games table if it doesn't exist
     */
    private function createGamesTable(): void
    {
        if (!$this->tableExists('games')) {
            $sql = "CREATE TABLE games (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(100) NOT NULL,
                developer VARCHAR(100) NOT NULL,
                genre VARCHAR(50),
                saleprice DECIMAL(10, 2) NOT NULL,
                quantity INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            $this->db->getPdo()->exec($sql);
        }
    }
    /**
     * Create the orders table if it doesn't exist
     */
    private function createOrdersTable(): void
    {
        if (!$this->tableExists('orders')) {
            $sql = "CREATE TABLE orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                customer_id INT NOT NULL,
                order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                total_amount DECIMAL(10, 2) NOT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'pending',
                FOREIGN KEY (customer_id) REFERENCES customers(id)
            )";

            $this->db->getPdo()->exec($sql);

            if (!$this->tableExists('order_items')) {
                $sql = "CREATE TABLE order_items (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    order_id INT NOT NULL,
                    game_id INT NOT NULL,
                    quantity INT NOT NULL,
                    price DECIMAL(10, 2) NOT NULL,
                    FOREIGN KEY (order_id) REFERENCES orders(id),
                    FOREIGN KEY (game_id) REFERENCES games(id)
                )";
                
                $this->db->getPdo()->exec($sql);
            }
        }
    }
}