<?php
ob_start();
session_start();
$pageTitle = 'Categories';

if(isset($_SESSION['Username'])){
    include 'init.php';
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
    
    if($do == 'Manage'){
        $sort = 'ASC';
        $sort_array = array('ASC','DESC');
        if(isset($_GET['sort']) && in_array($_GET['sort'], $sort_array)){
            $sort = $_GET['sort'];
        }
        
        $stmt = $con->prepare("SELECT * FROM categories WHERE parent = 0 ORDER BY Ordering $sort");
        $stmt->execute();
        $categ = $stmt->fetchAll();?>

        <h1 class="text-center mt-3">Manage Categories</h1>
        <div class="container categories mt-3">
            <div class="card">
                <div class="card-header ">
                    Manage Categories
                    <div class="option pull-right">
                        Ordering:[
                        <a class="<?php if($sort == 'ASC'){echo 'active'; } ?>" href="?sort=ASC">ASC</a> |
                        <a class="<?php if($sort == 'DESC'){echo 'active'; } ?>" href="?sort=DESC">DESC</a>]
                        View:[
                        <span class="active" data-view="full">Full</span> |
                        <span data-view="classic">Classic</span>]
                    </div>
                </div>
                <div class="card-body">
                    <?php
                        foreach($categ as $cat){
                            echo "<div class='cat'>";
                            echo "<div class='hidden-buttons'>";
                            echo    "<a href='categories.php?do=Edit&catid=" . $cat['ID'] . "' class='btn btn-xs btn-primary'><i class='fa fa-edit'></i>Edit</a>";
                            echo    "<a href='categories.php?do=Delete&catid=" . $cat['ID'] . "' class='confirm btn btn-xs btn-danger'><i class='fa fa-edit'></i>Delete</a>";
                            echo "</div>";
                            echo "<h3>" . $cat['Name'] ."</h3>";
                            echo "<div class='full-view'>";
                                echo "<p>"; if($cat['Description'] == ''){echo 'No descreption';}else{echo $cat['Description'];} echo"</p>";
                                // echo "<span>Ordering is " . $cat['Ordering'] ."</span>";
                                if($cat['Visibility'] == 1){echo "<span class='visibility'><i class='fa fa-eye'></i> Hidden</span>";}
                                if($cat['Allow_Comment'] == 1){echo "<span class='comment'><i class='fa fa-close'></i> Comment disabled</span>";}
                                if($cat['Allow_Ads'] == 1){echo "<span class='ads'><i class='fa fa-close'></i> Ads disabled</span>";}
                                $chiledCats = getAllFrom("categories","ID","WHERE parent = {$cat['ID']}");
                                if (! empty($chiledCats))
                                {
                                echo "<h5 class='pt-3'>Sub Categories</h5>";
                                echo "<ul class='list-unstiled'>";
                                
                                foreach($chiledCats as $categ)
                                {   echo "<div class='d-flex align-items-center mt-2 row'>";
                                    echo'<div class="col-3">';
                                    echo "<li>{$categ['Name']}</li>";
                                    echo'</div>';
                                    
                                    echo    "<a href='categories.php?do=Edit&catid=" . $categ['ID'] . "' class='btn btn-xs btn-primary mr-2'><i class='fa fa-edit'></i>Edit</a>";
                                    echo    "<a href='categories.php?do=Delete&catid=" . $categ['ID'] . "' class='confirm btn btn-xs btn-danger'><i class='fa fa-edit'></i>Delete</a>";
                                    echo "</div>";
                                }
                                echo "</ul>";
                                }
                                echo "</div>"; 
                                echo "<hr>";                            
                            echo "</div>";


                        }
                    ?>
                    
                </div>
                
            </div>
            <a class="add-category btn btn-primary" href="categories.php?do=Add"><i class="fa fa-plus"></i>Add New Category</a>
        </div>



        <?php
    }elseif($do == 'Add'){?>

         <h1 class="text-center mt-3">Add new Category</h1>
         <div class="container mt-3">
            <form class="form-horizontal" action="?do=Insert" method="POST">
            
                <div class="row form-group">
                    <label class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" name="name"  class="form-control" required="required" autocomplete="off"
                        placeholder="Name of the Category ">
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10">
                        <input type="text" name="description" class="form-control" placeholder="Describe the category ">
                        
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-sm-2 control-label">Ordering</label>
                    <div class="col-sm-10">
                        <input type="text" name="ordering" class="form-control" placeholder="number to arrange the categories">
                    </div>
                </div>
                
                <div class="row form-group">
                    <label class="col-sm-2 control-label">Category type</label>
                    <div class="col-sm-10">
                        <select name="parent">
                        <option value="0">none</option>
                        <?php
                            $stmt = $con->prepare("SELECT * FROM categories WHERE parent = 0");
                            $stmt->execute();
                            $categorys = $stmt->fetchAll();
                            foreach($categorys as $cat)
                            {
                                echo "<option value='{$cat['ID']}'>{$cat['Name']}</option>";
                            }
                        ?>
                        </select>
                    </div>
                </div>



                <div class="row form-group">
                    <label class="col-sm-2 control-label">Visible</label>
                    <div class="col-sm-10">
                        <div>
                            <input id="vis-yes" type="radio" name="visibility" value="0" checked>
                            <label for="vis-yes">Yes</label>
                        </div>
                        <div>
                            <input id="vis-no" type="radio" name="visibility" value="1">
                            <label for="vis-no">No</label>
                        </div>
                        
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-2 control-label">Allow Commenting</label>
                    <div class="col-sm-10">
                        <div>
                            <input id="com-yes" type="radio" name="commenting" value="0" checked>
                            <label for="com-yes">Yes</label>
                        </div>
                        <div>
                            <input id="com-no" type="radio" name="commenting" value="1">
                            <label for="com-no">No</label>
                        </div>
                        
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-2 control-label">Allow Ads</label>
                    <div class="col-sm-10">
                        <div>
                            <input id="ads-yes" type="radio" name="ads" value="0" checked>
                            <label for="ads-yes">Yes</label>
                        </div>
                        <div>
                            <input id="ads-no" type="radio" name="ads" value="1">
                            <label for="ads-no">No</label>
                        </div>
                        
                    </div>
                </div>

                <div class="row form-group">

                    <div class="col-sm-10">
                        <input type="submit" value="Add Category" class="btn btn-primary">
                    </div>
                </div>
            </form>
             
         </div>

    <?php
    }elseif($do == 'Insert'){
        //insert Categories page

        echo "<div class='container'>";
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            echo "<h1 class='text-center'>Insert Category</h1>";
            
            //GET variables from form 
            $name        = $_POST['name'];
            $desc        = $_POST['description'];
            $order       = $_POST['ordering'];
            $parent      = $_POST['parent'];
            $visible     = $_POST['visibility'];
            $comment     = $_POST['commenting'];
            $ads         = $_POST['ads'];


             //check if Category Exists in Database
             $check = checkItem("Name","categories", $name); 

             if($check ==1){
                 $theMsg = '<div class="alert alert-danger">sorry this Category does exist </div>';
                 redirectHome($theMsg,'back');
             } else{

                //Insert into the data base with this info
                
                $stmt = $con->prepare("INSERT INTO 
                                        categories(Name, Description, Ordering, parent, Visibility, Allow_comment, Allow_Ads)
                                        VALUES(:name, :desc, :order, :parent, :visible , :comment, :ads)");
                $stmt->execute(array(
                    'name'      => $name,
                    'desc'      => $desc,
                    'order'     => $order,
                    'parent'    => $parent,
                    'visible'   => $visible,
                    'comment'   => $comment,
                    'ads'       => $ads
                ));
                //echo success message
                $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " record Inserted </div>";
                redirectHome($theMsg,'back');
        }   
        

        }else{
            $theMsg = "<div class='alert alert-danger'>you cant browse this page directly</div>";
            redirectHome($theMsg,'back');
        }
         echo "</div>";

    }elseif($do == 'Edit'){
        $catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;
        $stmt = $con->prepare("SELECT * FROM categories WHERE ID = ?");
        $stmt->execute(array($catid));
        $cat=$stmt->fetch();
        $count = $stmt->rowCount();
        echo '<div class="container">';
        if($count>0){ ?>
         <h1 class="text-center mt-3">Edit <?php echo $cat['Name'] ?></h1>
         <form class="form-horizontal mt-3" action="?do=Update" method="POST">
         <input type="hidden" name="catid" value="<?php echo $catid; ?>">
            
                <div class="row form-group">
                    <label class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" name="name"  class="form-control" required="required" placeholder="Name of the Category " value="<?php echo $cat['Name'] ?>">
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10">
                        <input type="text" name="description" class="form-control" placeholder="Describe the category " value="<?php echo $cat['Description'] ?>">
                        
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-sm-2 control-label">Ordering</label>
                    <div class="col-sm-10">
                        <input type="text" name="ordering" class="form-control" placeholder="number to arrange the categories" value="<?php echo $cat['Ordering'] ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-2 control-label">Parent</label>
                    <div class="col-sm-10">
                    <select name="parent">
                    <?php
                        $stmt = $con->prepare("SELECT * FROM categories WHERE parent = 0");
                        $stmt->execute();
                        $categs = $stmt->fetchAll();
                        foreach($categs as $categ)
                        {
                            echo "<option value='".$categ['ID']."'";
                            if($cat['parent'] == $categ['ID']){echo 'selected';}
                            echo ">{$categ['Name']}</option>";
                        }
                    ?>
                    </select>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-sm-2 control-label">Visible</label>
                    <div class="col-sm-10">
                        <div>
                            <input id="vis-yes" type="radio" name="visibility" value="0" <?php if($cat['Visibility']==0){echo 'checked';} ?>>
                            <label for="vis-yes">Yes</label>
                        </div>
                        <div>
                            <input id="vis-no" type="radio" name="visibility" value="1"<?php if($cat['Visibility']==1){echo 'checked';} ?>>
                            <label for="vis-no">No</label>
                        </div>
                        
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-2 control-label">Allow Commenting</label>
                    <div class="col-sm-10">
                        <div>
                            <input id="com-yes" type="radio" name="commenting" value="0"<?php if($cat['Allow_Comment']==0){echo 'checked';} ?>>
                            <label for="com-yes">Yes</label>
                        </div>
                        <div>
                            <input id="com-no" type="radio" name="commenting" value="1"<?php if($cat['Allow_Comment']==1){echo 'checked';} ?>>
                            <label for="com-no">No</label>
                        </div>
                        
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-2 control-label">Allow Ads</label>
                    <div class="col-sm-10">
                        <div>
                            <input id="ads-yes" type="radio" name="ads" value="0"<?php if($cat['Allow_Ads']==0){echo 'checked';} ?>>
                            <label for="ads-yes">Yes</label>
                        </div>
                        <div>
                            <input id="ads-no" type="radio" name="ads" value="1"<?php if($cat['Allow_Ads']==1){echo 'checked';} ?>>
                            <label for="ads-no">No</label>
                        </div>
                        
                    </div>
                </div>

                <div class="row form-group">

                    <div class="col-sm-10">
                        <input type="submit" value="Update Category" class="btn btn-primary">
                    </div>
                </div>
            </form> 
        
             
    <?php }else{
        $theMsg = "<div class='alert alert-danger'>There is no such ID</div>"; 
        redirectHome($theMsg,'back');
    }
    echo "</div>";

    }elseif($do == 'Update'){
         
       echo "<div class='container'>";
       if($_SERVER['REQUEST_METHOD'] == 'POST'){
        echo "<h1 class='text-center'>Update Categories</h1>";
           //GET variables from form 
           $id          = $_POST['catid'];
           $name        = $_POST['name'];
           $desc        = $_POST['description'];
           $order       = $_POST['ordering'];
           $parent       = $_POST['parent'];
           $visible     = $_POST['visibility'];
           $comment     = $_POST['commenting'];
           $ads         = $_POST['ads'];


          //check if there is no errors
          
          $stmt = $con->prepare("UPDATE categories SET `Name` = ?, `Description` = ?, Ordering = ?, parent = ?, Visibility = ?, Allow_Comment = ?, Allow_Ads = ? WHERE ID = ?");
          $stmt->execute(array($name, $desc, $order, $parent, $visible, $comment, $ads, $id));

          //echo success message
          $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " record updated </div>";
          redirectHome($theMsg,'back');

       }else{
        $theMsg = "<div class='alert alert-danger'>you cant browse this page directly </div>";
        redirectHome($theMsg,'back');
       }
        echo "</div>";

    }elseif($do == 'Delete'){
         //Delete member page
         echo "<h1 class='text-center'>Delete category</h1>";
         echo "<div class='container'>";
 
         $catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;
         // check if user exist in database 
 
        $check = checkItem('ID','categories', $catid);
 
        if($check >0){
            $stmt = $con->prepare("DELETE FROM categories WHERE ID = :id");
            $stmt->bindParam(":id",$catid); 
            $stmt->execute(); 
            $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " record updated </div>";
            redirectHome($theMsg,'back');
        }else{
             $theMsg = "<div class='alert alert-danger'>This ID does not exist</div>";
             redirectHome($theMsg,'back');
        }
        echo "</div>";
    }
    include $tpl . 'footer.php';
}else{
    header('Location: index.php');
    exit();
}

ob_end_flush();
?>