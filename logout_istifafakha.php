<?php
session_start();

// Hapus semua session data
session_unset();
session_destroy();

// Hapus session cookie juga
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

header("Location: loginPetugas_istifafakha.php");
exit;
?>