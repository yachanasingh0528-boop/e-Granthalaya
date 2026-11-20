<?php
require_once "config/db.php";
require_once "includes/functions.php";
require_admin();

$totalBooks = (int)$conn->query("SELECT COALESCE(SUM(quantity),0) AS total FROM books")->fetch_assoc()["total"];
$availableBooks = (int)$conn->query("SELECT COALESCE(SUM(available_quantity),0) AS total FROM books")->fetch_assoc()["total"];
$totalStudents = (int)$conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()["total"];
$issuedBooks = (int)$conn->query("SELECT COUNT(*) AS total FROM issue_records WHERE status='Issued'")->fetch_assoc()["total"];

$recent = $conn->query("SELECT ir.id, b.title, s.name, ir.issue_date, ir.status
    FROM issue_records ir
    JOIN books b ON b.id=ir.book_id
    JOIN students s ON s.id=ir.student_id
    ORDER BY ir.id DESC LIMIT 6");

$pageTitle = "Dashboard";
require "includes/header.php";
?>
<section class="page-heading">
    <div><p class="eyebrow">OVERVIEW</p><h1>Dashboard</h1><p class="muted">Welcome! Here's what's happening in your library.</p></div>
    <a class="button primary" href="issue_book.php">＋ Issue a book</a>
</section>

<section class="stats-grid">
    <article class="stat-card"><div class="stat-icon blue">▤</div><div><p>Total book copies</p><strong><?= $totalBooks ?></strong><small>Across all titles</small></div></article>
    <article class="stat-card"><div class="stat-icon green">✓</div><div><p>Available copies</p><strong><?= $availableBooks ?></strong><small>Ready to issue</small></div></article>
    <article class="stat-card"><div class="stat-icon purple">♙</div><div><p>Registered students</p><strong><?= $totalStudents ?></strong><small>Library members</small></div></article>
    <article class="stat-card"><div class="stat-icon orange">⇄</div><div><p>Currently issued</p><strong><?= $issuedBooks ?></strong><small>Not yet returned</small></div></article>
</section>

<section class="panel">
    <div class="panel-heading"><div><h2>Recent issue activity</h2><p class="muted">Latest book issue and return records</p></div><a class="text-link" href="issue_book.php">View all →</a></div>
    <div class="table-wrap">
        <table><thead><tr><th>Book</th><th>Student</th><th>Issue date</th><th>Status</th></tr></thead><tbody>
        <?php if ($recent->num_rows): while ($row = $recent->fetch_assoc()): ?>
            <tr><td class="strong"><?= e($row["title"]) ?></td><td><?= e($row["name"]) ?></td><td><?= e($row["issue_date"]) ?></td><td><span class="pill <?= $row["status"] === "Issued" ? "warning" : "success" ?>"><?= e($row["status"]) ?></span></td></tr>
        <?php endwhile; else: ?>
            <tr><td colspan="4" class="empty">No issue records yet. Add books and students, then issue your first book.</td></tr>
        <?php endif; ?>
        </tbody></table>
    </div>
</section>
<section class="quick-grid">
    <a class="quick-card" href="books.php"><span>▤</span><div><strong>Manage books</strong><small>Add, edit, search or delete book records.</small></div><b>→</b></a>
    <a class="quick-card" href="students.php"><span>♙</span><div><strong>Manage students</strong><small>Maintain student registration details.</small></div><b>→</b></a>
    <a class="quick-card" href="issue_book.php"><span>⇄</span><div><strong>Issue & return</strong><small>Track books borrowed by students.</small></div><b>→</b></a>
</section>
<?php require "includes/footer.php"; ?>
