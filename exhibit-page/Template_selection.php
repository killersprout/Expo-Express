<?php
//Organizer can choose template
session_start();

//if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: elogin.php");
    exit;
}

//Check already saved data & load it
$user = $_SESSION["username"];
$event = $_SESSION["event"];

// Connect to Database
$link = mysqli_connect('mysql.expoexpress.online','expoexpressonlin','uUAkiKn5','expoexpress_online');

// Check connection
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

//Fetch user exhibit from database
$sql = "SELECT * FROM templates";

$result = mysqli_query($link, $sql);

$rows = array();
$index = 0;
while($row = mysqli_fetch_row($result)) {
    $rows[$index] = $row;
    $index++;
}

//print_r($rows);

/*for ($i = 0; $i < count($rows); $i++) {
    for ($j = 0; $j < count($rows[$i]); $j++) {
        echo $rows[$i][$j] . ", ";
    }
    echo '<br>';
}*/

mysqli_close($link);

//Add function for 'Select' button
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to Database
    $link = mysqli_connect('mysql.expoexpress.online','expoexpressonlin','uUAkiKn5','expoexpress_online');

    // Check connection
    if (!$link) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $template_name = $_POST['templateName'];
    $sql = "UPDATE users SET template_layout='$template_name' WHERE user_role='Organizer' AND username='$user'";

    if (mysqli_query($link, $sql)) {
        //echo "Updated entry";
    }

    mysqli_close($link);

    //Return to user_home
    header("Location: ../user_home");
}


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
        border: 1px solid white;
        border-radius: 5px;
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

    .footer {
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        background-color: lightgray;
        color: black;
        text-align: center;
        line-height: 30px;
    }

    .header {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        background-color: lightgray;
        color: black;
        text-align: center;
        line-height: 30px;
    }

    a {
        color: white;
    }
</style>

<head>
    <title>Template Preview</title>
</head>

<body onload="init()" style="background-color: #232323; color: white;">

<div class="header">
    <input id="Back" class="button" type="submit" onclick="Back()" value="Back" style="float: left;">

    <form method="POST" enctype="multipart/form-data">
        <input id="Submit" class="button" type="submit" value="Select" style="float: right;">
        <input type="hidden" id="templateName" name="templateName" value="">
    </form>

    <p><b id="Template_Name">Template Title</b></p>
</div>

<br><br><br>

<h1 id="template-title" class="title">Test</h1>
<div class="wrapper" id="parent"></div>

<br><br><br><br>


<div class="footer">
    <input id="Previous" class="button" type="submit" onclick="updateTemplate(-1)" value="Previous" style="float: left;">
    <input id="Next" class="button" type="submit" onclick="updateTemplate(1)" value="Next" style="float: right;">
    <p><b id="Template_Index">0/0</b></p>
</div>

</body>

</html>

<script>

    //Back button
    function Back() {
        window.location.href = '../user_home';
    }

    //Clamps a value between two ranges
    const clamp = (num, a, b) => Math.max(Math.min(num, Math.max(a, b)), Math.min(a, b));

    //Global variables
    var templates;
    var template_index = 0;

    //Get the sql data from php
    function init() {

        templates = <?php echo json_encode( $rows ) ?>;
        updateTemplate(0);
    }

    /*function resizeGrid() {
        const grid_width = 3;
        const max_width = window.innerWidth;
        const max_height = window.innerHeight;

        var children = document.getElementById("parent").childNodes;

        for(var i = 0; i < children.length; i++) {
            var child = children[i];
            var bounds = child.getBoundingClientRect();

            var sub_child = child.childNodes[child.childNodes.length - 1];
            var type = sub_child.nodeName;
            //Video resize
            if (type === "IFRAME" || type === "OBJECT") {
                //sub_child.style.height = '95%';
                //sub_child.style.width = '100%';
            //Image resize
            } else if (type === "IMG") {

            }
        }
    }*/

    //Update the header title, the footer index and load in the indexed template
    function updateTemplate(offset) {
        template_index += offset;
        template_index = clamp(template_index, 0, templates.length - 1);

        var str = templates[template_index][0];
        var template_name = str.substring(0, str.length - 4);
        document.getElementById("Template_Name").innerText = template_name.replace('_', ' ');
        document.getElementById("template-title").innerText = template_name.replace('_', ' ');
        document.getElementById("Template_Index").innerText = (template_index + 1) + "/" + templates.length

        document.getElementById("templateName").setAttribute("VALUE", template_name);

        loadTemplate(templates[template_index][1], templates[template_index][0]);
    }

    function loadTemplate(link, title) {
        //Get template file from the server
        var file = loadFile(link);
        file = file.split('\n');
        //Remove any newline or carriage return characters
        for (var i = 0; i < file.length; i++) {
            file[i] = file[i].replace(/^\s+|\s+$/g, '');
        }
        var i = 0;

        //Set max width
        var width_height = file[i++];
        var max_width = width_height[0];
        var parent_div = document.getElementById("parent");
        parent_div.style.gridTemplateColumns = "repeat(" + max_width + ", minmax(0, 1fr))";

        let layout = [];
        var num_of_types = file[i++];
        for (var j = 0; j < num_of_types; j++) {
            var pos_x = file[i++];
            var pos_y = file[i++];
            var size_x = file[i++];
            var size_y = file[i++];
            var type = file[i++];

            var item = {pos_x: pos_x, pos_y: pos_y, size_x: size_x, size_y: size_y, type: type};
            layout.push(item);
            //window.alert(item.pos_x + ", " + item.pos_y + ", " + item.size_x + ", " + item.size_y + ", " + item.type);
        }

        GenerateLayout(layout, title);
    }

    function GenerateLayout(layout, title) {
        //Check if div exists
        var parent_div = document.getElementById("parent");

        while (parent_div.firstChild) {
            parent_div.removeChild(parent_div.firstChild);
        }

        var i;
        for (i = 0; i < layout.length; i++) {
            var item = document.createElement("DIV");
            item.setAttribute("name", layout[i].type);
            item.style.gridColumn = layout[i].pos_x + "/ span " + layout[i].size_x;
            item.style.gridRow = layout[i].pos_y + "/ span " + layout[i].size_y;
            item.innerHTML = layout[i].type;

            parent_div.appendChild(item);

            //Add example preview for each type
            if (layout[i].type === "Video") {
                preview_video(item, layout[i].content);
            } else if (layout[i].type === "Description") {
                preview_desc(item);
            } else if (layout[i].type === "Image") {
                preview_img(item);
            } else if (layout[i].type === "Document") {
                preview_doc(item);
            } else if (layout[i].type === "Presentation") {
                preview_pres(item);
            }
        }
    }

    //Preview Powerpoint Document
    function preview_pres(parent) {
        var item = document.createElement("BR");
        parent.appendChild(item);
        item = document.createElement("OBJECT");

        item.setAttribute("data", "https://www.expoexpress.online/exhibit-page/example_types/Example_Presentation.pdf" + "#toolbar=0");

        item.setAttribute("type", "application/pdf");
        parent.appendChild(item);
    }

    //Preview Word Document
    function preview_doc(parent) {
        var item = document.createElement("BR");
        parent.appendChild(item);
        item = document.createElement("OBJECT");
        item.setAttribute("data", "https://www.expoexpress.online/exhibit-page/example_types/Example_Document.pdf" + "#toolbar=0");
        item.setAttribute("type", "application/pdf");
        parent.appendChild(item);
    }

    //Preview Image
    function preview_img(parent) {
        var item = document.createElement("BR");
        parent.appendChild(item);
        var img = new Image();
        img.className = 'Preview Image';
        img.src = "https://www.expoexpress.online/exhibit-page/example_types/Example_Image.jpg";
        parent.appendChild(img);
    }

    //Preview Descriptions
    function preview_desc(parent) {
        //LINKS FIX EM
        var text = 'This is an Example Description ldfjkgldkfjgldfjgldkfjgdflkgjdflkgjdflkgsdfsdfsdfdsf. Go visit our website: https://www.expoexpress.online/';
        var link_count = (text.match(/https:/g) || []).length;

        for (var i = 0; i < link_count; i++) {
            //Add text
            var item = document.createElement("P");
            item.style.setProperty('word-wrap', 'break-word');
            //item.style.setProperty("white-space", "nowrap");
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
    function preview_video(parent) {
        var link = 'https://www.youtube.com/watch?v=SzJ46YA_RaA';
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