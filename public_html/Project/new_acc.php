<?php
require_once(__DIR__ . "/../../lib/functions.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
require_once(__DIR__ . "/../../partials/nav.php");
?>