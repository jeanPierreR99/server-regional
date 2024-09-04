<?php

class GalleryModel
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

    public function addGallery($title, $date, $createdAt)
    {
        $statement = $this->connection->prepare("INSERT INTO gallery (title, date_published, create_at) VALUES (?, ?, ?)");
        $statement->execute([$title, $date, $createdAt]);
        return $this->connection->lastInsertId();
    }

    public function addFile($filePath, $extension, $galleryId)
    {
        $statement = $this->connection->prepare("INSERT INTO file_gallery (url, gallery_id, type) VALUES (?, ?, ?)");
        $statement->execute([$filePath, $galleryId, $extension]);
    }

    public function getGalleryById($galleryId)
    {
        $statement = $this->connection->prepare("SELECT * FROM gallery WHERE id = ?");
        $statement->execute([$galleryId]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function getFilesByGalleryId($galleryId)
    {
        $statement = $this->connection->prepare("SELECT * FROM file_gallery WHERE gallery_id = ?");
        $statement->execute([$galleryId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGallery()
    {
        $statement = $this->connection->prepare("SELECT gallery.*, file_gallery.id AS file_id, file_gallery.url AS file_url, file_gallery.type AS file_type FROM gallery LEFT JOIN file_gallery ON gallery.id = file_gallery.gallery_id order by create_at desc");
        $statement->execute();
        $gallerys = $statement->fetchAll(PDO::FETCH_ASSOC);

        $groupedGallery = [];

        foreach ($gallerys as $gallery) {
            $galleryId = $gallery['id'];

            if (!isset($groupedGallery[$galleryId])) {
                $groupedGallery[$galleryId] = [
                    'id' => $gallery['id'],
                    'title' => $gallery['title'],
                    'date_published' => $gallery['date_published'],
                    'create_at' => $gallery['create_at'],
                    'files' => []
                ];
            }

            if ($gallery['file_id'] !== null) {
                $groupedGallery[$galleryId]['files'][] = [
                    'id' => $gallery['file_id'],
                    'url' => $gallery['file_url'],
                    'type' => $gallery['file_type']
                ];
            }
        }
        return $groupedGallery;
    }

    public function deleteGallery($galleryId)
    {
        if ($galleryId == "") {
            return $this->responseError;
            return;
        }

        $statement = $this->connection->prepare("SELECT url FROM file_gallery WHERE gallery_id = ?");
        $statement->execute([$galleryId]);
        $filePaths = $statement->fetchAll(PDO::FETCH_COLUMN);

        $statement = $this->connection->prepare("DELETE FROM file_gallery WHERE gallery_id = ?");
        $statement->execute([$galleryId]);

        $statement = $this->connection->prepare("DELETE FROM gallery WHERE id = ?");
        $statement->execute([$galleryId]);

        // Eliminar los archivos físicos
        foreach ($filePaths as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        return $this->responseSuccess;
    }
}
