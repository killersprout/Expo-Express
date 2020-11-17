<?php include('functions.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Comment and reply system in PHP</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="main.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3 post">
            <h2>Expo Express</h2>
            <p>This is my senior design project called Expo Express, Tell us what you think!.</p>
        </div>

        <!-- comments section Start-->
        <div class="col-md-6 col-md-offset-3 comments-section">
            <!-- comment form -->
            <form class="clearfix" action="index.php" method="post" id="comment_form">
                <h4>Post a comment:</h4>
                <textarea name="comment_text" id="comment_text" class="form-control" cols="30" rows="3"></textarea>
                <button class="btn btn-primary btn-sm pull-right" id="submit_comment">Submit comment</button>
            </form>

            <!-- Display total number of comments on this post  -->
            <h2><span id="comments_count">0</span> Comment(s)</h2>
            <hr>
            <!-- comments wrapper  Start-->
            <div id="comments-wrapper">
                <div class="comment clearfix">
                    <img src="profile.png" alt="" class="profile_pic">
                    <!-- Main Thread Start-->
                    <div class="comment-details">

                        <span class="comment-name">Paola Del Valle</span>
                        <span class="comment-date">Oct 16, 2020</span>
                        <p>Hi Expo Express, Its looking good!</p>
                        <a class="reply-btn" href="#" >reply</a>
                    </div>
                    <!--  Main Thread End-->
                    <div>
                        <!-- reply -->
                        <div class="comment reply clearfix">
                            <img src="profile.png" alt="" class="profile_pic">
                            <!-- reply Thread Start-->
                            <div class="comment-details">
                                <span class="comment-name">Tim</span>
                                <span class="comment-date">Oct 16, 2020</span>
                                <p>Hey, Paola Im commenting on your post!</p>
                                <a class="reply-btn" href="#">reply</a>
                            </div>
                            <!-- reply Thread End-->
                        </div>
                    </div>
                </div>
            </div>
            <!-- // comments wrapper End-->
        </div>
        <!-- // comments section End-->
    </div>
</div>

<!-- Javascripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<!-- Bootstrap Javascript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>