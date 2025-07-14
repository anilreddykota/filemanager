<?php
// redirect to  specific step based on installation progress
if (file_exists('../config.php')) {
    header("Location: done.php");
    exit;
}
header("Location: step1.php");
exit;
