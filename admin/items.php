<?php

ob_start();
session_start();
$pageTitle = 'Items';
if(isset($_SESSION['Username'])){
    include 'init.php';
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
    
    if($do == 'Manage'){
         $stmt = $con->prepare("SELECT
                                     items.*,
                                     categories.Name AS category_name,
                                     users.Username
                                FROM
                                     items
                                INNER JOIN
                                     categories
                                ON
                                     categories.ID = items.Cat_ID
                                INNER JOIN
                                     users
                                ON
                                     users.UserID = items.Member_ID");
         $stmt->execute();
 
         //Assign to variables
 
         $items  = $stmt->fetchAll();
     ?>
         
         <h1 class="text-center mt-3">Manage Items</h1>
          <div class="container mt-3">
          <div class="table-responsive">
              <table class="table table-bordered">
                 <tr>
                 <th>#ID</th>
                 <th>image</th>
                 <th>Name</th>
                 <th>Description</th>
                 <th>Price</th>
                 <th>Adding Date</th>
                 <th>Category</th>
                 <th>Username</th>
                 <th>Control</th>
                 <?php
                     foreach ($items as $item) {
                         echo "<tr>";
                         echo "<td>" . $item['Item_ID'] . "</td>";
                         echo "<td><img style='width:50px;height:50px;' src='uploads/posts/" . $item['Image'] . "'></td>";
                         echo "<td>" . $item['Name'] . "</td>";
                         echo "<td>" . $item['Description'] . "</td>";
                         echo "<td>" . $item['Price'] . "</td>";
                         echo "<td>" . $item['Add_Date'] . "</td>";
                         echo "<td>" . $item['category_name'] . "</td>";
                         echo "<td>" . $item['Username'] . "</td>";

                         echo "<td>
                                 <a href='items.php?do=Edit&itemid=". $item['Item_ID']."' class='btn btn-success'>Edit</a>
                                 <a href='items.php?do=Delete&itemid=". $item['Item_ID']."' class='btn btn-danger confirm'>Delete </a>";
                                 if($item['Approve']==0){
                                     echo "<a href='items.php?do=Approve&itemid=".$item['Item_ID']."' class='btn btn-info approve'><i class='fa fa-check'></i>Approve</a>";
                                 }
                                 echo "</td>";
                         echo "</tr>";
                     }
                 
                 
                 ?>
 
                  
              </table>
          </div>
         <a href='items.php?do=Add' class="btn btn-primary"><i class="fa fa-plus"></i> Add new item</a>
         </div>
    <?php
    }elseif($do =='Add')
    {?>

        <h1 class="text-center mt-3">Add new Item</h1>
        <div class="container mt-3">
           <form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
           
               <div class="row form-group">
                   <label class="col-sm-2 control-label">Name</label>
                   <div class="col-sm-10">
                       <input type="text" name="name"  class="form-control"  autocomplete="off"
                       placeholder="Name of the Item ">
                   </div>
               </div>

               <div class="row form-group">
                   <label class="col-sm-2 control-label">Description</label>
                   <div class="col-sm-10">
                       <input type="text" name="description"  class="form-control" autocomplete="off"
                       placeholder="Description of the Item ">
                   </div>
               </div>

               <div class="row form-group">
                   <label class="col-sm-2 control-label">Price</label>
                   <div class="col-sm-10">
                       <input type="text" name="price"  class="form-control" autocomplete="off"
                       placeholder="Price of the Item ">
                   </div>
               </div>

               <div class="row form-group">
                   <label class="col-sm-2 control-label">Country</label>
                   <div class="col-sm-10">
                       <input type="text" name="country"  class="form-control" autocomplete="off"
                       placeholder="country of made">
                   </div>
               </div>
               <div class="row form-group">
                    <label class="col-sm-2 control-label">Image</label>
                    <div class="col-sm-10">
                        <input type="file" name="image" class="form-control" required="required">
                    </div>
                </div>
               <div class="row form-group">
                   <label class="col-sm-2 control-label">Status</label>
                   <div class="col-sm-10">
                       <select name="status">
                       <option value="0">...</option>
                       <option value="1">New</option>
                       <option value="2">Like New</option>
                       <option value="3">Used</option>
                       <option value="4">Old</option>
                           
                       </select>
                   </div>
               </div>
               <div class="row form-group">
                   <label class="col-sm-2 control-label">Member</label>
                   <div class="col-sm-10">
                       <select name="member">
                       <option value="0">...</option>
                        <?php
                        $stmt = $con->prepare("SELECT UserID,Username FROM users WHERE GroupID != 1 ");
                        $stmt->execute();
                        $users = $stmt->fetchAll();
                        foreach($users as $user){
                            echo "<option value='". $user['UserID'] ."'>". $user['Username'] ."</option>";
                        }
                        ?>
                       </select>
                   </div>
               </div>
               <div class="row form-group">
                   <label class="col-sm-2 control-label">Category</label>
                   <div class="col-sm-10">
                       <select name="category">
                       <option value="0">...</option>
                        <?php
                        $stmt2 = $con->prepare("SELECT ID,`Name` FROM categories WHERE parent !=0");
                        $stmt2->execute();
                        $cats = $stmt2->fetchAll();
                        foreach($cats as $cat){
                            echo "<option value='". $cat['ID'] ."'>". $cat['Name'] ."</option>";
                        }
                        ?>
                       </select>
                   </div>
               </div>


               <div class="row form-group">

                   <div class="col-sm-10">
                       <input type="submit" value="Add Item" class="btn btn-primary">
                   </div>
               </div>
           </form>
            
        </div>

    <?php
        
    }elseif($do =='Insert'){//insert item page

        echo "<div class='container'>";
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            echo "<h1 class='text-center'>Insert Item</h1>";
            
            //GET variables from form 
            $name       = $_POST['name'];
            $desc       = $_POST['description'];
            $price      = $_POST['price'];
            $country    = $_POST['country'];
            $status     = $_POST['status'];
            $member     = $_POST['member'];
            $cat        = $_POST['category'];

            $imageName = $_FILES['image']['name'];
            $imageSize = $_FILES['image']['size'];
            $imageTmp = $_FILES['image']['tmp_name'];
            $imageAllowedExtentions = ['jpeg','jpg','png','gif'];
            $imageexplode = explode('.',$imageName);
            $imageExtention = strtolower(end($imageexplode));
         // validate the form
         $formErrors = array();
         
         if (empty($name)) {
             $formErrors[] = "Name can't be <strong>Empty</strong>";
         }
         if(empty($desc)){
            $formErrors[] = "Description can't be <strong>Empty</strong>";
        }
         if(empty($price)){
             $formErrors[] = "Price can't be <strong>Empty</strong>";
         }
         if(empty($country)){
            $formErrors[] = "Country can't be <strong>Empty</strong>";
        }
         if($status == 0){
            $formErrors[] = "You must choose the <strong>Status</strong>";
        }
         if($member == 0){
            $formErrors[] = "You must choose the <strong>Member</strong>";
        }
         if($cat == 0){
            $formErrors[] = "You must choose the <strong>Category</strong>";
        }
        if(empty($imageName))
        {
            $formErrors[] = "Profile picture can't be <strong>empty</strong>";
        }
        if(! empty($imageName) && ! in_array($imageExtention , $imageAllowedExtentions))
        {
           $formErrors[] = "This image extention is <strong>Not Allowed</strong>";
        }
        if($imageSize > 4194304)
        {
            $formErrors[] = "Profile picture can't be larger that <strong>4MB</strong>";
        }
         foreach ($formErrors as $error) {
             echo  "<div class='alert alert-danger'>" . $error . "</div>" ;
             
         }
           //check if there is no errors
           
         if(empty($formErrors)){
             //check if User Exists in Database
             $image = time() . '_' . $imageName;
             move_uploaded_file($imageTmp, "uploads\posts\\" . $image);
             $check = checkItem("name","items", $name); 

             if($check ==1){
                 $theMsg = '<div class="alert alert-danger">sorry this item does exist </div>';
                 redirectHome($theMsg,'back');
             } else{

                //Insert into the data base with this info
                
                $stmt = $con->prepare("INSERT INTO 
                                        items(`Name`, `Description`, Price, Country_made, `Image`, `Status`, Add_Date, Cat_ID, Member_ID)
                                        VALUES(:name, :desc, :price, :country, :image, :status, now(), :cat, :member)");
                $stmt->execute(array(
                    'name'      => $name,
                    'desc'      => $desc,
                    'price'     => $price,
                    'country'   => $country,
                    'image'     => $image,
                    'status'    => $status,
                    'cat'       => $cat,
                    'member'    => $member
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

        
    }elseif($do =='Edit'){
       $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        // check if item exist in database 
       $stmt = $con->prepare("SELECT * FROM items WHERE Item_ID = ?");
       $stmt->execute(array($itemid));
       $item=$stmt->fetch();
       $count = $stmt->rowCount();
       echo '<div class="container">';
       if($count>0){ ?>
         <h1 class="text-center mt-3">Edit <?php echo $item['Name'] ?></h1>
        <div class="container mt-3">
           <form class="form-horizontal" action="?do=Update" method="POST">
           <input type="hidden" name="itemid" value="<?php echo $itemid ?>">
               <div class="row form-group">
                   <label class="col-sm-2 control-label">Name</label>
                   <div class="col-sm-10">
                       <input type="text" name="name"  class="form-control"  autocomplete="off"
                       placeholder="Name of the Item " value="<?php echo $item['Name'] ?>">
                   </div>
               </div>

               <div class="row form-group">
                   <label class="col-sm-2 control-label">Description</label>
                   <div class="col-sm-10">
                       <input type="text" name="description"  class="form-control" autocomplete="off"
                       placeholder="Description of the Item " value="<?php echo $item['Description'] ?>">
                   </div>
               </div>

               <div class="row form-group">
                   <label class="col-sm-2 control-label">Price</label>
                   <div class="col-sm-10">
                       <input type="text" name="price"  class="form-control" autocomplete="off"
                       placeholder="Price of the Item " value="<?php echo $item['Price'] ?>">
                   </div>
               </div>

               <div class="row form-group">
                   <label class="col-sm-2 control-label">Country</label>
                   <div class="col-sm-10">
                       <input type="text" name="country"  class="form-control" autocomplete="off"
                       placeholder="country of made" value="<?php echo $item['Country_Made'] ?>">
                   </div>
               </div>

               <div class="row form-group">
                   <label class="col-sm-2 control-label">Status</label>
                   <div class="col-sm-10">
                       <select name="status">
                       <option value="1"<?php if($item['Status']==1){echo "selected";} ?>>New</option>
                       <option value="2"<?php if($item['Status']==2){echo "selected";} ?>>Like New</option>
                       <option value="3"<?php if($item['Status']==3){echo "selected";} ?>>Used</option>
                       <option value="4"<?php if($item['Status']==4){echo "selected";} ?>>Old</option>
                           
                       </select>
                   </div>
               </div>
               <div class="row form-group">
                   <label class="col-sm-2 control-label">Member</label>
                   <div class="col-sm-10">
                       <select name="member">
                        <?php
                        $stmt = $con->prepare("SELECT UserID,Username FROM users WHERE GroupID != 1 ");
                        $stmt->execute();
                        $users = $stmt->fetchAll();
                        foreach($users as $user){
                            echo "<option value='". $user['UserID'] ."'";
                            if($item['Member_ID']== $user['UserID']){echo "selected";}
                            echo">". $user['Username'] ."</option>";
                        }
                        ?>
                       </select>
                   </div>
               </div>
               <div class="row form-group">
                   <label class="col-sm-2 control-label">Category</label>
                   <div class="col-sm-10">
                       <select name="category">
                        <?php
                        $stmt2 = $con->prepare("SELECT ID,`Name` FROM categories");
                        $stmt2->execute();
                        $cats = $stmt2->fetchAll();
                        foreach($cats as $cat){
                            echo "<option value='".$cat['ID'] ."'";
                            if($item['Cat_ID'] ==  $cat['ID']){echo "selected";}
                            echo">". $cat['Name'] ."</option>";
                        }
                        ?>
                       </select>
                   </div>
               </div>


               <div class="row form-group">

                   <div class="col-sm-10">
                       <input type="submit" value="Save Item" class="btn btn-primary">
                   </div>
               </div>
           </form>
            
        </div>
        
            
   <?php }else{
       $theMsg = "<div class='alert alert-danger'>There is no ID</div>"; 
       redirectHome($theMsg,'back');
   }
   echo "</div>";
        
    }elseif($do =='Update'){
        echo "<div class='container'>";
       if($_SERVER['REQUEST_METHOD'] == 'POST'){
        echo "<h1 class='text-center'>Update item</h1>";
           //GET variables from form 
           $id      = $_POST['itemid'];
           $name    = $_POST['name'];
           $desc   = $_POST['description'];
           $price    = $_POST['price'];
           $country    = $_POST['country'];
           $status    = $_POST['status'];
           $cat    = $_POST['category'];
           $member    = $_POST['member'];
           

        // validate the form
        
        $formErrors = array();
         
        if (empty($name)) {
            $formErrors[] = "Name can't be <strong>Empty</strong>";
        }
        if(empty($desc)){
            $formErrors[] = "Description can't be <strong>Empty</strong>";
        }
        if(empty($price)){
             $formErrors[] = "Price can't be <strong>Empty</strong>";
         }
        if(empty($country)){
            $formErrors[] = "Country can't be <strong>Empty</strong>";
        }
        if($status == 0){
           $formErrors[] = "You must choose the <strong>Status</strong>";
       }
        if($member == 0){
           $formErrors[] = "You must choose the <strong>Member</strong>";
       }
        if($cat == 0){
           $formErrors[] = "You must choose the <strong>Category</strong>";
       }
        foreach ($formErrors as $error) {
            echo  "<div class='alert alert-danger'>" . $error . "</div>" ;
            
        }
          
          //check if there is no errors
          
        if(empty($formErrors)){
            //update the data base with this info
             $stmt = $con->prepare("UPDATE items SET `Name` = ?, `Description` = ?, Price = ?, Country_Made= ?,`Status`= ?, Cat_ID= ?, Member_ID= ? WHERE Item_ID = ?");
             $stmt->execute(array($name,$desc,$price,$country,$status,$cat,$member,$id));

             //echo success message
             $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " record updated </div>";
             redirectHome($theMsg,'back');
        }
       }else{
        $theMsg = "<div class='alert alert-danger'>you cant browse this page directly </div>";
        redirectHome($theMsg,'back');
       }
        echo "</div>";
    }elseif($do =='Delete'){
        //Delete item page
        echo "<h1 class='text-center'>Delete Item</h1>";
        echo "<div class='container'>";

        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        // check if item exist in database 

       $check = checkItem('Item_ID','items', $itemid);

       if($check >0){
           $stmt = $con->prepare("DELETE FROM items WHERE Item_ID = :item");
           $stmt->bindParam(":item",$itemid); 
           $stmt->execute();
           $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " record updated </div>";
           redirectHome($theMsg,'back');
       }else{
            $theMsg = "<div class='alert alert-danger'>This ID does not exist</div>";
            redirectHome($theMsg,'back');
       }
       echo "</div>";
    }elseif($do =='Approve'){
        //Approve item page



        echo "<h1 class='text-center'>Approve Item</h1>";
        echo "<div class='container'>";

        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        // check if user exist in database 

       $check = checkItem('Item_ID','items', $itemid);

       if($check >0){
           $stmt = $con->prepare("UPDATE items SET Approve = 1 WHERE Item_ID = ?");
           $stmt->execute(array($itemid));
           $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " record Approved </div>";
           redirectHome($theMsg, 'back');
       }else{
            $theMsg = "<div class='alert alert-danger'>This ID does not exist</div>";
            redirectHome($theMsg);
       }
       echo "</div>";
    }
    include $tpl .'footer.php';
}else {
    header('Location: index.php');
    exit();
}
ob_end_flush();
?>