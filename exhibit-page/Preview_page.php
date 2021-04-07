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
$event = $_SESSION["event_name"];
$id = $_SESSION["user_id"];

// Connect to Database
$link = mysqli_connect('mysql.expoexpress.online', 'expoexpressonlin', 'uUAkiKn5', 'expoexpress_online');

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


$sql = "SELECT * FROM exhibits WHERE exhibitor = '$user'";
$title = "";
$str = "";

//Get row
if ($result = mysqli_query($link, $sql)) {
    //fetch associative array
    while ($row = mysqli_fetch_row($result)) {
        //Check if draft row already exists
        if($row[0] == 0) {
            $row_found = true;
            $title = $row[3];
            $title = str_replace('"', '\"', $title);

            //Retrieve data from draft.txt file
            $file_path = getcwd() . '/exhibit_texts/' . $id . "_draft.txt";
            $str = file_get_contents($file_path);
            $str = str_replace("\n", "\\n", $str);
            $str = str_replace("\r", "\\r", $str);
            $str = str_replace('"', '\"', $str);

            break;
        }
    }
}

mysqli_close($link);

if(isset($_POST['back'])) {
    header("Location: File_upload.php");
}

if(isset($_POST['publish'])) {
    $link = mysqli_connect('mysql.expoexpress.online', 'expoexpressonlin', 'uUAkiKn5', 'expoexpress_online');

    // Check connection
    if (!$link) {
        die("Connection failed: " . mysqli_connect_error());
    }

    //Remove all previous records
    $sql = "DELETE FROM exhibits WHERE exhibitor='$user' and is_published='1'";
    if (mysqli_query($link, $sql)) {
        //echo "Old exhibit published";
    }

    //Remove previous .txt file
    if (file_exists(getcwd() . '/exhibit_texts/' . $id . "_published.txt")) {
        unlink(getcwd() . '/exhibit_texts/' . $id . "_published.txt");
    }

    //Updates draft to published
    $sql = "UPDATE exhibits SET is_published='1' WHERE exhibitor='$user' and is_published='0'";
    if (mysqli_query($link, $sql)) {
        //echo "Exhibit published";
    }

    //Update draft .txt to published.txt
    rename(getcwd() . '/exhibit_texts/' . $id . "_draft.txt", getcwd() . '/exhibit_texts/' . $id . "_published.txt");

    mysqli_close($link);

    //Redirect to home page
    header("location: Display_exhibit.php");
}

//TODO:
//WORD + PRESENTATION PREVIEW
//YOUTUBE PREVIEW SIZE FIX
//IMAGE SCALE FIX

?>

<!DOCTYPE html>
<html>

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

    a {
        color: white;
    }
</style>

<head>
    <title>Template Preview</title>
</head>

<body onresize="resizeGrid()" onload="GenerateFromFile()" style="background-color: #232323; color: white;">

</body>

</html>

<script>
    const grid_spacing = 10;

    function resizeGrid() {
        const grid_width = 3;
        const max_width = window.innerWidth;
        const max_height = window.innerWidth;
        var children = document.getElementById("parent").childNodes;

        for(var i = 0; i < children.length; i++) {
            var child = children[i];
            var item_x = child.style.gridColumn.split(' ')[0];
            var item_y = child.style.gridRow.split(' ')[0];
            var item_width = child.style.gridColumn.split(' ')[3];
            var item_height = child.style.gridRow.split(' ')[3];
            var div_width = child.offsetWidth;
            var div_height = child.offsetHeight;

            var sub_child = child.childNodes[child.childNodes.length - 1];
            var type = sub_child.nodeName;
            //Video resize
            if (type === "IFRAME" || type === "OBJECT") {
                sub_child.style.height = '95%';
                sub_child.style.width = '100%';
            } else if (type === "IMG") {
                sub_child.style.height = '95%';
            }
        }
    }

    function GenerateFromFile() {
        //Get data from server
        var data = "<?php echo $str ?>";
        data.replace('\"', '"');
        data = data.split('{[&;}]');

        var title = "<?php echo $title ?>";
        title.replace('\"', '"');

        var parent_div = document.createElement("DIV");
        parent_div.setAttribute("class", "title");
        var title_elm = document.createElement("H1");
        title_elm.innerText = title;
        parent_div.appendChild(title_elm);
        document.body.appendChild(parent_div);

        //Get template file from the server
        var file = loadFile('/exhibit-page/templates/' + '<?php echo $template_name ?>' + '.txt');
        file = file.split('\n');
        //Remove any newline or carriage return characters
        for (var i = 0; i < file.length; i++) {
            file[i] = file[i].replace(/^\s+|\s+$/g, '');
        }

        var i = 0;

        //Set max width
        var width_height = file[i++];
        var max_width = width_height[0];

        let layout = [];
        var num_of_types = file[i++];
        for (var j = 0; j < num_of_types; j++) {
            var pos_x = file[i++];
            var pos_y = file[i++];
            var size_x = file[i++];
            var size_y = file[i++];
            var type = file[i++];
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

        GenerateLayout(layout, max_width);

        //Add publish and back button
        var form = document.createElement("FORM");
        form.setAttribute("type", "submit");
        form.setAttribute("method", "post");
        document.body.appendChild(form);
        var back = document.createElement("BUTTON");
        back.innerText = "Back";
        back.setAttribute("name", "back");
        back.setAttribute("class", "button");
        back.style.float = "left";
        form.appendChild(back);
        var publish = document.createElement("BUTTON");
        publish.innerText = "Publish";
        publish.setAttribute("name", "publish");
        publish.setAttribute("class", "button");
        publish.style.float = "right";
        form.appendChild(publish);

        resizeGrid();
    }

    function GenerateLayout(layout, max_width) {
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
    }

    //Preview Powerpoint Document
    function preview_pres(parent, path) {
        var item = document.createElement("BR");
        parent.appendChild(item);
        item = document.createElement("OBJECT");
        var extention = path.substring(path.length - 4);

        if (extention === ".pdf") {
            item.setAttribute("data", path + "#toolbar=0");
        } else {
            item.setAttribute("data", "https://docs.google.com/gview?url=" + path + "&embedded=true");
        }
        item.setAttribute("type", "application/pdf");

        //item.scr = "https://docs.google.com/gview?url=" + path + "&embedded=true";
        //item.setAttribute("frameborder", "0");
        //https://view.officeapps.live.com/op/embed.aspx?src=https://www.expoexpress.online/exhibit-page/Template/images/Lab1.docx
        parent.appendChild(item);
    }

    //Preview Word Document
    function preview_doc(parent, path) {
        var item = document.createElement("BR");
        parent.appendChild(item);
        item = document.createElement("OBJECT");
        var extention = path.substring(path.length - 4);

        if (extention === ".pdf") {
            item.setAttribute("data", path + "#toolbar=0");
        } else {
            item.setAttribute("data", "https://docs.google.com/gview?url=" + path + "&embedded=true");
        }
        item.setAttribute("type", "application/pdf");

        //item.scr = "https://view.officeapps.live.com/op/embed.aspx?src=" + path;
        //item.scr = "https://docs.google.com/gview?url=" + path + "&embedded=true";
        //https://view.officeapps.live.com/op/embed.aspx?src=https://www.expoexpress.online/exhibit-page/images/Edouard Gruyters CSE4510 Lab #7 (1).docx
        parent.appendChild(item);
    }

    //Preview Image
    function preview_img(parent, path) {
        var item = document.createElement("BR");
        parent.appendChild(item);
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
        var item = document.createElement("BR");
        parent.appendChild(item);
        item = document.createElement("IFRAME");
        item.setAttribute("src", "https://www.youtube.com/embed/" + link.split('=')[1]);
        item.setAttribute("frameborder", "0");
        item.setAttribute("allow", "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture")
        parent.appendChild(item);
    }

    //Read file from server
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
</script>