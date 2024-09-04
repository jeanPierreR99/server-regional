
<?php

require_once("Services/GalleryService.php");

class GalleryController
{

    private $galleryService;

    public function __construct()
    {
        $this->galleryService = new GalleryService();
    }
    public function deleteGalleryAndFile($id)
    {
        return $this->galleryService->deleteGallery($id);
    }
    public function getGalleryAndFile()
    {
        return $this->galleryService->getGallery();
    }
    public function addGalleryAndFile()
    {
        return $this->galleryService->addGalleryAndFile();
    }
}
