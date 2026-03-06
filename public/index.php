<?php
$page = $_GET['page'] ?? 'dashboard';

$allowedPages = [
    'dashboard',
    'products',
    'add_products',
    'add_category',
    'edit_products',
    'category',
    'stock_in',
    'stock_out',
    'reports',
    'category-delete',
    'delete_products',
    'print_report'
];

if (!in_array($page, $allowedPages)) {
    $page = 'dashboard';
}

if ($page === 'print_report') {
    require_once __DIR__ . '/../pages/reports/print_reports.php';
    exit;
}

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php'; 

switch ($page) {
    case 'products':
        require_once __DIR__ . '/../pages/products/products_list.php';
        break;
    case 'add_products':
        require_once __DIR__ . '/../pages/products/add_products.php';
        break;
    case 'edit_products':
        require_once __DIR__ . '/../pages/products/edit_products.php';
        break;
    case 'add_category':
        require_once __DIR__ . '/../pages/categories/add_category.php';
        break;
    case 'category':
        require_once __DIR__ . '/../pages/categories/category_list.php';
        break;
    case 'stock_in':
        require_once __DIR__ . '/../pages/stock/in.php';
        break;
    case 'stock_out':
        require_once __DIR__ . '/../pages/stock/out.php';
        break;
    case 'reports':
        require_once __DIR__ . '/../pages/reports/reports.php';
        break;
    case 'category-delete':
        require_once __DIR__ . '/../pages/categories/delete_category.php';
        break;
    case 'delete_products':
        require_once __DIR__ . '/../pages/products/delete_products.php';
        break;
        case 'print_report':
        require_once __DIR__ . '/../pages/reports/print_reports.php';
        break;

    default:
        require_once __DIR__ . '/../pages/dashboard.php';
}

require_once __DIR__ . '/../layouts/footer.php';
