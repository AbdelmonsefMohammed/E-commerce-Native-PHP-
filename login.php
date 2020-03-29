<?php
    session_start();
    $pageTitle = 'Login';
    if(isset($_SESSION['user'])){
        header('Location: index.php');  //redirect to page dashboard.php
    }
    include "init.php";
   //check if user coming from http post request
   if($_SERVER['REQUEST_METHOD'] == 'POST'){
       if(isset($_POST['login']))
       {
        $user = $_POST['username'];
        $pass = $_POST['password'];
        $hashedpass = sha1($pass);

        // check if user exist in database  
        $stmt = $con->prepare("SELECT UserID, Username, `Password`,avatar FROM users WHERE Username = ? AND `Password` = ? ");

        $stmt->execute(array($user,$hashedpass));
        $get = $stmt->fetch();
        $count = $stmt->rowCount();
        // if count > 0 user existed in data base
        if($count > 0)
        {
            $_SESSION['user'] = $user;  // register session name
            $_SESSION['userid'] = $get['UserID'];
            $_SESSION['avatar'] = $get['avatar'];
            //header('Location: index.php');  //redirect to page dashboard.php
            exit();
        }else
        {
            $loginerror = '<div class="alert alert-danger">Incorrect username or password</div>';
        }
    }
    else
    {
        $formErrors =[];

        $avatarName = $_FILES['avatar']['name'];
        $avatarSize = $_FILES['avatar']['size'];
        $avatarTmp = $_FILES['avatar']['tmp_name'];
        $avatarAllowedExtentions = ['jpeg','jpg','png','gif'];
        $avatarexplode = explode('.',$avatarName);
        $avatarExtention = strtolower(end($avatarexplode));

        if(isset($_POST['username']))
        {
            $filteredUser = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            if(strlen($filteredUser) < 4 || strlen($filteredUser) > 25)
            {
                $formErrors[] = 'User name can\'t be less than 4 characters or more than 25';
            }
            // does not allow name to contain any specisal caracter or number
            if(!preg_match("/^[a-zA-Z'-]+$/", $filteredUser))
            {
                $formErrors[] = 'Name is not valid! It must not contain numbers or special characters.
                 <br> No letters or special characters allowed!';
            }
        }

        if(isset($_POST['password'])  && isset($_POST['password2']))
        {
            $pass1 = sha1($_POST['password']);
            $pass2 = sha1($_POST['password2']);
            if($pass1 !== $pass2)
            {
                $formErrors[] = 'Sorry Password doesn\'t match';
            }
        }
        if(isset($_POST['fullname']))
        {
            $filteredfullname = filter_var($_POST['fullname'], FILTER_SANITIZE_STRING);
            if(strlen($filteredfullname) < 4 || strlen($filteredfullname) > 64)
            {
                $formErrors[] = 'User Full name can\'t be empty';
            }
            // does not allow the full name to contain any specisal caracter or number but can contain spaces
            if(!preg_match("/^[a-zA-Z'--\040]+$/", $filteredfullname))
            {
                $formErrors[] = 'Full Name is not valid! It must not contain numbers or special characters.
                 <br> No letters or special characters allowed!';
            }
        }
        if(isset($_POST['email']))
        {
            $filteredEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            if(!filter_var($filteredEmail , FILTER_VALIDATE_EMAIL))
            {
                $formErrors[] = 'Email isn\'t valid';
            }
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
        if(empty($formErrors))
        {   
            $avatar = time() . '_' . $avatarName;
            move_uploaded_file($avatarTmp, "admin\uploads\avatars\\" . $avatar);
            //check if User Exists in Database
            $check = checkItem("Username","users", $_POST['username']); 

            if($check ==1)
            {
                $formErrors[] = 'Sorry this user exists';
            } else{

               //Insert into the data base with this info
               
               $stmt = $con->prepare("INSERT INTO 
                                       users(Username, `Password`, Email, Fullname, Regstatus, `Date`, avatar)
                                       VALUES(:user, :pass, :email, :fullname , 0,now(), :avatar)");
               $stmt->execute(array(
                   'user' => $_POST['username'],
                   'pass' => $pass1,
                   'email' => $_POST['email'],
                   'fullname' =>$_POST['fullname'],
                   'avatar' => $avatar,
               ));
               //echo success message
               $theMsg = "<div class='alert alert-success'>Congrats your account has been Registered </div>";

       }   
       
       }
    }
}    


?>
<div class="container login-page">
    <h1 class="text-center"><span data-class="login" class="selected">Login</span> | <span data-class="signup">SignUp</span></h1>
    <div class="row justify-content-md-center">
    <div class="col-6 margin-auto">
        <!-- START LOGIN FORM -->
    <form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="form-group">
            <label for="Inputusername">User Name</label>
            <input type="text" name="username" class="form-control" id="Inputusername">
        </div>
        <div class="form-group">
            <label for="Inputpass">Password</label>
            <input type="password" name="password" class="form-control" id="Inputpass">
        </div>
        <button type="submit" name="login" class="btn btn-primary">Login</button>
    </form>
    <?php
        if(isset($loginerror))
        {
            echo $loginerror;
        }
    ?>
        <!-- END LOGIN FORM -->
        <!-- start signup form -->
    <form class="signup" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="Inputuser">User Name</label>
            <input type="text" name="username" class="form-control" id="Inputuser">
        </div>        
        <div class="form-group">
            <label for="Inputpass1">Password</label>
            <input type="password" name="password" class="form-control" id="Inputpass1">
        </div>
        <div class="form-group">
            <label for="Inputpass2">Rewrite Password</label>
            <input type="password" name="password2" class="form-control" id="Inputpass2">
        </div>
        <div class="form-group">
            <label for="Inputfullname">Full Name</label>
            <input type="text" name="fullname" class="form-control" id="Inputfullname">
        </div>    
        <div class="form-group">
            <label for="Inputemail">Email address</label>
            <input type="email" name="email" class="form-control" id="Inputemail">
            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>
        <div class="row form-group">
            <label class="col-sm-1 control-label">Image</label>
            <div class="col-sm-11">
                <input type="file" name="avatar" class="form-control" required="required"  >
            </div>
        </div>
        <button type="submit" name="signup" class="btn btn-success">SignUp</button>
    </form>
    <div class="the-errors text-center">
        <?php
            if(!empty($formErrors))
            {
                foreach($formErrors as $error)
                {
                    echo $error . "</br>";
                }
            }
            if(isset($theMsg))
            {
                echo $theMsg;
            }
        
        ?>
    </div>
    </div>
    </div>
</div>

<?php include $tpl .'footer.php'; ?>