<?php
// src/Database.php

class Database {
    private $host = "localhost";
    private $db_name = "webapi_demo";
    private $username = "root"; // เปลี่ยนตามการตั้งค่า XAMPP ของคุณ
    private $password = "";     // เปลี่ยนตามการตั้งค่า XAMPP ของคุณ
    public $conn;

    public function dbConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // ให้ fetch เป็น array แบบ associative
        } catch (PDOException $exception) {
            // ในงานจริงควร log ข้อผิดพลาดแทนที่จะแสดงผลโดยตรง
            die("Database connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>