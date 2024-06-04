<?php

class AdminModel
{
    private $connection;
    public $responseSuccess = [
        "response" => [
            "status" => 200,
            "message" => "success"
        ]
    ];

    public $responseError = [
        "response" => [
            "status" => 400,
            "message" => "error"
        ]
    ];

    public function __construct($databaseService)
    {
        $this->connection = $databaseService->getConnection();
    }

    public function verifyAdmin($user, $password)
    {
        $statement = $this->connection->prepare("SELECT * FROM admin WHERE user = ? and password = ?");
        $statement->execute([$user, $password]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function getAdmin()
    {
        $statement = $this->connection->prepare("SELECT *FROM admin");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
