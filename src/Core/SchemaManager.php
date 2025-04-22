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
                `cust_id` int(5) NOT NULL AUTO_INCREMENT,
                `username` varchar(25) NOT NULL,
                `town` varchar(20) NOT NULL,
                `eircode` varchar(8) NOT NULL,
                `password` varchar(20) NOT NULL,
                `phone` char(10) NOT NULL,
                `email` varchar(50) NOT NULL,
                `cardnumber` char(16) NOT NULL,
                `status` char(1) NOT NULL DEFAULT 'R',
                `county` varchar(9) NOT NULL,
                PRIMARY KEY (`cust_id`),
                UNIQUE KEY `email` (`email`),
                KEY `county` (`county`)
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
                `game_id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(20) NOT NULL,
                `developer` varchar(20) NOT NULL,
                `genre` varchar(20) DEFAULT NULL,
                `saleprice` decimal(4,2) NOT NULL,
                `quantity` int(11) DEFAULT NULL,
                `status` char(1) NOT NULL DEFAULT 'R',
                PRIMARY KEY (`id`),
                CONSTRAINT `CONSTRAINT_1` CHECK (`quantity` > 0),
                CONSTRAINT `CONSTRAINT_2` CHECK (`saleprice` >= 0)
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
                `order_id` int(11) NOT NULL AUTO_INCREMENT,
                `order_date` date NOT NULL,
                `cost` decimal(6,2) NOT NULL,
                `status` char(1) NOT NULL DEFAULT 'P',
                `cust_id` int(11) DEFAULT NULL,
                PRIMARY KEY (`order_id`),
                KEY `cust_id` (`cust_id`),
                CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`cust_id`) REFERENCES `customers` (`cust_id`)
            )";

            $this->db->getPdo()->exec($sql);

            if (!$this->tableExists('order_items')) {
                $sql = "CREATE TABLE order_items (
                    `order_id` int(11) NOT NULL,
                    `game_id` int(11) NOT NULL,
                    KEY `order_id` (`order_id`),
                    KEY `game_id` (`game_id`),
                    CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
                    CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`)
                )";
                $this->db->getPdo()->exec($sql);
            }
        }
    }
}