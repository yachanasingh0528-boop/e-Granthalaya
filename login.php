<?php
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/includes/functions.php";
if (is_admin_logged_in()) redirect("index.php");
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    if ($username === "" || $password === "") {
        $error = "Enter both username and password.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, username, password_hash FROM admin WHERE username=? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $admin = $stmt->get_result()->fetch_assoc();
            if ($admin && password_verify($password, $admin["password_hash"])) {
                session_regenerate_id(true);
                $_SESSION["admin_id"] = (int)$admin["id"];
                $_SESSION["admin_username"] = $admin["username"];
                set_flash("success", "Welcome back, " . $admin["username"] . "!");
                redirect("index.php");
            } else { $error = "Incorrect username or password."; }
        } catch (Throwable $e) { $error = "Login failed. Check that the admin table exists and database is connected."; }
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | e-Granthalaya</title><link rel="stylesheet" href="assets/style.css">
<style>
body{min-height:100vh;background:linear-gradient(135deg,#071b3a 0%,#123b72 52%,#eaf1ff 52%,#f7f9fd 100%);display:grid;place-items:center;margin:0;font-family:'DM Sans',Arial,sans-serif}
.login-shell{width:min(940px,calc(100% - 36px));display:grid;grid-template-columns:1fr 1fr;background:#fff;border-radius:22px;overflow:hidden;box-shadow:0 24px 70px #061a3833}
.login-brand{padding:54px 42px;background:linear-gradient(145deg,#0b2347,#174c8c);color:#fff;display:flex;flex-direction:column;justify-content:space-between;min-height:410px}
.login-logo{display:flex;align-items:center;gap:13px;font:800 25px Manrope,Arial,sans-serif}.login-logo-mark{width:54px;height:48px;border-radius:14px;background:#ffffff1c;display:grid;place-items:center}.login-logo svg{width:39px;fill:none;stroke:#fff;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round}.login-brand h2{font:800 29px Manrope,Arial,sans-serif;line-height:1.25;margin:0 0 13px}.login-brand p{color:#d4e3f7;line-height:1.7;font-size:13px;margin:0}.login-form-wrap{padding:54px 44px;display:flex;flex-direction:column;justify-content:center}.login-form-wrap h1{margin:0 0 7px;color:#102c52;font:800 25px Manrope,Arial,sans-serif}.login-form-wrap>p{margin:0 0 24px;color:#7b8ba2;font-size:13px}.login-card label{display:block;font-weight:700;color:#344b6a;margin:17px 0 7px;font-size:12px}.login-card input{width:100%;box-sizing:border-box;padding:13px 14px;border:1px solid #d7e0ed;border-radius:9px;font-size:14px;outline:none}.login-card input:focus{border-color:#2864d8;box-shadow:0 0 0 3px #2864d817}.login-card button{width:100%;margin-top:24px;cursor:pointer;padding:13px}.login-error{background:#fff0f0;color:#a32626;padding:11px;border-radius:8px;margin:14px 0;font-size:12px}.login-foot{margin-top:22px;color:#9aa7b8;font-size:10px;text-align:center}
@media(max-width:700px){body{background:#f2f6fc}.login-shell{grid-template-columns:1fr}.login-brand{min-height:180px;padding:28px}.login-brand h2{font-size:22px;margin-top:35px}.login-form-wrap{padding:32px 26px}}
</style></head><body><div class="login-shell">
<section class="login-brand"><div class="login-logo"><span class="login-logo-mark"><svg viewBox="0 0 48 40"><path d="M3 7.5c8-2 14-.4 21 4v24c-7-4.5-13-6-21-4V7.5Z"/><path d="M45 7.5c-8-2-14-.4-21 4v24c7-4.5 13-6 21-4V7.5Z"/><path d="M24 11.5v24"/></svg></span>e-Granthalaya</div><div><h2>Bringing Knowledge Closer to Everyone.</h2><p>A simple, smart space to manage books, students and library activity.</p></div></section>
<section class="login-form-wrap"><h1>Welcome back</h1><p>Sign in to continue to your library dashboard.</p><div class="login-card">
<?php show_flash(); ?><?php if ($error !== ""): ?><div class="login-error"><?= e($error) ?></div><?php endif; ?>
<form method="post"><label for="username">Username</label><input id="username" name="username" required maxlength="50" autocomplete="username" placeholder="Enter admin username">
<label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Enter your password">
<button class="button primary" type="submit">Sign in to dashboard</button></form></div><div class="login-foot">SECURE ADMIN ACCESS · e-GRANTHALAYA</div></section>
</div></body></html>
