<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../controllers/admin/ProfileController.php';

requireLogin();

$controller = new ProfileController();
if (is_post_request()) {
    $result = $controller->handlePasswordChange($_POST);
    if (!empty($result['success'])) {
        setFlashMessage('success', $result['success']);
    } elseif (!empty($result['error'])) {
        setFlashMessage('error', $result['error']);
    }
    redirect(BASE_URL . '/admin/profile.php');
} else {
    $data = $controller->index();
}
extract($data);

$bodyClass = 'admin-page';
require_once __DIR__ . '/../templates/layout/header.html.php';
require_once __DIR__ . '/../templates/admin/profile.html.php';
require_once __DIR__ . '/../templates/layout/footer.html.php';
