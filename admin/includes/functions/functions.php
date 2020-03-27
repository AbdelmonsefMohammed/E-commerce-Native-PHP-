<?php
//get title function
function getAllFrom($tableName ,$orderBy, $where = NULL){
    global $con;

    $stmt = $con->prepare("SELECT * FROM $tableName $where ORDER BY $orderBy DESC");
    $stmt->execute();
    $all = $stmt->fetchAll();
    return $all;
}

function getTitle(){
    global $pageTitle;

    if(isset($pageTitle)){
        echo $pageTitle;
    }else{
        echo 'Default';
    }

}

// Home redirect function
function redirectHome($theMsg,$url = null,$seconds = 3){
    if($url === null){
        $url = 'index.php';
        $link = 'Homepage';
    }else{
        if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== ''){
        $url = $_SERVER['HTTP_REFERER'];
        $link = 'previous page';
        }else{
            $url = 'index.php';
            $link = 'Homepage';
        }
    }

    echo $theMsg;
    echo "<div class='alert alert-info'>you will be redirected to $link in $seconds seconds</div>";
    header("refresh:$seconds;url=$url");
    exit();
}

/* function to check item in database
   $select = the item to select ex: user , item
   $from =The table to select from ex: users , items
   $value = The value of select ex: abdo , box
*/

function checkItem($select, $from, $value){

    global $con;
    $statement = $con->prepare("SELECT $select FROM $from WHERE $select=?");
    $statement->execute(array($value));
    $count = $statement->rowCount();

    return $count;

}

// count number of items

function countItems($item , $table) {
    global $con;
    $stmt2 = $con->prepare("SELECT COUNT($item) FROM $table");
    $stmt2->execute();
    return $stmt2->fetchColumn(); 

}

// Get latest records function

function getLatest($select , $table ,$order, $limit = 5){
    global $con;
    $stmt = $con->prepare("SELECT $select FROM $table ORDER BY $order DESC LIMIT $limit");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    return $rows;
}