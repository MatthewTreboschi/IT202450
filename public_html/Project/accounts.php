<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
if (isset($_GET["newacc"])) {
    echo("Your new account is: " . $_GET["newacc"]);
}
if (isset($_POST["accNum"])) {
    $accNum = se($_POST, "accNum", null, false);
    $_SESSION["accNum"] = $accNum;
    die(header("Location: transactions.php"));
}
?>
<h1>This is the accounts page</h1>
<div>
    <table>
        <tr>
            <th>Account Number</th>
            <th>Account Type</th>
            <th>Balance</th>
            <th>More Info</th>
        </tr>
        <?php foreach (get_accounts() as $acc) : ?>
        <!--<tr onclick="<?php echo("pst('" . $acc["account_number"] . "'"); ?>)">-->
        <tr>
            <?php $v = $acc["account_number"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $acc["account_type"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $acc["balance"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <td>
                <form method="POST">
                    <button type="submit" name="accNum" value="<?php echo($acc["account_number"]); ?>">More info</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
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