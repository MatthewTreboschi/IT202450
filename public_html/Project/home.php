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
<?php if ($_SESSION["admin"]) {
    if (isset($_POST["active"])) {
        toggle_active($_POST["active"]);
    }
    if (isset($_POST["control"])) {
        $_SESSION["user"] = json_decode($_POST["control"], true);
        echo("You're now in the account of " . $_SESSION["user"]["email"] . "<br>You still have all of your admin privileges");
    }
    $first = "";
    $last = "";
    $page = 1;
    if (isset($_GET["first"])) {
        $first = $_GET["first"];
    }
    if (isset($_GET["last"])) {
        $last = $_GET["last"];
    }
    if (isset($_GET["page"])) {
        $page = $_GET["page"];
    }
    $total_pages = count_users($first, $last, $page);
    $users=get_users($first, $last);

    ?>
    <form method="GET">
        <div>
            <label for="first">First Name: </label>
            <input type="text" name="first" id="first" />
        </div>
        <div>
            <label for="last">Last Name: </label>
            <input type="text" name="last" id="last" />
        </div>
        <div>
            <input type="submit" name="submit" value="Filter" />
        </div>
    </form>
    <table>
        <tr>
            <th>User ID</th>
            <th>Email</th>
            <th>Username</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Active</th>
            <th>Take control</th>
            <th>Toggle Active</th>
        </tr>
        <?php foreach($users as $user): ?>
        <tr>
            <?php $v = $user["id"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $user["email"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $user["username"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $user["first_name"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $user["last_name"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $user["is_active"]; ?>
            <?php if ($v) $v = "true"; else $v = "false"; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <td>
                <form method="POST">
                    <button type="submit" name="control" value='<?php echo(json_encode($user)); ?>'>Use This Account</button>
                </form>
            </td>
            <td>
                <form method="POST">
                    <button type="submit" name="active" value="<?php echo($user["id"]); ?>">Toggle active</button>
                </form>
            </td>
        </tr>
        <?php endforeach;?>
    </table>
    <div>
        <?php /** required $total_pages and $page to be set */ ?>
        <?php include(__DIR__ . "/../../partials/pagination.php"); ?>
    </div>
<?php } ?>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>