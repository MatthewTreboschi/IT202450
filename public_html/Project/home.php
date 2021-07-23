<?php
require_once(__DIR__ . "/../../lib/functions.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
require_once(__DIR__ . "/../../partials/nav.php");
?>
<h1>Home</h1>
<h5>Welcome, <?php se(get_username()); ?>!</h5>
<div>
    <div class="sidenav">
        <a href="profile.php">Profile</a>
        <a href="new_acc.php">Create Account</a>
        <a href="accounts.php">My Accounts</a>
        <a href="deposit.php">Deposit</a>
        <a href="withdraw.php">Withdrawal</a>
    </div>
    <h2>MATT'S<br>BANK</h2>
</div>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>