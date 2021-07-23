<?php
require_once(__DIR__ . "/db.php");

function se($v, $k = null, $default = "", $isEcho = true) {
    if (is_array($v) && isset($k) && isset($v[$k])) {
        $returnValue = $v[$k];
    } else if (is_object($v) && isset($k) && isset($v->$k)) {
        $returnValue = $v->$k;
    } else {
        $returnValue = $v;
    }
    if (!isset($returnValue)) {
        $returnValue = $default;
    }
    if ($isEcho) {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        echo htmlspecialchars($returnValue, ENT_QUOTES);
    } else {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        return htmlspecialchars($returnValue, ENT_QUOTES);
    }
}
function sanitize_email($email = "") {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}
function is_valid_email($email = "") {
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}
//User Helpers
function is_logged_in() {
    return isset($_SESSION["user"]); //se($_SESSION, "user", false, false);
}
function get_username() {
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "username", "", false);
    }
    return "";
}
function get_user_email() {
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "email", "", false);
    }
    return "";
}
function get_user_id() {
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "id", false, false);
    }
    return false;
}
function new_acc($deposit, $accType){
    if (is_logged_in()){
        $userid = get_user_id();
        //letters are in qwerty order. I wanted 1 of each and order didnt matter so i swiped my finger across each row of keys
        $strChars = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
        $db = getDB();
        $entered = False;
        $stmt = $db->prepare("INSERT INTO Accounts (account_number, user_id, account_type) VALUES (:accNum, :userid, :accType)");
        while(!$entered){
            try {
                $accNum = "";
                for ($i = 0; $i<12; $i++){
                    $accNum .= substr($strChars, rand(0,61), 1);
                }
                $stmt->execute([":accNum" => $accNum, ":userid" => $userid, ":accType"=>$accType]);
                $entered = True;
            } catch (PDOException $e) {
                $entered = False;
            }
        }
        transaction($accNum, "000000000000", $deposit, "deposit", "Initial deposit");
        flash("Welcome! Your account has been created successfully", "success");
        //die(header("Location: accounts.php"));
    }
    else {
        flash("You're not logged in!", "Whoops!");
    }
}
function get_balance($accNum = ""){
    $balance = 0;
    if ($accNum){
        $db = getDB();
        $stmt = $db->prepare("SELECT balance FROM Accounts WHERE account_number = :accNum");
        try {
            $stmt->execute([":accNum" => $accNum]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                $balance = (int)se($r, "balance", 0, false);
            }
        } catch (PDOException $e) {
            error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
        }
    }
    return $balance;
}
function get_account_id($accNum = ""){
    $id = 0;
    if ($accNum){
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM Accounts WHERE account_number = :accNum");
        try {
            $stmt->execute([":accNum" => $accNum]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                $id = (int)se($r, "id", 0, false);
            }
        } catch (PDOException $e) {
            error_log("Unknown error during account id check: " . var_export($e->errorInfo, true));
        }
    }
    return $id;
}
function transaction($to = "", $from = "", $amt = 0, $type = "deposit", $memo = "No memo"){
    try {
        if($to && $from){
            $toBalance = get_balance($to);
            $fromBalance = get_balance($from);
            $toAccID = get_account_id($to);
            $fromAccID = get_account_id($from);
            if($amt<0){
                flash("Negative amount for transaction", "Negative amount");
            }
            else if((!$toBalance+$amt<0 || $to == "000000000000") && (!$fromBalance-$amt<0 || $from == "000000000000")){
                $db = getDB();
                $stmt = $db->prepare("UPDATE Accounts SET balance = balance + :amt WHERE account_number = :to");
                $stmt->execute([":amt" => $amt, ":to"=>$to]);

                $stmt = $db->prepare("UPDATE Accounts SET balance = balance - :amt WHERE account_number = :from");
                $stmt->execute([":amt" => $amt, ":from"=>$from]);

                $stmt = $db->prepare("INSERT INTO Transactions (source, dest, bal_change, transaction_type, memo, expected total) VALUES (:from, :to, :amt, :type, :memo, :total)");
                $stmt->execute([":from"=>$fromAccID, ":to"=>$toAccID, ":amt" => $amt, ":type"=>$type, ":memo"=>$memo, ":total"=>($fromBalance-$amt)]);

                $stmt = $db->prepare("INSERT INTO Transactions (source, dest, bal_change, transaction_type, memo, expected total) VALUES (:to, :from, :amt, :type, :memo, :total)");
                $stmt->execute([":to"=>$toAccID, ":from"=>$fromAccID, ":amt" => $amt, ":type"=>$type, ":memo"=>$memo, ":total"=>($toBalance+$amt)]);
            }
            else {
                flash("One of the accounts doesn't have enough money for this transaciton", "Insufficient Funds!");
            }
        }
        else {
            flash("This transaction doesn't have an account number source and destination", "No to and from!");
        }
    }
    catch (PDOException $e) {
        error_log("Unknown error during transaction: " . var_export($e->errorInfo, true));
    }
}
//flash message system
function flash($msg = "", $color = "info") {
    $message = ["text" => $msg, "color" => $color];
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $message);
    } else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $message);
    }
}

function getMessages() {
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}
//end flash message system