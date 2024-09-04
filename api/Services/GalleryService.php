<?php

require_once("Models/GalleryModel.php");
require_once("DatabaseService.php");

class GalleryService
{

    private $galleryModel;

    public function __construct()
    {
        $databaseService = new DatabaseService();
        $this->galleryModel = new GalleryModel($databaseService);
    }

    public function getGallery()
    {
        $gallery = $this->galleryModel->getGallery();
        $this->galleryModel->responseSuccess["response"]["data"] = array_values($gallery);
        echo json_encode($this->galleryModel->responseSuccess);
    }

    public function addGalleryAndFile()
    {
        if (!isset($_FILES['files'])) {
            echo json_encode($this->galleryModel->responseError);
            return;
        }

        $title = $_POST['title'];
        $create_at = $_POST['create_at'];
        $date_published = $_POST['date_published'];

        $uploadsDirectory = '../uploads/gallery/';

        $galleryId = $this->galleryModel->addGallery($title, $create_at, $date_published);


        foreach ($_FILES['files']['tmp_name'] as $index => $tmpFilePath) {
            $fileName = $_FILES['files']['name'][$index];
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uniqueFileName = uniqid('', true) . '.' . $extension;
            $filePath = $uploadsDirectory . $uniqueFileName;

            if (move_uploaded_file($tmpFilePath, $filePath)) {
                $this->galleryModel->addFile($filePath, $extension, $galleryId);
            } else {
                return;
            }
        }

        $gallery = $this->galleryModel->getGalleryById($galleryId);
        $files = $this->galleryModel->getFilesByGalleryId($galleryId);

        $gallery['files'] = $files;

        $this->galleryModel->responseSuccess["response"]["data"] = $gallery;

        echo json_encode($this->galleryModel->responseSuccess);
    }

    public function deleteGallery($galleryId)
    {
        $response = $this->galleryModel->deleteGallery($galleryId);
        echo json_encode($response);
    }
}
