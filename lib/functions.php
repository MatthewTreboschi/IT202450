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
function new_acc(){
    if (is_logged_in()){
        $userid = get_user_id();
        //letters are in qwerty order. I wanted 1 of each and order didnt matter so i swiped my finger across each row of keys
        $strChars = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
        $accType = "Checking";
        $db = getDB();
        $entered = False;
        $stmt = $db->prepare("INSERT INTO Accounts (account_number, user_id, account_type) VALUES (:accNum, :userid, :accType)");
        while($entered){
            try {
                $accNum = "";
                for ($i = 0; $i<12; $i++){
                    $accNum += substr($strChars, rand(0,61), 1);
                }
                $stmt->execute([":accNum" => $accNum, ":userid" => $userid, ":accType"=>$accType]);
                $entered = True;
            } catch (PDOException $e) {
                $entered = False;
            }
        }
        transaction($accNum, "000000000000", 500);
        flash("Welcome! Your account has been created successfully", "success");
    }
    else {
        flash("You're not logged in!", "Whoops!");
    }
}
function transaction($to = "", $from = "", $amt = 0){
    
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