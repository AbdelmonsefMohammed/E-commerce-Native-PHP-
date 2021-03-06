<?php
    session_start();
    $pageTitle = 'Show Item';
    include "init.php";

    $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
    // check if item exist in database 
    $stmt = $con->prepare("SELECT items.* , categories.Name AS category_name, users.Username  FROM items
    INNER JOIN categories ON categories.ID = items.Cat_ID
    INNER JOIN users ON users.UserID = items.Member_ID
     WHERE Item_ID = ? AND Approve = 1");
    $stmt->execute(array($itemid));
    $count = $stmt->rowCount();
    if($count > 0)
    {

    
    $item=$stmt->fetch();

    ?>
    <div class="container">
        <h1 class="text-center"><?php echo $item['Name'] ?></h1>
        <div class="row">
            <div class="col-4">
            <img class='img-fluid w-100' src="admin/uploads/posts/<?php echo $item['Image']?>" class='card-img-top'>
            </div>
            <div class="col-8">
                <p>Description : <?php echo $item['Description'] ?></p>
                <span>Added in :<?php echo $item['Add_Date'] ?></span>
                <div>Price : <?php echo $item['Price'] ?></div>
                <div>Made in : <?php echo $item['Country_Made'] ?></div>
                <div>Status : <?php $status = $item['Status'];
                echo str_replace(array(1,2,3,4),array('new','like new','used','fucked'), $status);?>
                </div>
                <div>Category : <?php echo $item['category_name'] ?></div>
                <div>Added By : <?php echo $item['Username'] ?></div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-8">
                <div class="card card-body bg-light">
                <h3>Leave a Comment:</h3>
                <form action="<?php $_SERVER['PHP_SELF'] . '?itemid=' . $item['Item_ID'] ?>" method="POST">
                <div class="form-group">
                    <textarea class="form-control" name="comment" id="" rows="3" required></textarea>
                </div>
                <?php if(isset($_SESSION['user'])){ ?>
                <input class="btn btn-success" type="submit" value="Submit">
                <?php }else {echo "<a class='btn btn-success' href='login.php'>Submit</a>";} ?>
                </form>
                <?php
                    if($_SERVER['REQUEST_METHOD'] == 'POST')
                    {
                        $comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
                        $userid  = $_SESSION['userid'];
                        $itemid  = $item['Item_ID'];

                        if(!empty($comment))
                        {
                            $stmt = $con->prepare("INSERT INTO comments(comment, `status`, comment_date, item_id, `user_id`)
                                                        VALUES(:comment, 0, NOW(), :itemid, :userid)");
                            $stmt->execute([
                                'comment' => $comment,
                                'itemid'  => $itemid,
                                'userid'  => $userid
                            ]);
                        }
                        if($stmt)
                        {
                            echo "<div class='alert alert-success'>Success >>Your comment will be checked by the admins</div>";
                        }
                    }

                ?>
                </div>
            </div>
        </div>
        <hr>
        <?php
            
            $stmt = $con->prepare("SELECT
                                        comments.* , users.*
                                FROM 
                                        comments
                                INNER JOIN
                                        users ON users.UserID  = comments.User_ID
                                WHERE item_id = ? 
                                AND `status`  = 1
                                ORDER BY c_id DESC
                                ");
            $stmt->execute([$item['Item_ID']]);

            //Assign to variables

            $comments = $stmt->fetchAll();

        ?>
        <div class="row mt-3">
            <?php
            foreach($comments as $comment)
            {
                echo "<div class='media col-8 pb-3'>
                    <a class='pull-left' href='#'>
                        <img class='media-object' src='admin/uploads/avatars/{$comment['avatar']}' alt='' style='width:64px;height:64px;' >
                    </a>
                    <div class='media-body pl-2'>
                        <h4 class='media-heading'>{$comment['Username']}
                            <small>{$comment['comment_date']}</small>
                        </h4>
                        {$comment['comment']}
                    </div>
                </div>";
            }
            ?>
        </div>

    </div>

    <?php
    }else
    {
        echo "<div class='alert alert-danger'>sorry this item does not exist Or waiting Approval</div>";
    }

    include $tpl . 'footer.php';
?>

