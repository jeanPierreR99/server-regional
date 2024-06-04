
<?php

require_once("Services/AdminService.php");

class AdminController
{

    private $adminService;

    public function __construct()
    {
        $this->adminService = new AdminService();
    }

    public function getAdmin()
    {
        return $this->adminService->getAdmin();
    }
    public function verifyAdmin()
    {
        return $this->adminService->verifyAdmin();
    }
}
