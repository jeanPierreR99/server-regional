<?php

class NoticeModel
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

    public function addNotice($title, $content, $date, $createdAt)
    {
        $statement = $this->connection->prepare("INSERT INTO notice (title, content, date_published, create_at) VALUES (?, ?, ?, ?)");
        $statement->execute([$title, $content, $date, $createdAt]);
        return $this->connection->lastInsertId();
    }

    public function addFile($fileName, $filePath, $extension, $noticeId)
    {
        $statement = $this->connection->prepare("INSERT INTO file (name, url, type, notice_id) VALUES (?, ?, ?, ?)");
        $statement->execute([$fileName, $filePath, $extension, $noticeId]);
    }

    public function getNoticeById($noticeId)
    {
        $statement = $this->connection->prepare("SELECT * FROM notice WHERE id = ?");
        $statement->execute([$noticeId]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function getFilesByNoticeId($noticeId)
    {
        $statement = $this->connection->prepare("SELECT * FROM file WHERE notice_id = ?");
        $statement->execute([$noticeId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNotice()
    {
        $statement = $this->connection->prepare("SELECT notice.*, file.id AS file_id, file.name AS file_name, file.url AS file_url, file.type AS file_type FROM notice LEFT JOIN file ON notice.id = file.notice_id order by create_at desc");
        $statement->execute();
        $notices = $statement->fetchAll(PDO::FETCH_ASSOC);

        $groupedNotices = [];

        foreach ($notices as $notice) {
            $noticeId = $notice['id'];

            if (!isset($groupedNotices[$noticeId])) {
                $groupedNotices[$noticeId] = [
                    'id' => $notice['id'],
                    'title' => $notice['title'],
                    'content' => $notice['content'],
                    'date_published' => $notice['date_published'],
                    'create_at' => $notice['create_at'],
                    'files' => []
                ];
            }

            if ($notice['file_name'] !== null) {
                $groupedNotices[$noticeId]['files'][] = [
                    'id' => $notice['file_id'],
                    'name' => $notice['file_name'],
                    'url' => $notice['file_url'],
                    'type' => $notice['file_type']
                ];
            }
        }
        return $groupedNotices;
    }

    public function deleteNotice($noticeId)
    {
        if ($noticeId == "") {
            return $this->responseError;
            return;
        }

        $statement = $this->connection->prepare("SELECT url FROM file WHERE notice_id = ?");
        $statement->execute([$noticeId]);
        $filePaths = $statement->fetchAll(PDO::FETCH_COLUMN);

        $statement = $this->connection->prepare("DELETE FROM file WHERE notice_id = ?");
        $statement->execute([$noticeId]);

        $statement = $this->connection->prepare("DELETE FROM notice WHERE id = ?");
        $statement->execute([$noticeId]);

        // Eliminar los archivos físicos
        foreach ($filePaths as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        return $this->responseSuccess;
    }
}
