
<?php

require_once("Services/NoticeService.php");

class NoticeController
{

    private $noticeService;

    public function __construct()
    {
        $this->noticeService = new NoticeService();
    }
    public function deleteNoticeAndFile($id)
    {
        return $this->noticeService->deleteNotice($id);
    }

    public function getNoticeAndFile()
    {
        return $this->noticeService->getNotice();
    }

    public function addNoticeAndFile()
    {

        return $this->noticeService->addNoticeAndFile();
    }
}
