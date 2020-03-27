<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php getTitle() ?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?php echo $css;?>bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo $css;?>font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo $css;?>jquery-ui.min.css">
        <link rel="stylesheet" href="<?php echo $css;?>jquery.selectBoxIt.css">
        <link rel="stylesheet" href="<?php echo $css;?>front.css">
    </head>
    <body>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            
                <ul class="navbar-nav ml-auto">
                    <?php
                        if(isset($_SESSION['user']))
                        {?>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle"
                                href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" v-pre>
                                <img class='border rounded-circle' src="admin/uploads/avatars/<?php echo $_SESSION['avatar'] ?>" style="width:34px;height:34px;"> <?php echo $_SESSION['user'] ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a href="profile.php" class="dropdown-item">
                                        Profile
                                    </a>
                                    <?php
                                        $userStatus = checkUserStatus($_SESSION['user']);
                                        if($userStatus == 1)
                                        {
                                            echo "<a href='#' class='dropdown-item disabled'> New Ad </a>";
                                        }else
                                        {
                                    ?>
                                        <a href="newad.php" class="dropdown-item">
                                            New Ad
                                        </a>
                                    <?php } ?>
                                    <a href="logout.php" class="dropdown-item">
                                        Logout
                                    </a>
                                </div>
                            </li>
                        <?php }else{?>
                        <li class="nav-item">
                            <a href="login.php" class="nav-link">
                            <span>Login/SingUp</span>
                            </a>
                        </li>
                        <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
        <a class="navbar-brand" href="index.php">Home</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#app-nav" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="app-nav">
            <ul class="navbar-nav ml-auto">
                <?php
                    foreach(getAllFrom('categories','ID','WHERE parent = 0') as $cat)
                    {
                        //echo "<li class='nav-item'><a class='nav-link' href='categories.php?pageid={$cat['ID']}'>{$cat['Name']}</a></li>";
                     ?>
                        <li class="nav-item dropdown">
                        <a id="navbarDropdown<?php echo $cat['ID']; ?>" class="nav-link dropdown-toggle"
                        href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false" v-pre>
                        <?php echo $cat['Name'] ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown <?php echo $cat['ID']; ?>">
                            <?php
                            foreach(getAllFrom("categories","ID","WHERE parent = {$cat['ID']}") as $c)
                            {
    
                                echo "<a href='categories.php?pageid={$c['ID']}' class='dropdown-item'>{$c['Name']}</a>";
                            }
                            
                        echo"</div>";
                        echo"</li>";
                            
                    }
                ?>
            </ul>

        </div>
        </div>
    </nav>