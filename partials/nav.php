<?php
//Note: this is to resolve cookie issues with port numbers
$domain = $_SERVER["HTTP_HOST"];
if (strpos($domain, ":")) {
    $domain = explode(":", $domain)[0];
}
session_set_cookie_params([
    "lifetime" => 60 * 60,
    "path" => "/Project",
    //"domain" => $_SERVER["HTTP_HOST"] || "localhost",
    "domain" => $domain,
    "secure" => true,
    "httponly" => true,
    "samesite" => "lax"
]);
session_start();
require_once(__DIR__ . "/../lib/functions.php");

?>
<style>
<?php require_once(__DIR__ . "/../partials/style.css"); ?>
</style>
<div style="background-color: #ff8533">
<p id="navig">
<?php if (is_logged_in()) : ?>
    <a href="home.php">Home</a>
<?php endif; ?>
<?php if (!is_logged_in()) : ?>
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
<?php endif; ?>
<?php if (is_logged_in()) : ?>
    <a href="logout.php">Logout</a>
<?php endif; ?>
</p>
</div>