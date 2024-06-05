
<?php

require_once("Services/SystemService.php");

class SystemController
{

    private $systemService;

    public function __construct()
    {
        $this->systemService = new SystemService();
    }
    public function getDataSystem()
    {
        return $this->systemService->getDataSystem();
    }
}
