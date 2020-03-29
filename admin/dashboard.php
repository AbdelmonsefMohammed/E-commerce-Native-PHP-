<?php
    session_start();
    if(isset($_SESSION['Username'])){
        $pageTitle = "Dashboard";
        include 'init.php';
        // start dashboard page 
        $latestusers = getLatest('*' , 'users' , 'UserID',5);
        $latestitems = getLatest('*' , 'items' , 'Item_ID',5);
        // $latestcomments = getLatest('*' , 'comments' , 'C_ID',5)
        ?>
        <div class="container home-stats text-center">
        <h1>Dashboard</h1>
            <div class="row">
                <div class="col-md-3">
                   <a href="members.php"> 
                       <div class="stat st-members">
                        <i class="fa fa-users"></i>
                        <div class="info">
                            Total members
                            <span><?php echo countItems('UserID' , 'users') ?></span>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-md-3">
                <a href="members.php?do=Manage&page=Pending"> 
                    <div class="stat st-pending">
                        <i class="fa fa-user-plus"></i>
                        <div class="info">
                        Pending members
                        <span><?php echo checkItem('Regstatus', 'users', 0) ?></span>  
                        </div>

                    </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="items.php"> 
                       <div class="stat st-items">
                            <i class="fa fa-tag"></i>
                            <div class="info">
                                Total items
                                <span><?php echo countItems('Item_ID' , 'items') ?></span>
                            </div>
                       </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="comments.php"> 
                      <div class="stat st-comments">
                        <i class="fa fa-comments"></i>
                        <div class="info">
                        Total comments
                        <span><?php echo countItems('C_ID' , 'comments') ?></span>
                        </div>
                    </div>
                    </a>
                </div>
            </div>


            <div class="row latest">
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-users"></i> Latest Registered users
                            <span class="toggle-info pull-right"><i class="fa fa-plus fa-lg"></i></span>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled latest-users">
                                <?php 
                                
                                foreach($latestusers as $user){
                                    // echo '<li>' . $user['Username'];
                                    // echo '<span class="btn btn-success pull-right">';
                                    // echo '<i class="fa fa-edit"></i> <a href="members.php?do=Edit&userid=' . $user['UserID'] . '">Edit</a>';
                                    // echo '</span';
                                    // echo '</li>';
                                    echo '<li>';
                                    echo $user['Username'];
                                    echo '<a href="members.php?do=Edit&userid=' . $user['UserID'] . '">';
                                        echo '<span class="btn btn-success pull-right">';
                                        echo '<i class="fa fa-edit"></i> Edit';
                                        echo '</a>';
                                        if($user['RegStatus'] == 0){
                                            echo "<a href='members.php?do=Activate&userid=". $user['UserID']."' class='btn btn-info activate pull-right'>Activate</a>";
        
                                        }
                                        echo '</span>';
                                    
                                    echo '</li>';
                                }
                                 ?>
                             </ul>
                        </div>
                    </div>
                    
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-users"></i> Latest Items
                            <span class="toggle-info pull-right"><i class="fa fa-plus fa-lg"></i></span>
                        </div>
                        <div class="card-body">
                        <ul class="list-unstyled latest-users">
                                <?php 
                                if(! empty($latestitems)){
                                foreach($latestitems as $item){
                                    echo '<li>';
                                    echo $item['Name'];
                                    echo '<a href="items.php?do=Edit&itemid=' . $item['Item_ID'] . '">';
                                        echo '<span class="btn btn-success pull-right">';
                                        echo '<i class="fa fa-edit"></i> Edit</a>';
                                        if($item['Approve'] == 0){
                                            echo "<a href='items.php?do=Approve&itemid=". $item['Item_ID']."' class='btn btn-info activate pull-right'>Approve</a>";
        
                                        }
                                        echo '</span>';
                                    echo '</a>';
                                    echo '</li>';
                                }
                                }else{
                                    echo "There is no records to show";
                                }
                                 ?>
                             </ul>
                        </div>
                    </div>
                    
                </div>
                
            </div>
            <div class="row latest">
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-comments-o"></i> Latest Comments
                            <span class="toggle-info pull-right"><i class="fa fa-plus fa-lg"></i></span>
                        </div>
                        <div class="card-body">
                                <?php 
                                $stmt = $con->prepare("SELECT comments.*, users.Username
                                                       FROM
                                                              comments
                                                       INNER JOIN
                                                             users
                                                        ON
                                                             users.UserID = comments.User_ID
                                                        ORDER BY c_id DESC
                                                        LIMIT 5  ");
                                                             
                                $stmt->execute();
                                $comments = $stmt->fetchAll();
                                
                                foreach($comments as $comment){
                                    echo '<div class="comment-box">';
                                    echo '<div class="row">';
                                        echo '<span class="member-n">'.$comment["Username"].'</span>';
                                        echo '<p class="member-c">'.$comment["comment"].'</p>';
                                    echo '</div>';
                                    echo '<div class="row">';
                                        echo '<a href="comments.php?do=Edit&comid=' . $comment['c_id'] . '">';
                                        echo '<span class="btn btn-success pull-right">';
                                        echo '<i class="fa fa-edit"></i> Edit</a>';
                                        if($comment['status'] == 0){
                                            echo "<a href='comments.php?do=Approve&comid=". $comment['c_id']."' class='btn btn-info activate pull-right'>Approve</a>";
        
                                        }
                                        echo '</span>';
                                        echo '</div>';
                                    echo '</div>';
                                }
                                 ?>
                        </div>
                    </div>
                    
                </div>
                
            </div>
            
        </div>

         
    <?php
    //end dashboard page
        include $tpl . 'footer.php';
    }else { 
        header('Location: index.php');
        exit();
    }