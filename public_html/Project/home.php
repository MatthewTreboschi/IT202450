<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
?>
<h1>Home</h1>
<h5>Welcome, <?php se(get_username()); ?>!</h5>
<div>
    <div class="sidenav">
        <a href="profile.php">Profile</a>
        <a href="new_acc.php">Create Account</a>
        <a href="new_loan.php">Take Loan</a>
        <a href="accounts.php">My Accounts</a>
        <a href="deposit.php">Deposit</a>
        <a href="withdraw.php">Withdrawal</a>
        <a href="transfer_in.php">Internal Transfer</a>
        <a href="transfer_ext.php">External Transfer</a>
    </div>
    <h2>MATT'S<br>BANK</h2>
</div>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>