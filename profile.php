<?php
    session_start();
    $pageTitle = 'profile';
    include "init.php";
    if(isset($_SESSION['user']))
    {
    $getUser = $con->prepare("SELECT * FROM users WHERE Username = ?");
    $getUser->execute(array($_SESSION['user']));
    $info = $getUser->fetch();
    

 ?>
<div class="information">
    <div class="container">
        <div class="card block">
            <h5 class="card-header">Information</h5>
            <div class="card-body">
                <h5 class="card-title">Name :</h5>
                <p class="card-text"><?php echo $info['Username']; ?></p>
                <h5 class="card-title">Full Name :</h5>
                <p class="card-text"><?php echo $info['FullName']; ?></p>
                <h5 class="card-title">Email :</h5>
                <p class="card-text"><?php echo $info['Email']; ?></p>
                <a href="#" class="btn btn-primary">Edit Information</a>
            </div>
        </div>

        <div class="card">
            <h5 class="card-header">Ads</h5>
            <div class="card-body d-flex">
                <?php 
                if(! empty(getitems('Member_ID' , $info['UserID'],1)))
                {
                    foreach(getitems('Member_ID' , $info['UserID'] , 1) as $item)
                    {
                                        echo "<div class='col-3'>";
                        echo "<div class='card item-box'>";

                        if($item['Approve'] == 0){echo "<span class='approve-status'>Not Approved</span>";}

                            echo "<span class='price-tag'>{$item['Price']}</span>";
                            echo "<img class='img-fluid' src='img1.jpg' class='card-img-top'>";
                            echo "<div class='card-body'>";
                                echo "<a href='items.php?itemid={$item['Item_ID']}'><h5 class='card-title'>{$item['Name']}</h5></a>";
                                echo "<p class='card-text'>{$item['Description']}</p>";
                                echo "<div> {$item['Add_Date']}</div>";
                                echo "<a href='items.php?itemid={$item['Item_ID']}' class='btn btn-success'>View</a>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";    
                    }
                }else
                {
                    echo "<a href='newad.php' class='btn btn-primary'><i class='fa fa-plus'></i> Add new Ad</a>";
                }
                ?>
            </div>
        </div>

    </div>
</div>
<?php
    }
    else
        {
            header('Location: login.php');
            exit();
        }
    include $tpl . 'footer.php';
?>

