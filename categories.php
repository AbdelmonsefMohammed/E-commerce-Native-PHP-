<?php
    session_start();
    $pageTitle = '';
    include 'init.php'; 
?>
<div class="container">
    <h1 class="text-center">Show Category</h1>
    <div class="row">
        <?php 
            foreach(getitems('Cat_ID' , $_GET['pageid']) as $item)
            {
                echo "<div class='col-sm-6 col-md-3'>";
                    echo "<div class='card item-box'>";
                        echo "<span class='price-tag'>{$item['Price']}</span>";
                        echo "<img class='img-fluid' src='admin/uploads/posts/{$item['Image']}' class='card-img-top'>";
                        echo "<div class='card-body'>";
                            echo "<a href='items.php?itemid={$item['Item_ID']}'><h5 class='card-title'>{$item['Name']}</h5></a>";
                            echo "<p class='card-text'>{$item['Description']}</p>";
                            echo "<a href='#' class='btn btn-primary'>Add to Cart</a>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";

            }
        ?>
    </div>
</div>
<?php include $tpl .'footer.php'; ?>
