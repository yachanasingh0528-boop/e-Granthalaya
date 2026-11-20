<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8"); }
function redirect(string $url): void { header("Location: " . $url); exit; }
function set_flash(string $type, string $message): void { $_SESSION["flash"] = ["type"=>$type, "message"=>$message]; }
function show_flash(): void {
    if (!empty($_SESSION["flash"])) {
        $flash = $_SESSION["flash"];
        echo '<div class="alert ' . e($flash["type"]) . '">' . e($flash["message"]) . '</div>';
        unset($_SESSION["flash"]);
    }
}
function is_admin_logged_in(): bool {
    return isset($_SESSION["admin_id"]) && (int)$_SESSION["admin_id"] > 0;
}
function require_admin(): void {
    if (!is_admin_logged_in()) {
        set_flash("error", "Please log in as administrator to continue.");
        redirect("login.php");
    }
}
