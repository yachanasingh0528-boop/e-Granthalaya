<?php
require_once "config/db.php";
require_once "includes/functions.php";
require_admin();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    $title = trim($_POST["title"] ?? "");
    $author = trim($_POST["author"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $quantity = filter_var($_POST["quantity"] ?? null, FILTER_VALIDATE_INT);

    try {
        if ($action === "save") {
            $id = (int)($_POST["id"] ?? 0);
            if ($title === "" || $author === "" || $quantity === false || $quantity < 0) {
                throw new Exception("Enter a title, author and a valid non-negative quantity.");
            }
            if ($id > 0) {
                $stmt = $conn->prepare("SELECT available_quantity, quantity FROM books WHERE id=?");
                $stmt->bind_param("i", $id); $stmt->execute();
                $old = $stmt->get_result()->fetch_assoc();
                if (!$old) throw new Exception("Book record not found.");
                $issued = (int)$old["quantity"] - (int)$old["available_quantity"];
                if ($quantity < $issued) throw new Exception("Quantity cannot be less than the number of copies currently issued.");
                $available = $quantity - $issued;
                $stmt = $conn->prepare("UPDATE books SET title=?, author=?, category=?, quantity=?, available_quantity=? WHERE id=?");
                $stmt->bind_param("sssiii", $title, $author, $category, $quantity, $available, $id);
                $stmt->execute();
                set_flash("success", "Book updated successfully.");
            } else {
                $stmt = $conn->prepare("INSERT INTO books (title, author, category, quantity, available_quantity) VALUES (?,?,?,?,?)");
                $stmt->bind_param("sssii", $title, $author, $category, $quantity, $quantity);
                $stmt->execute();
                set_flash("success", "Book added successfully.");
            }
        } elseif ($action === "delete") {
            $id = (int)($_POST["id"] ?? 0);
            $stmt = $conn->prepare("DELETE FROM books WHERE id=?");
            $stmt->bind_param("i", $id); $stmt->execute();
            set_flash("success", "Book deleted successfully.");
        }
    } catch (Throwable $e) {
        set_flash("error", $e instanceof mysqli_sql_exception ? "This book cannot be deleted because it has issue history." : $e->getMessage());
    }
    redirect("books.php");
}

$editBook = null;
if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $stmt = $conn->prepare("SELECT * FROM books WHERE id=?");
    $stmt->bind_param("i", $id); $stmt->execute();
    $editBook = $stmt->get_result()->fetch_assoc();
}
$search = trim($_GET["q"] ?? "");
if ($search !== "") {
    $like = "%" . $search . "%";
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR category LIKE ? ORDER BY id DESC");
    $stmt->bind_param("sss", $like, $like, $like); $stmt->execute(); $books = $stmt->get_result();
} else {
    $books = $conn->query("SELECT * FROM books ORDER BY id DESC");
}
$pageTitle = "Manage Books";
require "includes/header.php";
?>
<section class="page-heading"><div><p class="eyebrow">COLLECTION</p><h1>Manage Books</h1><p class="muted">Add new titles and maintain your library collection.</p></div></section>
<div class="content-grid">
<section class="panel form-panel">
    <div class="panel-heading"><div><h2><?= $editBook ? "Edit book" : "Add a book" ?></h2><p class="muted">Enter the book information below.</p></div></div>
    <form method="post" class="form-grid">
        <input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= e($editBook["id"] ?? 0) ?>">
        <label>Book title<input name="title" required maxlength="150" value="<?= e($editBook["title"] ?? "") ?>" placeholder="e.g. The Alchemist"></label>
        <label>Author<input name="author" required maxlength="100" value="<?= e($editBook["author"] ?? "") ?>" placeholder="Author name"></label>
        <label>Category<input name="category" maxlength="100" value="<?= e($editBook["category"] ?? "") ?>" placeholder="e.g. Fiction"></label>
        <label>Total quantity<input type="number" name="quantity" min="0" required value="<?= e($editBook["quantity"] ?? 1) ?>"></label>
        <div class="form-actions"><button class="button primary" type="submit"><?= $editBook ? "Update book" : "Add book" ?></button><?php if ($editBook): ?><a class="button secondary" href="books.php">Cancel</a><?php endif; ?></div>
    </form>
</section>
<section class="panel">
    <div class="panel-heading"><div><h2>Book collection</h2><p class="muted"><?= $books->num_rows ?> record(s) found</p></div>
    <form method="get" class="search-form"><input type="search" name="q" value="<?= e($search) ?>" placeholder="Search books..."><button class="button secondary">Search</button></form></div>
    <div class="table-wrap"><table><thead><tr><th>Title / Author</th><th>Category</th><th>Copies</th><th>Available</th><th>Actions</th></tr></thead><tbody>
    <?php if ($books->num_rows): while ($book = $books->fetch_assoc()): ?>
    <tr><td><strong><?= e($book["title"]) ?></strong><small class="cell-sub"><?= e($book["author"]) ?></small></td><td><?= e($book["category"]) ?></td><td><?= (int)$book["quantity"] ?></td><td><?= (int)$book["available_quantity"] ?></td><td class="actions"><a class="mini-link" href="books.php?edit=<?= (int)$book["id"] ?>">Edit</a><form method="post" class="inline-form" data-confirm="Delete this book?"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$book["id"] ?>"><button class="mini-link danger-link">Delete</button></form></td></tr>
    <?php endwhile; else: ?><tr><td colspan="5" class="empty">No books found. Add your first book using the form.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
</div>
<?php require "includes/footer.php"; ?>
