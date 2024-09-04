<?php

class DatabaseService
{
    private $host = 'localhost';
    private $dbname = 'drvcs';
    private $username = 'root';
    private $password = 'jeanpierre';
    private $conn;

    public function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
    
        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            echo "Error al conectar a la base de datos: " . $e->getMessage();
        }
    }
    

    public function getConnection()
    {
        return $this->conn;
    }
}

?>
