<?php
    session_start();
    $pageTitle = 'New Ad';
    include "init.php";
    if(isset($_SESSION['user']))
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $formErrors = [];

            $name       = filter_var($_POST['name'] , FILTER_SANITIZE_STRING);
            $desc       = filter_var($_POST['description'] , FILTER_SANITIZE_STRING);
            $price      = filter_var($_POST['price'] , FILTER_SANITIZE_NUMBER_INT);
            $country    = filter_var($_POST['country'] , FILTER_SANITIZE_STRING);
            $status     = filter_var($_POST['status'] , FILTER_SANITIZE_NUMBER_INT);
            $category   = filter_var($_POST['category'] , FILTER_SANITIZE_NUMBER_INT);
            $values = [$name , $desc , $price , $country , $status , $category];

            $imageName = $_FILES['image']['name'];
            $imageSize = $_FILES['image']['size'];
            $imageTmp = $_FILES['image']['tmp_name'];
            $imageAllowedExtentions = ['jpeg','jpg','png','gif'];
            $imageexplode = explode('.',$imageName);
            $imageExtention = strtolower(end($imageexplode));
            if(empty($name))
            {
                $formErrors[] = 'Item name can\'t be empty';
            }else
            {
                if(strlen($name) < 4 || strlen($name) > 25)
                {
                    $formErrors[] = 'Item name can\'t be less than 4 characters or more than 25';
                }
            }
            if(empty($desc))
            {
                $formErrors[] = 'Description can\'t be empty';
            }else
            {
                if(strlen($desc) > 128)
                {
                    $formErrors[] = 'description can\'t be more than 128 characters';
                }
            }
            if(empty($country))
            {
                $formErrors[] = 'country name can\'t be empty';
            }else
            {
                if(strlen($country) < 3)
                {
                    $formErrors[] = 'Country name can\'t be less than 3 characters';
                }
            }
            if(empty($price))
            {
                $formErrors[] = 'Price field can\'t be empty';
            }
            if(empty($status))
            {
                $formErrors[] = 'Status field can\'t be empty';
            }
            if(empty($category))
            {
                $formErrors[] = 'Category field can\'t be empty';
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

            if(empty($formErrors))
            {
                $image = time() . '_' . $imageName;
                move_uploaded_file($imageTmp, "admin\uploads\posts\\" . $image);
                //check if User Exists in Database
                $check = checkItem("name","items", $name); 
   
                if($check ==1){
                    $theMsg = '<div class="alert alert-danger">sorry this item does not exist </div>';
                    redirectHome($theMsg,'back');
                } 
                else
                {
   
                   //Insert into the data base with this info
                   
                   $stmt = $con->prepare("INSERT INTO 
                                           items(`Name`, `Description`, Price, Country_made,`Image`, `Status`, Add_Date, Cat_ID, Member_ID)
                                           VALUES(:name, :desc, :price, :country, :image, :status, now(), :cat, :member)");
                   $stmt->execute(array(
                       'name'      => $name,
                       'desc'      => $desc,
                       'price'     => $price,
                       'country'   => $country,
                       'image'     => $image,
                       'status'    => $status,
                       'cat'       => $category,
                       'member'    => $_SESSION['userid']
                   ));
                }   

            }
        }
    

 ?>
<div class="create-ad">
    <div class="container">
        <div class="card block">
            <h5 class="card-header">Create New Ad</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
            
                            <div class="row form-group">
                                <label class="col-sm-2 control-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="text" name="name"  class="form-control live"  autocomplete="off"
                                    placeholder="Name of the Item " data-class=".live-name">
                                </div>
                            </div>

                            <div class="row form-group">
                                <label class="col-sm-2 control-label">Description</label>
                                <div class="col-sm-10">
                                    <input type="text" name="description"  class="form-control live" autocomplete="off"
                                    placeholder="Description of the Item " data-class=".live-desc">
                                </div>
                            </div>

                            <div class="row form-group">
                                <label class="col-sm-2 control-label">Price</label>
                                <div class="col-sm-10">
                                    <input type="text" name="price"  class="form-control live" autocomplete="off"
                                    placeholder="Price of the Item " data-class=".live-price">
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
                                <label class="col-sm-2 control-label">Category</label>
                                <div class="col-sm-10">
                                    <select name="category">
                                    <option value="0">...</option>
                                        <?php
                                        $cats = getAllFrom('categories','Name');

                                        foreach($cats as $cat){
                                            echo "<option value='". $cat['ID'] ."'>". $cat['Name'] ."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>


                            <div class="row form-group">

                                <div class="col-sm-10">
                                    <input onclick="myFunction()" type="submit" value="Add Item" class="btn btn-primary">
                                </div>
                            </div>
                        </form>
                    
                    </div>
                    <div class="col-md-4">
                        <div class='card item-box live-preview'>
                            <span class='price-tag live-price'>0</span>
                            <img class='img-fluid' src='img1.jpg' class='card-img-top'>
                        <div class='card-body'>
                            <h5 class='card-title live-name'>title</h5>
                            <p class='card-text live-desc'>Lorem consectetur adipisicing elit. </p>
                            <a href='#' class='btn btn-primary'>Add to Cart</a>
                        </div>
                    </div>
                    </div>
                    
                </div>
                <?php
                    if(!empty($formErrors))
                    {
                        foreach($formErrors as $error)
                        {
                            echo "<div class='alert alert-danger'>{$error}</div>";
                        }
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