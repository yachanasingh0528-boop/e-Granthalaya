<?php
require_once __DIR__ . "/functions.php";
$pageTitle = $pageTitle ?? "Dashboard";
$currentPage = basename($_SERVER["PHP_SELF"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> | e-Granthalaya</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
<a class="brand" href="index.php" aria-label="e-Granthalaya Dashboard">
<span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 48 40" role="img"><path d="M3 7.5c8-2 14-.4 21 4v24c-7-4.5-13-6-21-4V7.5Z"/><path d="M45 7.5c-8-2-14-.4-21 4v24c7-4.5 13-6 21-4V7.5Z"/><path d="M24 11.5v24"/></svg></span>
<span class="brand-copy">e-Granthalaya<small>SMART LIBRARY MANAGEMENT SYSTEM</small></span>
</a>
<div class="topbar-right"><span class="status-dot"></span> Admin: <?= e($_SESSION["admin_username"] ?? "Administrator") ?> <a class="button secondary" href="logout.php" style="margin-left:12px;">Logout</a></div>
</header>
<div class="layout">
<aside class="sidebar">
<div class="sidebar-label">MAIN MENU</div>
<a class="<?= $currentPage === 'index.php' ? 'active' : '' ?>" href="index.php"><span>▦</span> Dashboard</a>
<a class="<?= $currentPage === 'books.php' ? 'active' : '' ?>" href="books.php"><span>▤</span> Manage Books</a>
<a class="<?= $currentPage === 'students.php' ? 'active' : '' ?>" href="students.php"><span>♙</span> Students</a>
<a class="<?= $currentPage === 'issue_book.php' ? 'active' : '' ?>" href="issue_book.php"><span>⇄</span> Issue / Return</a>
<div class="sidebar-note"><strong>Your library, organised.</strong><p>Bringing Knowledge Closer to Everyone.</p></div>
</aside>
<main class="main-content">
<?php show_flash(); ?>
