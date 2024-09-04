<?php
class SystemService
{
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

    function formatSize($bytes)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        for ($i = 0; $bytes >= 1024 && $i < 4; $i++) $bytes /= 1024;
        return round($bytes, 2) . ' ' . $units[$i];
    }

    function getSizeUploads()
    {
        $dir = './uploads';
        $sizeFiles = 0;
        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                $sizeFiles += filesize($dir . '/' . $file);
            }
        }

        $sizeTotal = $this->formatSize($sizeFiles);

        return $sizeTotal;
    }

    function getMmemoryUsage()
    {

        $output = shell_exec('free -m');
        $lines = explode("\n", $output);
        $memory_line = explode(" ", preg_replace('/\s+/', ' ', trim($lines[1])));
        $memory_in_use =$memory_line[2]; // Total - Libre

        return $memory_in_use;
    }

    function getCpuUsage()
    {
        $cpu_usage = shell_exec("top -bn1 | grep 'Cpu(s)' | sed 's/.*, *\\([0-9.]*\\)%* id.*/\\1/' | awk '{print 100 - $1}'");
        return $cpu_usage;
    }

    function getDataSystem()
    {
        $sizeFile = $this->getSizeUploads();
        $memory = $this->getMmemoryUsage();
        $cpu = $this->getCpuUsage();

        $this->responseSuccess["response"]["data"]["size"] = $sizeFile;
        $this->responseSuccess["response"]["data"]["memory"] = $memory;
        $this->responseSuccess["response"]["data"]["cpu"] = $cpu;

        echo json_encode($this->responseSuccess);
    }
}
