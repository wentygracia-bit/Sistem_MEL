<?php
// Main Application Router & Bootstrap
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/helpers/AuthHelper.php';

// Controllers
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/ProjectController.php';
require_once __DIR__ . '/controllers/FieldUpdateController.php';
require_once __DIR__ . '/controllers/EvaluationController.php';
require_once __DIR__ . '/controllers/CorrectionController.php';
require_once __DIR__ . '/controllers/MELResultController.php';
require_once __DIR__ . '/controllers/NotificationController.php';

// URI normalization
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = BASE_URL;

if (strpos($requestUri, $basePath) === 0) {
    $path = substr($requestUri, strlen($basePath));
} else {
    $path = $requestUri;
}

$path = strtok($path, '?');
$path = rtrim($path, '/');
if (empty($path)) {
    $path = '/';
}

// Router dispatch table
switch ($path) {
    case '/':
    case '/public-dashboard':
        (new DashboardController())->publicView();
        break;

    case '/login':
        $auth = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->processLogin();
        } else {
            $auth->showLogin();
        }
        break;

    case '/logout':
        (new AuthController())->logout();
        break;

    case '/dashboard':
        (new DashboardController())->index();
        break;

    case '/projects':
        (new ProjectController())->index();
        break;

    case '/projects/detail':
        (new ProjectController())->detail();
        break;

    case '/projects/create':
        (new ProjectController())->create();
        break;

    case '/projects/add-member':
        (new ProjectController())->addMember();
        break;

    case '/projects/create-activity':
        (new ProjectController())->createActivity();
        break;

    case '/projects/add-indicator':
        (new ProjectController())->addIndicator();
        break;

    case '/field-updates':
        (new FieldUpdateController())->index();
        break;

    case '/field-updates/detail':
        (new FieldUpdateController())->detail();
        break;

    case '/field-updates/submit':
        (new FieldUpdateController())->submit();
        break;

    case '/evaluations':
        (new EvaluationController())->index();
        break;

    case '/evaluations/submit':
        (new EvaluationController())->submit();
        break;

    case '/corrections':
        (new CorrectionController())->index();
        break;

    case '/corrections/detail':
        (new CorrectionController())->detail();
        break;

    case '/corrections/respond':
        (new CorrectionController())->respond();
        break;

    case '/mel-results':
        (new MELResultController())->index();
        break;

    case '/mel-results/detail':
        (new MELResultController())->detail();
        break;

    case '/mel-results/create':
        (new MELResultController())->create();
        break;

    case '/mel-results/approve':
        (new MELResultController())->approve();
        break;

    case '/notifications':
        (new NotificationController())->index();
        break;

    case '/notifications/read':
        (new NotificationController())->markAsRead();
        break;

    default:
        http_response_code(404);
        echo "<h2 style='font-family:sans-serif; text-align:center; margin-top:50px;'>404 - Halaman Tidak Ditemukan</h2>";
        echo "<p style='text-align:center;'><a href='" . BASE_URL . "/dashboard'>Kembali ke Dashboard</a></p>";
        break;
}
