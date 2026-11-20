<?php
require_once "config/db.php";
require_once "includes/functions.php";
require_admin();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    try {
        if ($action === "issue") {
            $bookId = (int)($_POST["book_id"] ?? 0);
            $studentId = (int)($_POST["student_id"] ?? 0);
            $issueDate = $_POST["issue_date"] ?? date("Y-m-d");
            if (!$bookId || !$studentId) throw new Exception("Select both a book and a student.");
            $conn->begin_transaction();
            $stmt = $conn->prepare("SELECT available_quantity FROM books WHERE id=? FOR UPDATE");
            $stmt->bind_param("i", $bookId); $stmt->execute();
            $book = $stmt->get_result()->fetch_assoc();
            if (!$book || (int)$book["available_quantity"] < 1) throw new Exception("This book is not currently available.");
            $stmt = $conn->prepare("INSERT INTO issue_records (book_id, student_id, issue_date, status) VALUES (?,?,?,'Issued')");
            $stmt->bind_param("iis", $bookId, $studentId, $issueDate); $stmt->execute();
            $stmt = $conn->prepare("UPDATE books SET available_quantity=available_quantity-1 WHERE id=?");
            $stmt->bind_param("i", $bookId); $stmt->execute();
            $conn->commit();
            set_flash("success", "Book issued successfully.");
        } elseif ($action === "return") {
            $recordId = (int)($_POST["record_id"] ?? 0);
            $conn->begin_transaction();
            $stmt = $conn->prepare("SELECT book_id, status FROM issue_records WHERE id=? FOR UPDATE");
            $stmt->bind_param("i", $recordId); $stmt->execute();
            $record = $stmt->get_result()->fetch_assoc();
            if (!$record || $record["status"] !== "Issued") throw new Exception("This issue record is not active.");
            $stmt = $conn->prepare("UPDATE issue_records SET status='Returned', return_date=CURDATE() WHERE id=?");
            $stmt->bind_param("i", $recordId); $stmt->execute();
            $stmt = $conn->prepare("UPDATE books SET available_quantity=available_quantity+1 WHERE id=?");
            $stmt->bind_param("i", $record["book_id"]); $stmt->execute();
            $conn->commit();
            set_flash("success", "Book returned successfully.");
        }
    } catch (Throwable $e) {
        try { $conn->rollback(); } catch (Throwable $ignored) {}
        set_flash("error", $e->getMessage());
    }
    redirect("issue_book.php");
}

$books = $conn->query("SELECT id, title, available_quantity FROM books WHERE available_quantity > 0 ORDER BY title");
$students = $conn->query("SELECT id, name, roll_number FROM students ORDER BY name");
$records = $conn->query("SELECT ir.id, b.title, s.name, s.roll_number, ir.issue_date, ir.return_date, ir.status
    FROM issue_records ir JOIN books b ON b.id=ir.book_id JOIN students s ON s.id=ir.student_id
    ORDER BY ir.id DESC");
$pageTitle = "Issue / Return";
require "includes/header.php";
?>
<section class="page-heading"><div><p class="eyebrow">CIRCULATION</p><h1>Issue & Return</h1><p class="muted">Issue available books to students and record returns.</p></div></section>
<div class="content-grid issue-grid">
<section class="panel form-panel"><div class="panel-heading"><div><h2>Issue a book</h2><p class="muted">Choose a book and student.</p></div></div>
<?php if ($books->num_rows && $students->num_rows): ?>
<form method="post" class="form-grid">
<input type="hidden" name="action" value="issue">
<label>Select book<select name="book_id" required><option value="">Choose a book</option><?php while ($book = $books->fetch_assoc()): ?><option value="<?= (int)$book["id"] ?>"><?= e($book["title"]) ?> (<?= (int)$book["available_quantity"] ?> available)</option><?php endwhile; ?></select></label>
<label>Select student<select name="student_id" required><option value="">Choose a student</option><?php while ($student = $students->fetch_assoc()): ?><option value="<?= (int)$student["id"] ?>"><?= e($student["name"]) ?> — <?= e($student["roll_number"]) ?></option><?php endwhile; ?></select></label>
<label>Issue date<input type="date" name="issue_date" value="<?= date("Y-m-d") ?>" required></label>
<div class="form-actions"><button class="button primary">Confirm issue</button></div>
</form>
<?php else: ?><div class="empty-box">You need at least one available book and one registered student before issuing a book. Add them in the respective sections.</div><?php endif; ?>
</section>
<section class="panel"><div class="panel-heading"><div><h2>Issue records</h2><p class="muted"><?= $records->num_rows ?> record(s)</p></div></div>
<div class="table-wrap"><table><thead><tr><th>Book / Student</th><th>Issue date</th><th>Return date</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php if ($records->num_rows): while ($record = $records->fetch_assoc()): ?>
<tr><td><strong><?= e($record["title"]) ?></strong><small class="cell-sub"><?= e($record["name"]) ?> (<?= e($record["roll_number"]) ?>)</small></td><td><?= e($record["issue_date"]) ?></td><td><?= e($record["return_date"] ?? "—") ?></td><td><span class="pill <?= $record["status"] === "Issued" ? "warning" : "success" ?>"><?= e($record["status"]) ?></span></td><td><?php if ($record["status"] === "Issued"): ?><form method="post" data-confirm="Mark this book as returned?"><input type="hidden" name="action" value="return"><input type="hidden" name="record_id" value="<?= (int)$record["id"] ?>"><button class="mini-link">Return</button></form><?php else: ?>—<?php endif; ?></td></tr>
<?php endwhile; else: ?><tr><td colspan="5" class="empty">No issue records yet.</td></tr><?php endif; ?>
</tbody></table></div></section>
</div>
<?php require "includes/footer.php"; ?>
