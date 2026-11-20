<?php
require_once "config/db.php";
require_once "includes/functions.php";
require_admin();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    try {
        if ($action === "save") {
            $id = (int)($_POST["id"] ?? 0);
            $name = trim($_POST["name"] ?? "");
            $roll = trim($_POST["roll_number"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $phone = trim($_POST["phone"] ?? "");
            if ($name === "" || $roll === "") throw new Exception("Student name and roll number are required.");
            if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception("Enter a valid email address.");
            if ($id > 0) {
                $stmt = $conn->prepare("UPDATE students SET name=?, roll_number=?, email=?, phone=? WHERE id=?");
                $stmt->bind_param("ssssi", $name, $roll, $email, $phone, $id); $stmt->execute();
                set_flash("success", "Student updated successfully.");
            } else {
                $stmt = $conn->prepare("INSERT INTO students (name, roll_number, email, phone) VALUES (?,?,?,?)");
                $stmt->bind_param("ssss", $name, $roll, $email, $phone); $stmt->execute();
                set_flash("success", "Student registered successfully.");
            }
        } elseif ($action === "delete") {
            $id = (int)($_POST["id"] ?? 0);
            $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
            $stmt->bind_param("i", $id); $stmt->execute();
            set_flash("success", "Student deleted successfully.");
        }
    } catch (Throwable $e) {
        if ($e instanceof mysqli_sql_exception && (int)$e->getCode() === 1062) {
            set_flash("error", "This roll number is already registered.");
        } elseif ($e instanceof mysqli_sql_exception) {
            set_flash("error", "This student has issue history and cannot be deleted.");
        } else {
            set_flash("error", $e->getMessage());
        }
    }
    redirect("students.php");
}

$editStudent = null;
if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
    $stmt->bind_param("i", $id); $stmt->execute();
    $editStudent = $stmt->get_result()->fetch_assoc();
}
$search = trim($_GET["q"] ?? "");
if ($search !== "") {
    $like = "%" . $search . "%";
    $stmt = $conn->prepare("SELECT * FROM students WHERE name LIKE ? OR roll_number LIKE ? OR email LIKE ? ORDER BY id DESC");
    $stmt->bind_param("sss", $like, $like, $like); $stmt->execute(); $students = $stmt->get_result();
} else {
    $students = $conn->query("SELECT * FROM students ORDER BY id DESC");
}
$pageTitle = "Students";
require "includes/header.php";
?>
<section class="page-heading"><div><p class="eyebrow">MEMBERS</p><h1>Students</h1><p class="muted">Register and maintain student information.</p></div></section>
<div class="content-grid">
<section class="panel form-panel"><div class="panel-heading"><div><h2><?= $editStudent ? "Edit student" : "Register a student" ?></h2><p class="muted">Student details</p></div></div>
<form method="post" class="form-grid">
<input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= e($editStudent["id"] ?? 0) ?>">
<label>Full name<input name="name" required value="<?= e($editStudent["name"] ?? "") ?>" placeholder="Student name"></label>
<label>Roll number<input name="roll_number" required value="<?= e($editStudent["roll_number"] ?? "") ?>" placeholder="e.g. 2401040001"></label>
<label>Email<input type="email" name="email" value="<?= e($editStudent["email"] ?? "") ?>" placeholder="student@example.com"></label>
<label>Phone<input name="phone" maxlength="15" value="<?= e($editStudent["phone"] ?? "") ?>" placeholder="Phone number"></label>
<div class="form-actions"><button class="button primary"><?= $editStudent ? "Update student" : "Register student" ?></button><?php if ($editStudent): ?><a class="button secondary" href="students.php">Cancel</a><?php endif; ?></div>
</form></section>
<section class="panel"><div class="panel-heading"><div><h2>Registered students</h2><p class="muted"><?= $students->num_rows ?> record(s) found</p></div><form method="get" class="search-form"><input type="search" name="q" value="<?= e($search) ?>" placeholder="Search students..."><button class="button secondary">Search</button></form></div>
<div class="table-wrap"><table><thead><tr><th>Name</th><th>Roll number</th><th>Email</th><th>Phone</th><th>Actions</th></tr></thead><tbody>
<?php if ($students->num_rows): while ($student = $students->fetch_assoc()): ?>
<tr><td><strong><?= e($student["name"]) ?></strong></td><td><?= e($student["roll_number"]) ?></td><td><?= e($student["email"]) ?></td><td><?= e($student["phone"]) ?></td><td class="actions"><a class="mini-link" href="students.php?edit=<?= (int)$student["id"] ?>">Edit</a><form method="post" class="inline-form" data-confirm="Delete this student?"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$student["id"] ?>"><button class="mini-link danger-link">Delete</button></form></td></tr>
<?php endwhile; else: ?><tr><td colspan="5" class="empty">No students found. Register the first student.</td></tr><?php endif; ?>
</tbody></table></div></section>
</div>
<?php require "includes/footer.php"; ?>
