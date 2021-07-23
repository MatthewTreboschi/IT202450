<?php
require_once(__DIR__ . "/../../lib/functions.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
require_once(__DIR__ . "/../../partials/nav.php");
?>
<h1>Hello World, this is the deposit page</h1>