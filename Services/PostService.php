<?php

require_once("Models/PostModel.php");
require_once("DatabaseService.php");

class PostService
{

    private $postModel;

    public function __construct()
    {
        $databaseService = new DatabaseService();
        $this->postModel = new PostModel($databaseService);
    }

    public function getPost()
    {
        $post = $this->postModel->getPost();
        $this->postModel->responseSuccess["response"]["data"] = array_values($post);
        echo json_encode($this->postModel->responseSuccess);
    }

    public function addPostAndFile()
    {
        if (!isset($_FILES['file'])) {
            echo json_encode($this->postModel->responseError);
            return;
        }

        $title = $_POST['title'];
        $content = $_POST['content'];
        $create_at = $_POST['create_at'];
        $date_published = $_POST['date_published'];

        $uploadsDirectory = './uploads/';

        $postId = $this->postModel->addPost($title, $content, $create_at, $date_published);


        if (isset($_FILES['file']['tmp_name'])) {
            $tmpFilePath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uniqueFileName = uniqid('', true) . '.' . $extension;
            $filePath = $uploadsDirectory . $uniqueFileName;

            if (move_uploaded_file($tmpFilePath, $filePath)) {
                $this->postModel->addFile($filePath, $postId);
            } else {
                return;
            }

            $post = $this->postModel->getFileByPostId($postId);

            $this->postModel->responseSuccess["response"]["data"] = $post;

            echo json_encode($this->postModel->responseSuccess);
        }
    }

    public function deletePost($postId)
    {
        $response = $this->postModel->deletePost($postId);
        echo json_encode($response);
    }

    public function updatePost()
    {
        $status = $_POST['status'];
        $id = $_POST['id'];

        $this->postModel->updatePost($status, $id);

        $post = $this->postModel->getPost();

        $this->postModel->responseSuccess["response"]["data"] = array_values($post);

        echo json_encode($this->postModel->responseSuccess);
    }
}
