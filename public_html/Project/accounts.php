<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
if (isset($_POST["submit"])) {
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
        </tr>
        <?php foreach (get_accounts() as $acc) : ?>
        <tr onclick="<?php echo("post(" . $acc["account_number"]); ?>)">
            <?php $v = $acc["account_number"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $acc["account_type"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $acc["balance"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<script>
    function post(accNum) {
        $.post({"accNum": accNum})
    }
</script>
