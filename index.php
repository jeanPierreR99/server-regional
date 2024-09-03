<?php
require_once('Controllers/NoticeController.php');
require_once('Controllers/PostController.php');
require_once('Controllers/AdminController.php');
require_once('Controllers/SystemController.php');
require_once('Controllers/GalleryController.php');

$noticeController = new NoticeController();
$postController = new PostController();
$adminController = new AdminController();
$systemController = new SystemController();
$galleryController = new GalleryController();

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json;charset=utf-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

switch ($action) {
    case '':
        header("Location: ./404.php");
        break;
    case 'delete_notice':
        $noticeController->deleteNoticeAndFile($id);
        break;
    case 'add_notice':
        $noticeController->addNoticeAndFile();
        break;
    case 'get_notice':
        $noticeController->getNoticeAndFile();
        break;
    case 'add_post':
        $postController->addPostAndFile();
        break;
    case 'get_post':
        $postController->getPostAndFile();
        break;
    case 'update_post':
        $postController->updatePost();
        break;
    case 'delete_post':
        $postController->deletePostAndFile($id);
        break;
    case 'get_user':
        $adminController->getAdmin();
        break;
    case 'verify_user':
        $adminController->verifyAdmin();
        break;
    case 'system':
        $systemController->getDataSystem();
        break;
    case 'get_gallery':
        $galleryController->getGalleryAndFile();
        break;
    case 'add_gallery':
        $galleryController->addGalleryAndFile();
        break;
    case 'delete_gallery':
        $galleryController->deleteGalleryAndFile($id);
        break;
    default:
        header("Location: ./404.php");
        break;
}
