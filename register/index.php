<?php //Main home page for everyone. We can add functions to specify who is who hopefully. ?>
<?php include "includes/header.php"; //contains styling?>

    <!-- Navigation -->
<?php include "includes/navigation.php";  //includes top bar "about" "services" etc?>

    <!-- Page Content -->
    <div class="container">

    <div class="row">

        <!-- Home page -->
        <div class="col-md-8">

            <h1 class="page-header">
                <small>Welcome</small>
                <img class="img-responsive" src="../images/FiTbanner.jpg" alt="">
            </h1>

            <!-- Main body goes here -->
            <h2>Expo Express</h2>
            <p class="lead">
                by <a href="https://www.linkedin.com/in/bdupree5/">Brandon DuPree,</a>
                <a href="https://www.linkedin.com/in/edouard-gruyters-936837197/">Edouard Gruyters,</a>
                and <a href="https://www.linkedin.com/in/ritishasharma/">Ritisha Sharma</a>
            </p>
            <!--
            <p><span class="glyphicon glyphicon-time"></span>Please Sign Up or Login</p>
            <hr>  Line break to separate content or define a change
            -->

        </div>

        <!-- Blog Sidebar Widgets Column -->
        <?php include "includes/sidebar.php"; //Side bar "blog search" "blog cat" etc ?>
    </div>

    <!-- /.row -->

    <hr><!-- Line break to separate content or define a change-->


<?php

include "includes/footer.php";
?>