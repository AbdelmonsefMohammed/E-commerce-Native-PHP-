<?php
    session_start();
    $pageTitle = "Login";
    $nonavbar ="";
    if(isset($_SESSION['Username'])){
        header('Location: dashboard.php');  //redirect to page dashboard.php
    }
    include "init.php";

    //check if user coming from http post request
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $username = $_POST['user'];
        $password = $_POST['pass'];
        $hashedpass = sha1($password);
    
        // check if user exist in database  
        $stmt = $con->prepare("SELECT UserID,Username, Password FROM users WHERE Username = ? AND Password = ? AND GroupID = 1 LIMIT 1");

        $stmt->execute(array($username,$hashedpass));
        $row=$stmt->fetch();
        $count = $stmt->rowCount();
        // if count > 0 user existed in data base
        if($count > 0){
            $_SESSION['Username'] = $username;  // register session name
            $_SESSION['ID'] = $row['UserID'];   //register session ID
            header('Location: dashboard.php');  //redirect to page dashboard.php
            exit();
        }

    }
?>



    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="d-flex justify-content-center h-100">
        <div class="container">
            <div class="d-flex justify-content-center h-100 index">
                <div class="card">
                    <div class="card-header">
                        <h3>Sign In</h3>
                        <div class="d-flex justify-content-end social_icon">
                            <span><i class="fa fa-facebook-square fa-blue"></i></span>
                            <span><i class="fa fa-google-plus-square fa-orange"></i></span>
                            <span><i class="fa fa-twitter-square fa-liteblue"></i></span>
                        </div>
                    </div>
                    <div class="card-body">
                        
                            <div class="input-group form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-address-book fa-2x"></i></span>
                                </div>
                                <input class="form-control" type="text" name="user"  placeholder="username" autocomplete="off">

                            </div>
                            <div class="input-group form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-key fa-2x"></i></span>
                                </div>
                                <input class="form-control" type="password" name="pass"  placeholder="password" autocomplete="new-password">
                            </div>

                            <div class="form-group">
                                <input class="btn float-right login_btn" type="submit" value="Login" >
                            </div>
                        
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-center links">
                            Don't have an account?<a href="#">Sign Up</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>




<!-- <form class="login">
    <input class="form-control" type="text" name="user" placeholder="Username" autocomplete="off"/>
    <input class="form-control" type="password" name="pass" placeholder="Password" autocomplete="new-password">
    <input class="btn btn-primary btn-block" type="submit" value="Login">
    
</form> -->
<?php

    include $tpl . 'footer.php';
?>

