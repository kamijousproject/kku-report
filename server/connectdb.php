<?php
// $host = '';
// $db_name = '';
// $username = '';
// $password = '';
// $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
// $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

class Database
{
    private $host = "";
    private $dbname = "";
    private $username = "";
    private $password = "";
    private $conn;

    public function connect()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage();
        }

        return $this->conn;
    }
}

