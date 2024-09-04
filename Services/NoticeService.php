<?php

require_once("Models/NoticeModel.php");
require_once("DatabaseService.php");

class NoticeService
{

    private $noticeModel;

    public function __construct()
    {
        $databaseService = new DatabaseService();
        $this->noticeModel = new NoticeModel($databaseService);
    }

    public function getNotice()
    {
        $notice = $this->noticeModel->getNotice();
        $this->noticeModel->responseSuccess["response"]["data"] = array_values($notice);
        echo json_encode($this->noticeModel->responseSuccess);
    }

    public function addNoticeAndFile()
    {
        if (!isset($_FILES['files'])) {
            echo json_encode($this->noticeModel->responseError);
            return;
        }

        $title = $_POST['title'];
        $content = $_POST['content'];
        $create_at = $_POST['create_at'];
        $date_published = $_POST['date_published'];

        $uploadsDirectory = './uploads/';

        $noticeId = $this->noticeModel->addNotice($title, $content, $create_at, $date_published);


        foreach ($_FILES['files']['tmp_name'] as $index => $tmpFilePath) {
            $fileName = $_FILES['files']['name'][$index];
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uniqueFileName = uniqid('', true) . '.' . $extension;
            $filePath = $uploadsDirectory . $uniqueFileName;

            if (move_uploaded_file($tmpFilePath, $filePath)) {
                $this->noticeModel->addFile($fileName, $filePath, $extension, $noticeId);
            } else {
                return;
            }
        }

        $notice = $this->noticeModel->getNoticeById($noticeId);
        $files = $this->noticeModel->getFilesByNoticeId($noticeId);

        $notice['files'] = $files;

        $this->noticeModel->responseSuccess["response"]["data"] = $notice;

        echo json_encode($this->noticeModel->responseSuccess);
    }

    public function deleteNotice($noticeId)
    {
        $response = $this->noticeModel->deleteNotice($noticeId);
        echo json_encode($response);
    }
}
