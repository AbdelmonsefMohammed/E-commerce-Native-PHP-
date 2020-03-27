<?php

//Manage members page 
// you can add || delete|| edit members from here

session_start();
$pageTitle="members";

if(isset($_SESSION['Username'])){
    include 'init.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    // start manage page

    if($do == 'Manage'){//manage members page 
        //Select All users except Admin
        $query = '';
        if(isset($_GET['page']) && $_GET['page']=='Pending'){
            $query = 'AND Regstatus = 0';
        }

        $stmt = $con->prepare("SELECT * FROM users WHERE GroupID != 1 $query");
        $stmt->execute();

        //Assign to variables

        $rows = $stmt->fetchAll();
    ?>
        
        <h1 class="text-center">Manage Member</h1>
         <div class="container">
         <div class="table-responsive">
             <table class="table table-bordered">
                <tr>
                <th>#ID</th>
                <th>Image</th>
                <th>Username</th>
                <th>Email</th>
                <th>Full Name</th>
                <th>Registerd Date</th>
                <th>Control</th>
                <?php
                    foreach ($rows as $row) {
                        echo "<tr>";
                        echo "<td>" . $row['UserID'] . "</td>";
                        echo "<td><img style='width:50px;height:50px;' src='uploads/avatars/" . $row['avatar'] . "'></td>";
                        echo "<td>" . $row['Username'] . "</td>";
                        echo "<td>" . $row['Email'] . "</td>";
                        echo "<td>" . $row['FullName'] . "</td>";
                        echo "<td>" . $row['Date'] . "</td>";
                        echo "<td>
                                <a href='members.php?do=Edit&userid=". $row['UserID']."' class='btn btn-success'>Edit</a>
                                <a href='members.php?do=Delete&userid=". $row['UserID']."' class='btn btn-danger confirm'>Delete </a>";

                                if($row['RegStatus'] == 0){
                                    echo "<a href='members.php?do=Activate&userid=". $row['UserID']."' class='btn btn-info activate'>Activate</a>";

                                }
                            
                                echo "</td>";
                        echo "</tr>";
                    }
                
                
                ?>

                 
             </table>
         </div>
        <a href='members.php?do=Add' class="btn btn-primary"><i class="fa fa-plus"></i> Add new members</a>
        </div>
   <?php }elseif ($do == 'Add') {//Add new member page ?>

        <h1 class="text-center">Add new Member</h1>
         <div class="container">
            <form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
            
                <div class="row form-group">
                    <label class="col-sm-2 control-label">Username</label>
                    <div class="col-sm-10">
                        <input type="text" name="username"  class="form-control" required="required">
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10">
                        <input type="Password" name="password" class="password form-control" autocomplete="new-password" required="required">
                        <i class="show-pass fa fa-eye fa-2x"></i>   
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" name="email" class="form-control" required="required">
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-sm-2 control-label">Full name</label>
                    <div class="col-sm-10">
                        <input type="text" name="full" class="form-control" required="required"  >
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-2 control-label">Image</label>
                    <div class="col-sm-10">
                        <input type="file" name="avatar" class="form-control" required="required"  >
                    </div>
                </div>

                <div class="row form-group">

                    <div class="col-sm-10">
                        <input type="submit" value="Add member" class="btn btn-primary">
                    </div>
                </div>
            </form>
             
         </div>


        
    <?php }elseif($do == 'Insert'){
        //insert member page

        echo "<div class='container'>";
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            echo "<h1 class='text-center'>Insert Member</h1>";
            
            //GET variables from form 
            $user    = $_POST['username'];
            $pass    = $_POST['password'];
            $email   = $_POST['email'];
            $name    = $_POST['full'];

            $avatarName = $_FILES['avatar']['name'];
            $avatarSize = $_FILES['avatar']['size'];
            $avatarTmp = $_FILES['avatar']['tmp_name'];
            $avatarAllowedExtentions = ['jpeg','jpg','png','gif'];
            $avatarexplode = explode('.',$avatarName);
            $avatarExtention = strtolower(end($avatarexplode));

            $hashpassword = sha1($_POST['password']);
         // validate the form
         $formErrors = array();
         
         if (strlen($user)< 4 || strlen($user) >11) 
         {
             $formErrors[] = "Username can't be <strong>less that 4 characters or more than 11 characters</strong>";
         }
         if(empty($user))
         {
             $formErrors[] = "Username can't be <strong>empty</strong>";
         }
         if(empty($pass))
         {
            $formErrors[] = "password can't be <strong>empty</strong>";
         }
         if(empty($email))
         {
             $formErrors[] = "email can't be <strong>empty</strong>";
         }
         if(empty($name))
         {
             $formErrors[] = "Fullname can't be <strong>empty</strong>";
         }
         if(empty($avatarName))
         {
             $formErrors[] = "Profile picture can't be <strong>empty</strong>";
         }
         if(! empty($avatarName) && ! in_array($avatarExtention , $avatarAllowedExtentions))
         {
            $formErrors[] = "This image extention is <strong>Not Allowed</strong>";
         }
         if($avatarSize > 4194304)
         {
             $formErrors[] = "Profile picture can't be larger that <strong>4MB</strong>";
         }
         foreach ($formErrors as $error) {
             $theMsg = "<div class='alert alert-danger'>" . $error . "</div>" ;
             redirectHome($theMsg,'back');
         }
         //check if there is no errors
           
         if(empty($formErrors)){

            $avatar = time() . '_' . $avatarName;
            move_uploaded_file($avatarTmp, "uploads\avatars\\" . $avatar); 
             //check if User Exists in Database
             $check = checkItem("Username","users", $user); 

             if($check ==1){
                 $theMsg = '<div class="alert alert-danger">sorry this user does exist </div>';
                 redirectHome($theMsg,'back');
             } else
             {

                //Insert into the data base with this info
                
                $stmt = $con->prepare("INSERT INTO 
                                        users(Username, `Password`, Email, Fullname, Regstatus, `Date`, avatar)
                                        VALUES(:user, :pass, :email, :fullname , 1, now(), :avatar)");
                $stmt->execute(array(
                    'user' => $user,
                    'pass' => $hashpassword,
                    'email' => $email,
                    'fullname' => $name,
                    'avatar' => $avatar
                ));
                //echo success message
                $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " record Inserted </div>";
                redirectHome($theMsg,'back');
             }   
        
        }
        }else{
            $theMsg = "<div class='alert alert-danger'>you cant browse this page directly</div>";
            redirectHome($theMsg);
        }
         echo "</div>";


    }elseif($do == 'Edit'){//Edit page  

        // if(isset($_GET['userid']) && is_numeric($_GET['userid']) ){

        //     echo intval($_GET['userid']);

        // }else{
        //     echo 0;
        // }
        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
         // check if user exist in database 
        $stmt = $con->prepare("SELECT * FROM users WHERE UserID = ? LIMIT 1");
        $stmt->execute(array($userid));
        $row=$stmt->fetch();
        $count = $stmt->rowCount();
        echo '<div class="container">';
        if($count>0){ ?>
         
         <h1 class="text-center">Edit Member</h1>
         
            <form class="form-horizontal" action="?do=Update" method="POST">
            <input type="hidden" name="userid" value="<?php echo $userid ?>">
                <div class="row form-group">
                    <label class="col-sm-2 control-label">Username</label>
                    <div class="col-sm-10">
                        <input type="text" name="username" value="<?php echo $row['Username'] ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10">
                        <input type="hidden" name="oldpassword" value="<?php echo $row['Password'] ?>">
                        <input type="Password" name="newpassword" class="form-control" autocomplete="new-password" placeholder="*******">
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" name="email" value="<?php echo $row['Email'] ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-sm-2 control-label">Full name</label>
                    <div class="col-sm-10">
                        <input type="text" name="full" value="<?php echo $row['FullName'] ?>" class="form-control" required="required"  >
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
    }elseif($do == 'Update'){ // Update page
      
       echo "<div class='container'>";
       if($_SERVER['REQUEST_METHOD'] == 'POST'){
        echo "<h1 class='text-center'>Update Member</h1>";
           //GET variables from form 
           $id      = $_POST['userid'];
           $user    = $_POST['username'];
           $email   = $_POST['email'];
           $name    = $_POST['full'];

           //password trick
        //    $pass="";
        //    if(empty($_POST['newpassword'])){
        //        $pass = $_POST['oldpassword'];

        //    }else{
        //        $pass = sha1($_POST['newpassword']);
        //    }

        $pass=empty($_POST['newpassword']) ? $_POST['oldpassword'] : sha1($_POST['newpassword']);

        // validate the form
        $formErrors = array();
        
        if (strlen($user)< 4 || strlen($user) >11) {
            $formErrors[] = "Username can't be <strong>less that 4 characters or more than 11 characters</strong>";
        }
        if(empty($user)){
            $formErrors[] = "Username can't be <strong>empty</strong>";
        }
        if(empty($email)){
            $formErrors[] = "email can't be <strong>empty</strong>";
        }
        if(empty($name)){
            $formErrors[] = "Fullname can't be <strong>empty</strong>";
        }
        foreach ($formErrors as $error) {
            $theMsg = "<div class='alert alert-danger'>" . $error . "</div>" ;
            redirectHome($theMsg,'back');
        }
          
          //check if there is no errors
          
        if(empty($formErrors)){
            $stmt2 = $con->prepare("SELECT * FROM users WHERE Username = ? AND UserID != ?");
            $stmt2->execute(array($user,$id));
            $count = $stmt2->rowCount();
            if($count==1){
                $theMsg = "<div class='alert alert-danger'>Sorry this user exists </div>";
                redirectHome($theMsg,'back');
            }else{
            //update the data base with this info
             $stmt = $con->prepare("UPDATE users SET Username = ?, Email = ?, Fullname = ?, `Password`= ? WHERE UserID = ?");
             $stmt->execute(array($user,$email,$name,$pass,$id));

             //echo success message
             $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " record updated </div>";
             redirectHome($theMsg,'back');
            }
        }
       }else{
        $theMsg = "<div class='alert alert-danger'>you cant browse this page directly </div>";
        redirectHome($theMsg,'back');
       }
        echo "</div>";
    }elseif($do == 'Delete'){
        //Delete member page
        echo "<h1 class='text-center'>Update Member</h1>";
        echo "<div class='container'>";

        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
        // check if user exist in database 

       $check = checkItem('userid','users', $userid);

       if($check >0){
           $stmt = $con->prepare("DELETE FROM users WHERE UserID = :user");
           $stmt->bindParam(":user",$userid); 
           $stmt->execute();
           $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " record updated </div>";
           redirectHome($theMsg,'back');
       }else{
            $theMsg = "<div class='alert alert-danger'>This ID does not exist</div>";
            redirectHome($theMsg,'back');
       }
       echo "</div>";
    }elseif($do == 'Activate'){
        //Activate member page



        echo "<h1 class='text-center'>Activate Member</h1>";
        echo "<div class='container'>";

        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
        // check if user exist in database 

       $check = checkItem('userid','users', $userid);

       if($check >0){
           $stmt = $con->prepare("UPDATE users SET Regstatus = 1 WHERE UserID = ?");
           $stmt->execute(array($userid));
           $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " record Activated </div>";
           redirectHome($theMsg);
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