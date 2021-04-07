<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
//Get logged in user
session_start();
include "../db/config.php";

//if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../user_home");
    exit;
}
//Check already saved data & load it
$user = $_SESSION["username"];
$id = $_SESSION["user_id"];
$event = $_SESSION["event_name"];
$user_type = $_SESSION["user_role"];
$division = $_SESSION["division_name"];

//Get Id of the person in the exhibitor table
//Gives error when hitting (view finalized page)
if(isset($_GET['p_id'])){
    $the_post_id = $_GET['p_id'];
}else{
    $the_post_id = $id;
}


// Check connection
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

//Get template layout name
$template_name = "";

$sql = "SELECT template_layout FROM users WHERE user_role='Organizer' AND event_name='$event'";
$result = mysqli_query($link, $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $template_name = $row['template_layout'];
} else {
    echo "0 results";
}

//Get exhibit category
$sql = "SELECT division_name FROM users WHERE user_id = '$the_post_id'";
$result = mysqli_query($link, $sql);
$row = $result->fetch_assoc();
$exhibit_div = $row["division_name"];

//Sql statement to display the user when they click on exhibit
$sql = "SELECT * FROM exhibits WHERE exhibit_id = '$the_post_id'";
$title = "";
$str = "";
$visits = 0;

//Get row
if ($result = mysqli_query($link, $sql)) {
    //fetch associative array
    while ($row = mysqli_fetch_row($result)) {
        //Check if draft row already exists
        if($row[0] == 1) {
            $row_found = true;
            $title = $row[3];
            $title = str_replace('"', '\"', $title);

            $file_path = getcwd() . '/exhibit_texts/' . $the_post_id . "_published.txt";
            if (!file_exists($file_path)) {
                echo 'file doesn\'t exists';
                return;
            }

            //Retrieve data from draft.txt file
            $str = file_get_contents($file_path);
            $str = str_replace("\n", "\\n", $str);
            $str = str_replace("\r", "\\r", $str);
            $str = str_replace('"', '\"', $str);

            $visits = $row[5];
            break;
        }
    }
}

//Update visit count depending on user type
if ($user_type === 'Judge' || $user_type === 'Attendee') {
    $visits++;
}

$sql = "UPDATE exhibits SET visits = '$visits' WHERE exhibit_id = '$the_post_id'";
$result = mysqli_query($link, $sql);
if(!$result) {
    echo "Didn't update";
}

if(isset($_POST['back'])) {
    header("location: ../user_home/exhibitors.php");
}

//TODO:
//WORD + PRESENTATION PREVIEW
//YOUTUBE PREVIEW SIZE FIX
//IMAGE SCALE FIX

?>

<!DOCTYPE html>
<html>
<style type="text/css">
    <?php echo file_get_contents('comments.css'); ?>
</style>
<!-- COMMENTS -->
<div class="container" id="comments">

    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">


            <!-- Comments -->
            <?php
            if(isset($_POST['create_comment'])){
                $the_post_ = $_GET['p_id'];  //received from URL
                $comment_author = $_SESSION['username'];
                $comment_content = $_POST['comment_content'];

                //query to insert comments
                $query = "INSERT INTO comments (comment_post_id, comment_author,  comment_context, comment_status,comment_date) ";
                $query .= "VALUES ($the_post_id ,'{$comment_author}', '{$comment_content }', 'approved',now())"; //automatic approval?

                //$query = "INSERT INTO comments (comment_post_id,comment_author, comment_email, comment_content,comment_status, comment_date,)";
                //$query .= "VAlUES ($the_post_id,'{$comment_author}','{$comment_email}', '{$comment_content}','unapproved', now())";

                $create_comment_query = mysqli_query($link,$query);
                if(!$create_comment_query){
                    die("QUERY FAILED" . mysqli_error($link));
                }

                //Also, spacing matters in queries
                $query = "UPDATE posts SET post_comment_count = post_comment_count + 1 "; //Update the comment count for the posts
                $query .= "WHERE posts_id = $the_post_id ";

                $update_comment_count = mysqli_query($link,$query);

            }

            ?>

            <!-- Comments Form -->
            <div class="well">
                <h4 style="margin-left: 10px">Leave a Comment:</h4>
                <form action="" method="post" role="form">

                    <div class="form-group" id="comment_box">
                        <!-- new -->
                        <div class="form-group" id="author">
                            <label for="Comment">Writing comment as: </label><b><?php echo $_SESSION['username']; ?></b>
                        </div>
                        <textarea id="comment_text_area" placeholder="Enter a Comment..." name="comment_content" class="form-control" rows="3"></textarea>
                    </div>

                    <button type="submit" id="submit_comment" name="create_comment" class="button">Submit</button>
                </form>
            </div>

            <hr> <!-- Line break -->

            <!-- Posted Comments -->
            <?php

            //gets the comments and after approval posts them, its posting twice. TODO fix that its posting twice
            $query = "SELECT * FROM comments WHERE comment_post_id = {$the_post_id} ";
            $query .= "AND comment_status = 'approved' ";
            $query .= "ORDER BY comment_id DESC "; //ordered by descending
            $select_comment_query = mysqli_query($link, $query);
            if(!$select_comment_query) {
                die('Query Failed' . mysqli_error($link));
            }
            while ($row = mysqli_fetch_array($select_comment_query)) {
                $comment_date = $row['comment_date'];
                $comment_content = $row['comment_context'];
                $comment_author = $row['comment_author'];

                ?>
                <!-- Comments -->
                <div class="media" id="view_comment">
                    <!--<a class="pull-left" href="#">
                        <img class="media-object" src="http://placehold.it/64x64" alt="">
                    </a>-->
                    <div class="media-body">
                        <h4 class="media-heading"><?php echo $comment_author ;?>
                            <small><?php echo $comment_date;?></small>
                        </h4>
                        <?php echo $comment_content;?>
                    </div>
                </div>

            <?php }
            ?>

        </div>

        <!-- Blog Sidebar Widgets Column -->

    </div>
    <!-- /.row -->

<style>
    .wrapper {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        grid-gap: 10px;
        grid-auto-rows: minmax(100px, 300px);
        font-family: Arial, Helvetica, sans-serif;
    }

    .wrapper > div {
        padding: 10px;
    }

    .wrapper > div > img {
        object-fit: contain;
        width: 100%;
        height: 100%;
    }

    .wrapper > div > iframe {
        object-fit: contain;
        width: 100%;
        height: 100%;
    }

    .wrapper > div > object {
        object-fit: contain;
        width: 100%;
        height: 100%;
    }

    .title {
        text-align: center;
        color: white;
        font-size: 40px; !important;
        font-family: Arial, Helvetica, sans-serif;
    }

    .button {
        background-color: white;
        color: black;
        border: 2px solid #e7e7e7;
        padding: 16px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        transition-duration: 0.4s;
        cursor: pointer;
    }

    .comments {
        display: inline-table;
    }

    a {
        color: white;
    }
</style>

<head>
    <title>Template Preview</title>
</head>

<body onload = "GenerateFromFile(document, '/exhibit-page/templates/' + '<?php echo $template_name; ?>' + '.txt')" style="background-color: #232323; color: white;">

</body>

</html>

<script>
    function visitVideoChat(){
        //WE GOTTA ADD THE VIDEO CALL ID TO THE LINK
        num = '<?php echo $the_post_id; ?>';
        window.location= 'https://www.expoexpress.online/user_home/video.php?p_id='+num;
    }

    function loadFile(filePath) {
        var result = null;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", filePath, false);
        xmlhttp.send();
        if (xmlhttp.status == 200) {
            result = xmlhttp.responseText;
        }
        return result;
    }

    function GenerateFromFile(document, template) {
        //Get data from server
        var data = "<?php echo $str ?>";
        data.replace('\"', '"');
        data = data.split('{[&;}]');

        var title = "<?php echo $title; ?>";
        title.replace('\"', '"');
        var parent_div = document.createElement("DIV");
        parent_div.setAttribute("class", "title");
        var title_elm = document.createElement("H1");
        title_elm.innerText = title;
        parent_div.appendChild(title_elm);
        document.body.appendChild(parent_div);

        //Get template file from the server
        var file = loadFile(template);
        file = file.split('\n');
        //Remove any newline or carriage return characters
        for (var i = 0; i < file.length; i++) {
            file[i] = file[i].replace(/^\s+|\s+$/g, '');
        }
        var i = 0;

        //Set max width
        var width_height = file[i++];
        var max_width = width_height[0];

        var max_y = 0;
        let layout = [];
        var num_of_types = file[i++];
        for (var j = 0; j < num_of_types; j++) {
            var pos_x = parseInt(file[i++]);
            var pos_y = parseInt(file[i++]);
            var size_x = parseInt(file[i++]);
            var size_y = parseInt(file[i++]);
            var type = file[i++];

            //Get heights y value with height
            var temp_y = pos_y + size_y;
            if (temp_y > max_y) {
                max_y = temp_y;
            }

            //Find the index of the first occurance of the type
            var content = "";
            var index = data.findIndex(element => element === type);
            if(index >= 0) {
                content = data[index + 1];
                //Remove item from the list so other items can be found
                data[index] = "";
                data[index + 1] = "";
            }

            var item = {pos_x: pos_x, pos_y: pos_y, size_x: size_x, size_y: size_y, type: type, content: content};
            layout.push(item);
            //window.alert(item.pos_x + ", " + item.pos_y + ", " + item.size_x + ", " + item.size_y + ", " + item.type);
        }

        GenerateLayout(layout, max_y, max_width);

        //Add publish and back button
        var form = document.createElement("FORM");
        form.setAttribute("type", "submit");
        form.setAttribute("method", "post");
        var comments = document.getElementById("comments");
        comments.parentElement.insertBefore(form, comments.parentElement.firstChild);
        var back = document.createElement("BUTTON");
        back.innerText = "Back";
        back.setAttribute("name", "back");
        back.setAttribute("class", "button");
        back.style.marginLeft = "10px";
        form.appendChild(back);
    }

    function GenerateLayout(layout, comment_y, max_width) {
        //Check if div exists
        var parent_div = document.getElementById("parent");
        if (parent_div != null) {
            parent_div.remove();
            //window.alert("Removing parent");
        }

        parent_div = document.createElement("DIV");
        parent_div.setAttribute("id", "parent");
        parent_div.setAttribute("class", "wrapper");
        parent_div.style.gridTemplateColumns = "repeat(" + max_width + ", minmax(0, 1fr))";
        document.body.appendChild(parent_div);

        var i;
        for (i = 0; i < layout.length; i++) {
            //Remove div if nothing is entered
            if (layout[i].content === "") {
                continue;
            }

            var item = document.createElement("DIV");
            item.setAttribute("name", layout[i].type);
            item.style.gridColumn = layout[i].pos_x + "/ span " + layout[i].size_x;
            item.style.gridRow = layout[i].pos_y + "/ span " + layout[i].size_y;
            //item.innerHTML = layout[i].type /*+ ": " + layout[i].content*/;

            parent_div.appendChild(item);

            //Add preview for each type
            if (layout[i].content !== "") {
                if (layout[i].type === "Video") {
                    preview_video(item, layout[i].content);
                } else if (layout[i].type === "Description") {
                    preview_desc(item, layout[i].content);
                } else if (layout[i].type === "Image") {
                    preview_img(item, layout[i].content);
                } else if (layout[i].type === "Document") {
                    preview_doc(item, layout[i].content);
                } else if (layout[i].type === "Presentation") {
                    preview_pres(item, layout[i].content);
                }
            }
        }

        var user_type = "<?php echo $user_type ?>";
        var can_see_video = (user_type === 'Exhibitor' && <?php echo $the_post_id ?> === <?php echo $id ?>) || user_type === 'Judge';

        var sub_parent = document.createElement("DIV");
        sub_parent.setAttribute("id", "parent");
        sub_parent.setAttribute("class", "wrapper");
        sub_parent.style.gridTemplateColumns = "repeat(3, minmax(0, 1fr))";
        //sub_parent.style.backgroundColor = "rgba(255, 255, 255, 0.01)";
        document.body.appendChild(sub_parent);

        //Add comments
        var item = document.createElement("DIV");
        item.setAttribute("name", "Comments");
        item.setAttribute("class", "comments");
        //item.style.gridRow = comment_y + "/ span " + 1;
        item.style.gridColumn = 1 + " / span " + (can_see_video ? 2 : 3);
        item.style.gridAutoRows = "auto";
        item.style.borderRadius = "5px";
        item.style.border = "1px solid gray";
        var title = document.createElement("H1");
        title.innerText = "Comments";
        title.style.marginLeft = "10px";
        item.appendChild(title);
        sub_parent.appendChild(item);
        var comments = document.getElementById("comments");
        item.appendChild(comments);

        //Add video chat link (only for exhibitors and judges)
        if (can_see_video) {
            var item = document.createElement("DIV");
            item.style.gridColumn = 3 + " / span 1";
            item.style.gridRow = "span 1";
            item.style.borderRadius = "5px";
            item.style.border = "1px solid gray";

            //Header
            var title = document.createElement("H1");
            title.innerText = "Video Chat";
            title.style.textAlign = "center";

            //Button to join chat
            var join_button = document.createElement("BUTTON");
            join_button.innerText = "Join";
            join_button.onclick= function(){ visitVideoChat() };

            join_button.setAttribute("name", "back");
            join_button.setAttribute("class", "button");
            join_button.style.margin = "auto";
            join_button.style.display = "block";

            item.appendChild(title);
            item.appendChild(document.createElement("BR"));
            item.appendChild(document.createElement("BR"));
            item.appendChild(join_button);
            sub_parent.appendChild(item);
        }

        //Button to judge
        if (user_type === "Judge" && '<?php echo $division; ?>' === '<?php echo $exhibit_div; ?>') {
            var item = document.createElement("DIV");
            item.style.gridColumn = 3 + " / span 1";
            item.style.gridRow = "2 / span 1";
            item.style.borderRadius = "5px";
            item.style.border = "1px solid gray";

            //Header
            var title = document.createElement("H1");
            title.innerText = "Judge this exhibit";
            title.style.textAlign = "center";

            //Button to join chat
            var join_button = document.createElement("BUTTON");
            join_button.innerText = "Judge";
            join_button.onclick= function() { window.location = "../user_home/judging.php?p_id=" + '<?php echo $the_post_id; ?>' };

            join_button.setAttribute("name", "back");
            join_button.setAttribute("class", "button");
            join_button.style.margin = "auto";
            join_button.style.display = "block";

            item.appendChild(title);
            item.appendChild(document.createElement("BR"));
            item.appendChild(document.createElement("BR"));
            item.appendChild(join_button);
            sub_parent.appendChild(item);
        }
    }

    //Preview Powerpoint Document
    function preview_pres(parent, path) {
        //var item = document.createElement("BR");
        //parent.appendChild(item);
        item = document.createElement("OBJECT");
        var extention = path.substring(path.length - 4);

        if (extention === ".pdf") {
            item.setAttribute("data", path + "#toolbar=0");
        } else {
            item.setAttribute("data", "https://docs.google.com/gview?url=" + path + "&embedded=true");
        }
        item.setAttribute("type", "application/pdf");
        parent.appendChild(item);
    }

    //Preview Word Document
    function preview_doc(parent, path) {
        //var item = document.createElement("BR");
        //parent.appendChild(item);
        item = document.createElement("OBJECT");
        var extention = path.substring(path.length - 4);

        if (extention === ".pdf") {
            item.setAttribute("data", path + "#toolbar=0");
        } else {
            item.setAttribute("data", "https://docs.google.com/gview?url=" + path + "&embedded=true");
        }
        item.setAttribute("type", "application/pdf");
        parent.appendChild(item);
    }

    //Preview Image
    function preview_img(parent, path) {
        if (path === '') { return; }
        //var item = document.createElement("BR");
        //parent.appendChild(item);
        var img = new Image();
        img.className = 'Preview Image';
        img.src = path;
        parent.appendChild(img);
    }

    //Preview Descriptions
    function preview_desc(parent, text) {
        var link_count = (text.match(/https:/g) || []).length;
        parent.style.setProperty("overflow-y", "auto");

        for (var i = 0; i < link_count; i++) {
            //Add text
            var item = document.createElement("P");
            var pos = text.indexOf('https://');
            item.innerText = text.substr(0, pos);
            text = text.substring(pos);
            parent.appendChild(item);

            //Add hyperlink
            var item = document.createElement("A");
            var link = '';
            while(text.length > 0 && text.charAt(0) !== ' ' && text.charAt(0) !== '\n' ) {
                link += text.charAt(0);
                text = text.substr(1);
            }
            item.href = link;
            item.innerText = link.replace(/(\r\n|\n|\r)/gm, "");
            parent.appendChild(item);
        }

        var item = document.createElement("P");
        item.innerText = text;
        parent.appendChild(item);
    }

    //Preview Youtube Videos
    function preview_video(parent, link) {
        //var item = document.createElement("BR");
        //parent.appendChild(item);
        item = document.createElement("IFRAME");
        item.setAttribute("src", "https://www.youtube.com/embed/" + link.split('=')[1]);
        item.setAttribute("frameborder", "0");
        item.setAttribute("allow", "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture")
        parent.appendChild(item);
    }

</script>

<?php mysqli_close($link); ?>