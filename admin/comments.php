<?php

//Manage Comments page 
// you can Manage || delete|| Approve members from here

session_start();
$pageTitle="Comments";

if(isset($_SESSION['Username'])){
    include 'init.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    // start manage page

    if($do == 'Manage'){//manage members page 

        $stmt = $con->prepare("SELECT
                                     comments.* , items.Name AS Item_Name , users.Username 
                               FROM 
                                     comments
                               INNER JOIN
                                     items ON items.Item_ID = comments.Item_ID
                               INNER JOIN
                                     users ON users.UserID  = comments.User_ID    
                               ");
        $stmt->execute();

        //Assign to variables

        $rows = $stmt->fetchAll();
    ?>
        
        <h1 class="text-center">Manage Comments</h1>
         <div class="container">
         <div class="table-responsive">
             <table class="table table-bordered">
                <tr>
                <th>#ID</th>
                <th>Comment</th>
                <th>Item name </th>
                <th>User Name</th>
                <th>Added Date</th>
                <th>Control</th>
                <?php
                    foreach ($rows as $row) {
                        echo "<tr>";
                        echo "<td>" . $row['c_id'] . "</td>";
                        echo "<td>" . $row['comment'] . "</td>";
                        echo "<td>" . $row['Item_Name'] . "</td>";
                        echo "<td>" . $row['Username'] . "</td>";
                        echo "<td>" . $row['comment_date'] . "</td>";
                        echo "<td>
                                <a href='comments.php?do=Edit&comid=". $row['c_id']."' class='btn btn-success'>Edit</a>
                                <a href='comments.php?do=Delete&comid=". $row['c_id']."' class='btn btn-danger confirm'>Delete </a>";

                                if($row['status'] == 0){
                                    echo "<a href='comments.php?do=Approve&comid=". $row['c_id']."' class='btn btn-info activate'>Approve</a>";

                                }
                            
                                echo "</td>";
                        echo "</tr>";
                    }
                
                
                ?>

                 
             </table>
         </div>
        </div>
   <?php }elseif($do == 'Edit'){//Edit page  

        $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0;
         // check if user exist in database 
        $stmt = $con->prepare("SELECT * FROM comments WHERE c_id = ?");
        $stmt->execute(array($comid));
        $row=$stmt->fetch();
        $count = $stmt->rowCount();
        echo '<div class="container">';
        if($count>0){ ?>
         
         <h1 class="text-center">Edit Comment</h1>
         
            <form class="form-horizontal" action="?do=Update" method="POST">
            <input type="hidden" name="comid" value="<?php echo $comid ?>">
                <div class="row form-group">
                    <label class="col-sm-2 control-label">Comment</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="comment"> <?php echo $row['comment']; ?> </textarea>
                    </div>
                </div>
                <div class="row form-group">

                    <div class="col-sm-10">
                        <input type="submit" value="save" class="btn btn-primary">
                    </div>
                </div>
            </form>
             
         
    
    
    <?php }else{
        $theMsg = "<div class='alert alert-danger'>There is no ID</div>"; 
        redirectHome($theMsg,'back');
    }
    echo "</div>";
    }elseif($do == 'Update'){ // comment page
      
       echo "<div class='container'>";
       if($_SERVER['REQUEST_METHOD'] == 'POST'){
        echo "<h1 class='text-center'>Update Member</h1>";
           //GET variables from form 
           $comid      = $_POST['comid'];
           $comment    = $_POST['comment'];

        // validate the form
        $formErrors = array();
 
          
        //update the data base with this info
            $stmt = $con->prepare("UPDATE comments SET comment = ? WHERE c_id = ?");
             $stmt->execute(array($comment,$comid));

             //echo success message
             $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " record updated </div>";
             redirectHome($theMsg,'back');
       }else{
        $theMsg = "<div class='alert alert-danger'>you cant browse this page directly </div>";
        redirectHome($theMsg,'back');
       }
        echo "</div>";
    }elseif($do == 'Delete'){
        //Delete comment page
        echo "<h1 class='text-center'>Delete Comment</h1>";
        echo "<div class='container'>";

        $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0;
        // check if user exist in database 

       $check = checkItem('c_id','comments', $comid);

       if($check >0){
           $stmt = $con->prepare("DELETE FROM comments WHERE c_id = :com");
           $stmt->bindParam(":com",$comid); 
           $stmt->execute();
           $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " record updated </div>";
           redirectHome($theMsg,'back');
       }else{
            $theMsg = "<div class='alert alert-danger'>This ID does not exist</div>";
            redirectHome($theMsg,'back');
       }
       echo "</div>";
    }elseif($do == 'Approve'){
        //Activate member page



        echo "<h1 class='text-center'>Approve Member</h1>";
        echo "<div class='container'>";

        $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0;
        // check if user exist in database 

       $check = checkItem('c_id','comments', $comid);

       if($check >0){
           $stmt = $con->prepare("UPDATE comments SET Status = 1 WHERE c_id = ?");
           $stmt->execute(array($comid));
           $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " record Approved </div>";
           redirectHome($theMsg,'back');
       }else{
            $theMsg = "<div class='alert alert-danger'>This ID does not exist</div>";
            redirectHome($theMsg);
       }
       echo "</div>";
    }


    include $tpl . 'footer.php';
}else { 
    header('Location: index.php');
    exit();
}