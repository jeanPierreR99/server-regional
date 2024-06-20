
<?php

require_once("Services/PostService.php");

class PostController
{

    private $postService;

    public function __construct()
    {
        $this->postService = new PostService();
    }
    public function deletePostAndFile($id)
    {
        return $this->postService->deletePost($id);
    }

    public function getPostAndFile()
    {
        return $this->postService->getPost();
    }

    public function addPostAndFile()
    {

        return $this->postService->addPostAndFile();
    }
}
