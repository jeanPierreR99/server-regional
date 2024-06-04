<?php
require_once('Controllers/NoticeController.php');
require_once('Controllers/AdminController.php');

$noticeController = new NoticeController();
$adminController = new AdminController();

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
// Permitir solicitudes desde cualquier origen (no recomendado en producción)
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
    case 'get_user':
        $adminController->getAdmin();
        break;
    case 'verify_user':
        $adminController->verifyAdmin();
        break;
    default:
        header("Location: ./404.php");
        break;
}
