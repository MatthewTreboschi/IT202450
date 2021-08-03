<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
$page = 1;
if (isset($_GET["page"])){
    $page = se($_GET, "page", null, false);
}
if (isset($_GET["newacc"])) {
    echo("Your new account is: " . $_GET["newacc"]);
}
if (isset($_GET["removed"])) {
    echo("You closed the account: " . $_GET["removed"]);
}
if (isset($_POST["accNum"])) {
    $accNum = se($_POST, "accNum", null, false);
    $_SESSION["accNum"] = $accNum;
    die(header("Location: transactions.php"));
}
$search = "";
$all = false;
if (isset($_POST["search"])) {
    $search = "%" . $_POST["search"] . "%";
    if ($_POST["accs"]=="all"){
        $all = true;
    }
}
$accounts = get_accounts(true, true, $page, $search, $all);
$total_pages = ceil(count_accounts()/5);
?>
<h1>This is the accounts page</h1>
<?php if ($_SESSION["admin"]) { ?>
    <h5>By default, these are the accounts of the user you are controlling</h5>
    <h5>This form will allow you to search for anyone's account by account number</h5>
    <form method="POST">
        <div>
            <label for="search">Search</label>
            <input type="text" name="search" id="search" />
        </div>
        <div>
            <label for="accs">Search All accounts or just this user's?</label>
            <select name="accs" id="accs" >
                <option value="this">This user's</option>
                <option value="all">All accounts</option>
            </select>
        </div>
        <div>
            <input type="submit" name="submit" value="Filter" />
        </div>
    </form>
<?php } ?>
<div>
    <table>
        <tr>
            <th>Account Number</th>
            <th>Account Type</th>
            <th>Balance</th>
            <th>APY</th>
            <th>More Info</th>
        </tr>
        <?php foreach ($accounts as $acc) : ?>
        <!--<tr onclick="<?php echo("pst('" . $acc["account_number"] . "'"); ?>)">-->
        <tr>
            <?php $v = $acc["account_number"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $acc["account_type"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $acc["balance"]; if ($v<0) $v*=-1; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $acc["apy"]; ?>
            <td value ="<?php se($v); ?>"><?php if ($v==0.0) {echo"-";} else {se($v);} ?></td>
            <td>
                <form method="POST">
                    <button type="submit" name="accNum" value="<?php echo($acc["account_number"]); ?>">More info</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <div>
        <?php /** required $total_pages and $page to be set */ ?>
        <?php include(__DIR__ . "/../../partials/pagination.php"); ?>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous">
    function pst(accNum) {
        console.log(accNum)
        $.post("accounts.php", {
            "accNum": accNum
            }, (res)=>{
            console.log("resp", res)
        })
    }
</script>