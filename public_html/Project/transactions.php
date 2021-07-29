<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
$accNum = $_SESSION["accNum"];
$info = get_account_info($accNum);
?>
<h1>This is the transactions page for account <?php echo($accNum)?></h1>
<h3>Account Number: <?php se($info["account_number"]); ?></h3>
<h3>Account Type: <?php se($info["account_type"]); ?></h3>
<h3>Balance: <?php se($info["balance"]); ?></h3>
<h3>Opened: <?php se($info["created"]); ?></h3>
<div>
    <h4>Filter: </h4>
    <form method="POST">
        <label for="start">Start date: </label>
        <input type="date" name="start" id="start" />
    </form>
    <h4>Filtered transactions: </h4>
    <table>
        <tr>
            <th>Other Transactant</th>
            <th>Transaction Type</th>
            <th>Balance Change</th>
            <th>Memo</th>
            <th>Final Balance</th>
            <th>Time and Date</th>
        </tr>
        <?php foreach (get_transactions($accNum) as $transaction) : ?>
        <tr>
            <?php $v = $transaction["dest"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $transaction["transaction_type"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $transaction["bal_change"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $transaction["memo"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $transaction["expected_total"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $transaction["created"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>