<?php

class PostModel
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

    public function addPost($title, $content, $date, $createdAt)
    {
        $statement = $this->connection->prepare("INSERT INTO post (title, content, date_published, create_at) VALUES (?, ?, ?, ?)");
        $statement->execute([$title, $content, $date, $createdAt]);
        return $this->connection->lastInsertId();
    }

    public function addFile($filePath, $postId)
    {
        $statement = $this->connection->prepare("INSERT INTO file_post (url, post_id) VALUES (?, ?)");
        $statement->execute([$filePath, $postId]);
    }

    public function getFileByPostId($postId)
    {
        $statement = $this->connection->prepare("SELECT post.*, file_post.id AS file_id, file_post.url AS file_url FROM post LEFT JOIN file_post ON post.id = file_post.id WHERE file_post.post_id = ?");
        $statement->execute([$postId]);
        $posts = $statement->fetchAll(PDO::FETCH_ASSOC);

        $post = [
            'id' => $posts[0]['id'],
            'title' => $posts[0]['title'],
            'content' => $posts[0]['content'],
            'date_published' => $posts[0]['date_published'],
            'create_at' => $posts[0]['create_at'],
            'file' => ['id' => $posts[0]['file_id'], 'url' => $posts[0]['file_url']]
        ];

        return $post;
    }

    public function getPost()
    {
        $statement = $this->connection->prepare("SELECT post.*, file_post.id AS file_id, file_post.url AS file_url FROM post LEFT JOIN file_post ON post.id = file_post.post_id order by create_at desc");
        $statement->execute();
        $posts = $statement->fetchAll(PDO::FETCH_ASSOC);

        $groupPosts = [];

        foreach ($posts as $post) {
            $postId = $post['id'];

            if (!isset($groupPosts[$postId])) {
                $groupPosts[$postId] = [
                    'id' => $post['id'],
                    'title' => $post['title'],
                    'content' => $post['content'],
                    'date_published' => $post['date_published'],
                    'create_at' => $post['create_at'],
                    'file' => [
                        'id' => $post['file_id'],
                        'url' => $post['file_url']
                    ]
                ];
            }
        }
        return $groupPosts;
    }

    public function deletePost($postId)
    {
        if ($postId == "") {
            return $this->responseError;
            return;
        }

        $statement = $this->connection->prepare("SELECT url FROM file_post WHERE post_id = ?");
        $statement->execute([$postId]);
        $filePaths = $statement->fetchAll(PDO::FETCH_COLUMN);

        $statement = $this->connection->prepare("DELETE FROM file_post WHERE post_id = ?");
        $statement->execute([$postId]);

        $statement = $this->connection->prepare("DELETE FROM post WHERE id = ?");
        $statement->execute([$postId]);

        // Eliminar los archivos físicos
        foreach ($filePaths as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        return $this->responseSuccess;
    }
}
