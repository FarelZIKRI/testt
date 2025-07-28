<?php
require_once '../config/admin.php';

logoutAdmin();

header('Location: ../admin_login.php?message=logout_success');
exit;
?>