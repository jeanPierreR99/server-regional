<?php

require_once("Models/AdminModel.php");
require_once("DatabaseService.php");

class AdminService
{

    private $adminModel;

    public function __construct()
    {
        $databaseService = new DatabaseService();
        $this->adminModel = new AdminModel($databaseService);
    }

    public function getAdmin()
    {
        $admin = $this->adminModel->getAdmin();

        $this->adminModel->responseSuccess["response"]["data"] = $admin;
        echo json_encode($this->adminModel->responseSuccess);
    }

    public function verifyAdmin()
    {
        if (!isset($_POST["user"]) || !isset($_POST["password"])) {
            echo json_encode($this->adminModel->responseError);
            return;
        }
        $user = $_POST["user"];
        $password = $_POST["password"];

        $admin = $this->adminModel->verifyAdmin($user, $password);
        $this->adminModel->responseSuccess["response"]["data"] = $admin["user"];
        echo json_encode($this->adminModel->responseSuccess);
    }
}
