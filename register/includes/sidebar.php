
<!-- Blog Sidebar Widgets Column -->
<div class="col-md-4">


    <!--Login Form -->
    <div class="well">
        <h4>Login</h4>
        <form action="./login.php" method="post">
            <div class="form-group">
                <input name="username" type="text" class="form-control" placeholder="Enter Username">
            </div>

            <div class="form-group">
                <input name="password" type="password" class="form-control" placeholder="Enter password">

            </div>
            <button class="btn btn-primary" name="login" type="submit">Submit</button>
            <button class="btn btn-default" name="reset" type="reset">Reset</button>
        </form>
    </div>



    <!-- Side Widget Well -->
    <?php include "widget.php";?>

</div>
