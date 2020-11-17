<?php
include('functions.php');
?>
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

        </div>
        <div class="col-md-6 col-md-offset-3 comments-section">
            <?php if (isset($userid)): ?>
                <form class="clearfix" action="postdetails.php" method="post" id="comment_form">
                    <textarea name="comment_text" id="comment_text" class="form-control" cols="30" rows="3"></textarea>
                    <button class="btn btn-primary btn-sm pull-right" id="submit_comment">Submit comment</button>
                </form>

            <?php else: ?>
                <div class="well" style="margin-top: 20px;">
                    <!-- For now send them to home screen -->
                    <h4 class="text-center"><a href="https://www.expoexpress.online/">Sign in</a> to post a comment</h4>
                </div>

            <?php endif ?>

            <!-- Display total number of comments on this post  -->
            <!-- comments variable is defined in functions.php  -->
            <h2><span id="comments_count"><?php echo count($comments) ?></span> Comment(s)</h2>
            <hr>
            <!-- comments wrapper -->
            <div id="comments-wrapper">
                <?php if (isset($comments)): ?>
                    <!-- Display comments -->
                    <?php foreach ($comments as $comment): ?>
                        <!-- comment -->
                        <div class="comment clearfix">
                            <img src="profile.png" alt="" class="profile_pic">
                            <div class="comment-details">

                                <!-- This information comes from functions, from the postid in the sql command, since its the first post-->
                                <span class="comment-name"><?php echo getUsernameById($comment['userid']) ?></span>
                                <span class="comment-date"><?php echo date("F j, Y ", strtotime($comment["created_at"])); ?></span>
                                <p><?php echo $comment['body']; ?></p>
                                <a class="reply-btn" href="#" data-id="<?php echo $comment['id']; ?>">reply</a>
                            </div>

                            <!-- reply form -->
                            <form action="postdetails.php" class="reply_form clearfix" id="comment_reply_form_<?php echo $comment['id'] ?>" data-id="<?php echo $comment['id']; ?>">
                                <textarea class="form-control" name="reply_text" id="reply_text" cols="30" rows="2"></textarea>
                                <button class="btn btn-primary btn-xs pull-right submit-reply">Submit reply</button>
                            </form>

                            <!-- GET ALL REPLIES -->
                            <?php $replies = getRepliesByCommentId($comment['id']) ?>
                            <div class="replies_wrapper_<?php echo $comment['id']; ?>">
                                <?php if (isset($replies)): ?>
                                    <?php foreach ($replies as $reply): ?>
                                        <!-- reply -->
                                        <div class="comment reply clearfix">
                                            <img src="profile.png" alt="" class="profile_pic">
                                            <div class="comment-details">
                                                <span class="comment-name"><?php echo getUsernameById($reply['userid']) ?></span>
                                                <span class="comment-date"><?php echo date("F j, Y ", strtotime($reply["created_at"])); ?></span>
                                                <p><?php echo $reply['body']; ?></p>
                                                <a class="reply-btn" href="#">reply</a>
                                            </div>
                                        </div>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </div>
                        </div>
                        <!-- // comment -->
                    <?php endforeach ?>
                <?php else: ?>
                    <h2>Be the first to comment on this post</h2>
                <?php endif ?>
            </div><!-- comments wrapper -->
        </div><!-- // all comments -->
    </div>
</div>
<!-- Javascripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<!-- Bootstrap Javascript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script src="scripts.js"></script>
</body>
</html>