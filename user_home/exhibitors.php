<?php
ob_start();
session_start();
include "includes/user_header.php";
//include "functions.php";
?>

<div id="wrapper">

    <!-- Navigation -->
    <?php include "includes/user_navigation.php"; ?>

    <div id="page-wrapper">

        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="row">
                <div class = "col-lg-12">

                    <h1 class="page-header">
                        Welcome to the Exhibits Page!
                    </h1>

                    <?php
                    //Get can use variables from the url
                    if(isset($_GET['source'])){
                        $source = $_GET['source'];
                    } else {
                        $source = "";
                    }
                    //Had an issue where I was putting source to add_post.php. I forgot the 's' at the end of post >:(
                    switch($source){
                        //Cases are the name of the scripts and in the url
                        //Links to pages on the side bar
                        /*
                        case 'add_user';
                            include "includes/add_user.php";
                            break;

                            //Doesnt matter for now
                        case 'view_temp_exhibit';
                            include "includes/view_temp_exhibit.php";
                            break;
                        */
                        default:
                            include "includes/view_exhibitors.php";
                            break;
                    }
                    ?>
                    

                </div>

            </div>
        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->

</div>
<!-- /#page-wrapper -->
<?php include "includes/user_footer.php"; ?>
