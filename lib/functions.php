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
function set_is_admin() {
    $query = "SELECT Roles.name FROM Users JOIN UserRoles ON Users.id = UserRoles.user_id JOIN Roles ON UserRoles.role_id = Roles.id WHERE user_id = :uid";
    $db = getDB();
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":uid" => get_user_id()]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r) {
            $_SESSION["admin"] = true;
        }
        else {
            $_SESSION["admin"] = false;
        }
    } catch (PDOException $e) {
        error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
    }
}
function new_acc($deposit = 5, $accType = "Checking"){
    if (is_logged_in()){
        $userid = get_user_id();
        //letters are in qwerty order. I wanted 1 of each and order didnt matter so i swiped my finger across each row of keys
        $strChars = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
        $db = getDB();
        $entered = False;
        if ($accType == "Checking") {
            $query = "INSERT INTO Accounts (account_number, user_id, account_type) VALUES (:accNum, :userid, :accType)";
        }
        else {
            $query = "INSERT INTO Accounts (account_number, user_id, account_type, apy, last_apy) VALUES (:accNum, :userid, :accType, 5.00, CURRENT_TIMESTAMP)";
        }
        $stmt = $db->prepare($query);
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
        transaction_prep($accNum, "000000000000", $deposit, "deposit", "Initial deposit");
        die(header("Location: accounts.php?newacc=".$accNum));
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
    $id = -1;
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
//the "to" account is always the account gaining money, the "from" is losing it
function transaction_prep($to = "", $from = "", $amt = 0, $type = "deposit", $memo = "No memo"){
    try {
        if($to && $from){
            $toBalance = get_balance($to);
            $fromBalance = get_balance($from);
            $toAccID = get_account_id($to);
            $fromAccID = get_account_id($from);
            if($amt<0){
                flash("Negative amount for transaction", "Negative amount");
            }
            else if ( !($toBalance<0 && $toBalance+$amt>0) && ( !($fromBalance-$amt<0) || $from == "000000000000" || $type == "Interest") ){
                
                transaction($toAccID, $fromAccID, $amt, $type, $memo);

                if ($toBalance<0 && $toBalance+$amt=0) {
                    close($toAccID);
                }

                if($memo != "Initial deposit") {
                    flash("Successful transaction with memo: " . $memo, "Success");
                }
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
function transaction($toAccID = "", $fromAccID = "", $amt = 0, $type = "deposit", $memo = "No memo", $fromBalance = 0, $toBalance = 0) {
    $db = getDB();

    $stmt = $db->prepare("INSERT INTO Transactions (source, dest, bal_change, transaction_type, memo, expected_total) VALUES (:from, :to, :amt, :type, :memo, :total)");
    $stmt->execute([":from"=>$fromAccID, ":to"=>$toAccID, ":amt" => $amt*-1, ":type"=>$type, ":memo"=>$memo, ":total"=>($fromBalance-$amt)]);

    $stmt = $db->prepare("INSERT INTO Transactions (source, dest, bal_change, transaction_type, memo, expected_total) VALUES (:to, :from, :amt, :type, :memo, :total)");
    $stmt->execute([":to"=>$toAccID, ":from"=>$fromAccID, ":amt" => $amt, ":type"=>$type, ":memo"=>$memo, ":total"=>($toBalance+$amt)]);

    $stmt = $db->prepare("UPDATE Accounts SET balance = (SELECT IFNULL(SUM(bal_change), 0) FROM Transactions WHERE source = :toAccID) WHERE id = :toAccID");
    $stmt->execute(["toAccID"=>$toAccID]);

    $stmt = $db->prepare("UPDATE Accounts SET balance = (SELECT IFNULL(SUM(bal_change), 0) FROM Transactions WHERE source = :fromAccID) WHERE id = :fromAccID");
    $stmt->execute(["fromAccID"=>$fromAccID]);
}
function get_users($first="", $last="", $page=1) {
    $users = [];
    $params = [];
    $offset = ($page-1)*10;
    if (is_logged_in()){
        $query = "SELECT * FROM Users WHERE TRUE";
        if ($first) {
            $query .= " AND first_name = :first";
            $params[":first"] = $first;
        }
        if ($last) {
            $query .= " AND last_name = :last";
            $params[":last"] = $last;
        }
        $query .= " ORDER BY created desc LIMIT :offset , 10";
        $params[":offset"] = $offset;
        $db = getDB();
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $stmt = $db->prepare($query);
        try {
            
            $stmt->execute($params);
            $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($r) {
                $users = $r;
            }
        } catch (PDOException $e) {
            flash($query);
            error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
        }
    }
    return $users;
}
function count_users($first="", $last="") {
    $count = 0;
    $params = [];
    if (is_logged_in()){
        $query = "SELECT count(*) as n FROM Users WHERE TRUE";
        if ($first) {
            $query .= " AND first_name = :first";
            $params[":first"] = $first;
        }
        if ($last) {
            $query .= " AND last_name = :last";
            $params[":last"] = $last;
        }
        $db = getDB();
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $stmt = $db->prepare($query);
        try {
            
            $stmt->execute($params);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                $count = (int)se($r, "n", 0, false);;
            }
        } catch (PDOException $e) {
            flash($query);
            error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
        }
    }
    return $count;
}
function get_accounts($limit = false, $loans = true, $page = 1, $search = "", $all=false){
    $accounts = [];
    $params = [];
    $offset = ($page-1)*5;
    if (is_logged_in()){
        $db = getDB();
        $query = "SELECT * FROM Accounts WHERE true";
        if (!$all or !$_SESSION["admin"]) {
            $query .= " AND user_id = :uid AND closed = false";
            $params[":uid"] = get_user_id();
        }
        if ($search) {
            $query .= " AND account_number LIKE :search";
            $params[":search"] = $search;
        }
        if (!$loans) {
            $query .= " AND NOT account_type = 'loan'";
        }
        if ($limit){
            $query .= " LIMIT :offset, 5";
            $params[":offset"] = $offset;
        }
        else {
            $query .= " AND frozen = false";
        }
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $stmt = $db->prepare($query);
        try {
            $stmt->execute($params);
            $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($r) {
                $accounts = $r;
            }
        } catch (PDOException $e) {
            error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
        }
    }
    return $accounts;
}
function count_accounts($loans = true, $search = "", $all=false){
    $count = 0;
    $params = [];
    if (is_logged_in()){
        $db = getDB();
        $query = "SELECT COUNT(*) as n FROM Accounts WHERE true";
        if (!$all or !$_SESSION["admin"]) {
            $query .= " AND user_id = :uid AND closed = false";
            $params[":uid"] = get_user_id();
        }
        if ($search) {
            $query .= " AND account_number LIKE :search";
            $params[":search"] = $search;
        }
        if (!$loans) {
            $query .= " AND NOT account_type = 'loan'";
        }
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $stmt = $db->prepare($query);
        try {
            $stmt->execute($params);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                $count = (int)se($r, "n", 0, false);
            }
        } catch (PDOException $e) {
            error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
        }
    }
    return $count;
}
function get_transactions($accNum = "", $start="", $end="", $type="", $page=1){
    $transactions = [];
    $params = [];
    $accID = get_account_id($accNum);
    $offset = ($page-1)*10;
    $params[":accID"] = $accID;
    if (is_logged_in()){
        $query = "SELECT * FROM Transactions WHERE source = :accID";
        if ($start) {
            $query .= " AND created > :start";
            $params[":start"] = $start;
        }
        if ($end) {
            $query .= " AND created < :end";
            $params[":end"] = $end;
        }
        if ($type) {
            $query .= " AND transaction_type = :type";
            $params[":type"] = $type;
        }
        $query .= " ORDER BY created desc LIMIT :offset , 10";
        $params[":offset"] = $offset;
        $db = getDB();
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $stmt = $db->prepare($query);
        try {
            
            $stmt->execute($params);
            $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($r) {
                $transactions = $r;
            }
        } catch (PDOException $e) {
            flash($query);
            error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
        }
    }
    return $transactions;
}
function count_transactions($accNum = "", $start="", $end="", $type=""){
    $count = 0;
    $params = [];
    $accID = get_account_id($accNum);
    $params[":accID"] = $accID;
    if (is_logged_in()){
        $query = "SELECT COUNT(*) as n FROM Transactions WHERE source = :accID";
        if ($start) {
            $query .= " AND created > :start";
            $params[":start"] = $start;
        }
        if ($end) {
            $query .= " AND created < :end";
            $params[":end"] = $end;
        }
        if ($type) {
            $query .= " AND transaction_type = :type";
            $params[":type"] = $type;
        }
        $db = getDB();
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $stmt = $db->prepare($query);
        try {
            
            $stmt->execute($params);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                $count = (int)se($r, "n", 0, false);
            }
        } catch (PDOException $e) {
            flash($query);
            error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
        }
    }
    return $count;
}
function get_account_info($accNum = ""){
    $account = [];
    if (is_logged_in()){
        $user_id = get_user_id();
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM Accounts WHERE account_number = :accNum AND user_id = :user_id");
        try {
            $stmt->execute([":accNum" => $accNum, ":user_id" => $user_id]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                $account = $r;
            }
            else{
                $account["user_id"] = -99;
            }
        } catch (PDOException $e) {
            error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
        }
    }
    return $account;

}
function pagination_filter($newPage) {
    $_GET["page"] = $newPage;
    //php.net/manual/en/function.http-build-query.php
    return se(http_build_query($_GET));
}
function get_first_name() {
    $first_name = "";
    if (is_logged_in()){
        $id = get_user_id();
        $db = getDB();
        $stmt = $db->prepare("SELECT first_name FROM Users WHERE id = :id");
        try {
            $stmt->execute([":id" => $id]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                if (is_string($r["first_name"])){
                    $first_name = se($r, "first_name", 0, false);
                }
                else {
                    $first_name = "";
                }
            }
        } catch (PDOException $e) {
            error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
        }
    }
    return $first_name;
}
function get_last_name() {
    $last_name = "";
    if (is_logged_in()){
        $id = get_user_id();
        $db = getDB();
        $stmt = $db->prepare("SELECT last_name FROM Users WHERE id = :id");
        try {
            $stmt->execute([":id" => $id]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                if (is_string($r["last_name"])){
                    $last_name = se($r, "last_name", 0, false);
                }
                else {
                    $last_name = "";
                }
            }
        } catch (PDOException $e) {
            error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
        }
    }
    return $last_name;
}
function get_account_num($last_name="", $shortAccNum="") {
    $account_number = "";
    $accNumPattern = "%".$shortAccNum;
    if (is_logged_in()){
        $query = "SELECT account_number FROM Accounts JOIN Users ON Accounts.user_id = Users.id ";
        $query .= "WHERE last_name = :last_name AND account_number LIKE :accNumPattern AND closed = false AND frozen = false";
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute([":last_name" => $last_name, ":accNumPattern" => $accNumPattern]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                if (is_string($r["account_number"])){
                    $account_number = se($r, "account_number", 0, false);
                }
                else {
                    $account_number = "";
                }
            }
        } catch (PDOException $e) {
            error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
        }
    }
    return $account_number;
}
function interest() {
    if (is_logged_in()){
        $id = get_user_id();
        $query = "SELECT account_number, account_type, balance, apy, DATEDIFF(CURRENT_TIMESTAMP, last_apy) as dif FROM Accounts WHERE last_apy<DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 MONTH) AND user_id=:id";
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute([":id" => $id]);
            $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($r as $account):
                $accNum = $account["account_number"];
                $balance = $account["balance"];
                $apy = $account["apy"]/100;
                $t = $account["dif"]/365; // amount of time its been in years
                $interest = $balance(1+$apy/12)**(12*$t) - $balance;
                $interest = round($interest);
                if ($account["account_type"] == "Savings"){
                    transaction($accNum, "000000000000", $interest, "Interest", "Interest");
                }
                else {
                    transaction("000000000000", $accNum, $interest, "Interest", "Interest");
                }
            endforeach;
            $query = "UPDATE Accounts SET last_apy = CURRENT_TIMESTAMP WHERE last_apy<DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 MONTH) AND user_id=:id";
            $stmt = $db->prepare($query);
            $stmt->execute([":id" => $id]);
        } catch (PDOException $e) {
            error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
        }
    }
}
function close($accNum = "") {
    $query = "UPDATE Accounts SET closed = TRUE WHERE account_number = :accNum";
    $db = getDB();
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([":accNum" => $accNum]);
    } catch (PDOException $e) {
        error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
    }
}
function new_loan($amt = 500, $accTo) {
    if (is_logged_in()){
        $userid = get_user_id();
        //letters are in qwerty order. I wanted 1 of each and order didnt matter so i swiped my finger across each row of keys
        $strChars = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
        $db = getDB();
        $entered = false;
        $query = "INSERT INTO Accounts (account_number, user_id, account_type, apy, last_apy) VALUES (:accNum, :userid, 'Loan', 5.00, CURRENT_TIMESTAMP)";
        $stmt = $db->prepare($query);
        while(!$entered){
            try {
                $accNum = "";
                for ($i = 0; $i<12; $i++){
                    $accNum .= substr($strChars, rand(0,61), 1);
                }
                $stmt->execute([":accNum" => $accNum, ":userid" => $userid]);
                $entered = True;
            } catch (PDOException $e) {
                $entered = False;
            }
        }
        $toAccID = get_account_id($accTo);
        $fromAccID = get_account_id($accNum);
        $toBalance = get_balance($accTo);
        try {
            transaction($toAccID, $fromAccID, $amt, "Internal transfer", "Initial loan", 0, $toBalance);
        }
        catch (PDOException $e) {
            error_log("Unknown error during transaction: " . var_export($e->errorInfo, true));
        }
        die(header("Location: accounts.php?newacc=".$accNum));
    }
    else {
        flash("You're not logged in!", "Whoops!");
    }
}
function toggle_privacy() {
    $query = "UPDATE Users SET priv = if(priv,0,1) WHERE id = :id";
    $id = get_user_id();
    $db = getDB();
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([":id" => $id]);
    } catch (PDOException $e) {
        error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
    }
}
function get_priv() {
    $priv = "";
    if (is_logged_in()){
        $id = get_user_id();
        $db = getDB();
        $stmt = $db->prepare("SELECT priv FROM Users WHERE id = :id");
        try {
            $stmt->execute([":id" => $id]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                $priv = se($r, "priv", 0, false);
            }
        } catch (PDOException $e) {
            error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
        }
    }
    return $priv;
}
function toggle_freeze($accNum) {
    $query = "UPDATE Accounts SET frozen = if(frozen,0,1) WHERE account_number = :accNum";
    $db = getDB();
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([":accNum" => $accNum]);
    } catch (PDOException $e) {
        error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
    }
}
function toggle_active($id) {
    $query = "UPDATE Users SET is_active = if(is_active,0,1) WHERE id = :id";
    $db = getDB();
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([":id" => $id]);
    } catch (PDOException $e) {
        error_log("Unknown error during balance check: " . var_export($e->errorInfo, true));
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