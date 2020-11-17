<?php
// Include the db file
//include "../db/config.php";
session_start();

//1. RETRIEVE ALREADY UPLOADED DATA

//if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: elogin.php");
    exit;
}

//Check already saved data & load it
$user = 'sample user';//$_SESSION["username"];

// Connect to Database
$link = mysqli_connect('mysql.expoexpress.online','expoexpressonlin','uUAkiKn5','expoexpress_online');

// Check connection
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

//Fetch user exhibit from database
$sql = "SELECT * FROM exhibits WHERE exhibitor = '$user'";

//Data to hold
$row_found = false;
$title = "";
$types = "";

if ($result = mysqli_query($link, $sql)) {
    //fetch associative array
    while ($row = mysqli_fetch_row($result)) {
        echo $row;
        //Check if draft row already exists
        if($row[0] == 0) {
            $row_found = true;
            $title = $row[2];
            $types = $row[3];
        }
    }
}

mysqli_close($link);

//2. UPLOAD NEW DATA ALONGSIDE EXISTING DATA
if($_SERVER["REQUEST_METHOD"] == "POST") {
    //Set up array to hold data types and data
    $data_arr = array();

    // Connect to Database
    $link = mysqli_connect('mysql.expoexpress.online','expoexpressonlin','uUAkiKn5','expoexpress_online');

    // Check connection
    if (!$link) {
        die("Connection failed: " . mysqli_connect_error());
    }

    //Get previous uploaded data
    $prev_data = $types;
    $prev_data = explode(";", $prev_data);

    foreach ($prev_data as $value) {
        echo $value;
    }

    $prev_count = 0;

    echo $data_arr;

    //Create str for description and video links
    $count = 0;
    foreach ($_POST as $key => $value) {
        if ($count > 0) {
            //All other input
            $prev_count += 2;

            $type = strtok(htmlspecialchars($key), '_');
            $str = $str . $type . ";" . $value . ";";
        } else {
            //Exhibit title input
            $title = $value;
        }
        $count++;
    }

    foreach ($_FILES as $key => $value) {
        $prev_url = $prev_data[$prev_count + 1];
        $prev_arr = explode('/', $prev_url);
        $prev_item = end($prev_arr);
        $prev_count += 2;

        $type = strtok(htmlspecialchars($key), '_');
        if ($value['name'] !== "") {
            //Remove replaced images
            if ($prev_item !== "") {
                unlink(getcwd() . '/images/' . $prev_item);
            }

            $tname = $value['tmp_name'];
            $uploads_dir = getcwd() . '/images/';
            $full_path = $uploads_dir . $value['name'];
            move_uploaded_file($tname, $full_path);
            $str = $str . $type . ";" . 'https://www.' . substr($full_path, 16) . ";";
        } else {
            $str = $str . $type . ";" . $prev_url . ";";
        }
    }

    //Add entry if none exists
    if(!$row_found) {
        $sql = "INSERT INTO exhibits (is_published, exhibitor, title, types) VALUES (false, 'sample user', '$title', '$str')";
        if (mysqli_query($link, $sql)) {
            //echo "New record created successfully";
        }
    } else { //Update entry if one exists
        $sql = "UPDATE exhibits SET title='$title', types='$str' WHERE exhibitor='$user' and is_published='0'";
        if (mysqli_query($link, $sql)) {
            //echo "New record replaced successfully";
        }
    }

    mysqli_close($link);

    //Can't put up echos
    header("Location: Preview_page.php");
}
?>

<!DOCTYPE html>
<html>

<style>
    .wrapper {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-gap: 5px;
        font-size: 20px;
        grid-auto-rows: minmax(100px, 300px);
    }

    .wrapper>div {
        border: 2px solid #ffffff;
        padding: 10px;
        border-radius: 5px;
        background-color: #7e0001;
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

    #Title {
        grid-column: 1 / span 3;
    }

    input.a {
        margin-left: 10px;
    }

    p {
        font-size: 15px;
    }

    body {
        font-size: 30px;
    }
</style>

<head>
    <title>Resource Upload</title>
</head>

<body onload="Refesh()" style="background-color: #860001; color: white;">

Resource Upload
<br><br>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">

    <div class="wrapper" id="parent">
        <div id="Title">
            <b>Exhibit Title</b>
            <input id="exhibit-title" class="a" type="text" name="exhibit-title" onchange="updateSubmit()" placeholder="Enter Exhibit Title Here">
        </div>
    </div>

    <input id="Submit" class="button" type="submit" value="Preview" style="position: absolute; right: 8px;">
</form>

</body>

</html>

<script>
    function Refesh() {
        //Get template file from the server
        var file = loadFile('/exhibit-page/template1.txt');
        file = file.split('\n');
        //Remove any newline or carriage return characters
        for(var i = 0; i < file.length; i++) { file[i] = file[i].replace(/^\s+|\s+$/g, ''); }

        //Get unpublished data
        var found = "<?php echo $row_found ?>";
        var sql_title = "";
        var sql_types = "";
        if (found) {
            sql_title = "<?php echo $title ?>";
            sql_types = "<?php echo $types ?>";
            sql_types = sql_types.split(";");
        }

        //Create object to pass by reference
        var type_obj = { data: sql_types};

        if(found) {
            document.getElementById("exhibit-title").setAttribute("value", sql_title);
        }

        let layout = [];
        var i = 0;
        var num_of_types = file[i++];
        for (var j = 0; j < num_of_types; j++) {
            var pos_x = file[i++];
            var pos_y = file[i++];
            var size_x = file[i++];
            var size_y = file[i++];
            var type = file[i++];

            //Check if content is found in the database
            var content = "";
            if (found) {
                content = getContent(type, type_obj);
            }

            var item = {pos_x: pos_x, pos_y: pos_y, size_x: size_x, size_y: size_y, type: type, content: content };
            layout.push(item);
        }

        GenerateLayout(layout);
    }

    function GenerateLayout(layout) {
        //Check if div exists
        var parent_div = document.getElementById("parent");

        var desc_count = 0;
        var video_count = 0;
        var image_count = 0;
        var docu_count = 0;
        var pres_count = 0;

        var i;
        for (i = 0; i < layout.length; i++) {
            var item = document.createElement("DIV");

            item.style.gridColumn = layout[i].pos_x + "/ span " + layout[i].size_x;
            item.style.gridRow = (parseInt(layout[i].pos_y) + 1) + "/ span " + layout[i].size_y;
            item.innerHTML = layout[i].type;

            parent_div.appendChild(item);

            var br = document.createElement("BR");
            item.appendChild(br);

            //Add input fields
            var name = layout[i].type;
            if (name === "Description") {
                inputDescription(item, layout[i].content, name + "_" + desc_count);
                desc_count++;
            } else if (name === "Video") {
                inputVideo(item, layout[i].content, name + "_" + video_count);
                video_count++;
            } else if (name === "Image") {
                inputImage(item, layout[i].content, name + "_" + image_count);
                image_count++;
            } else if (name === "Document") {
                inputDoc(item, layout[i].content, name + "_" + docu_count);
                docu_count++;
            } else if (name === "Presentation") {
                inputPres(item, layout[i].content, name + "_" + pres_count);
                pres_count++;
            } else {
                item.innerHTML = item.innerHTML + layout[i].content;
            }
        }
    }

    //Input field for descriptions
    function inputDescription(parent, content, name) {
        var input = document.createElement("TEXTAREA");
        parent.appendChild(input);

        input.innerHTML = content;
        input.setAttribute("placeholder", "Enter Description here");
        input.setAttribute("name", name);
        input.style.height = "50px"; //AUTOMATE
        input.style.width = "200px"; //AUTOMATE
    }

    //Input field for videos
    function inputVideo(parent, content, name) {
        var input = document.createElement("INPUT");
        parent.appendChild(input);

        input.setAttribute("type", "url");
        input.setAttribute("value", content);
        input.setAttribute("name", name);
        input.setAttribute("placeholder", "Enter Youtube URL here");
    }

    //Input field for images
    //TODO:
    //BUTTON TO DELETE IMAGE & REMOVE FROM DATABASE
    function inputImage(parent, content, name) {
        var input = document.createElement("INPUT");
        parent.appendChild(input);

        input.setAttribute("type", "file");
        input.setAttribute("id", "img");
        input.setAttribute("accept", "image/*");
        input.setAttribute("name", name);
        if(content !== "") {
            var txt = document.createElement("P");
            parent.appendChild(txt);
            txt.innerHTML = "\nThe currently selected image is: " + getFileName(content);
        }
    }

    //Input field for word documents
    //TODO:
    //ACCEPT PDF's
    function inputDoc(parent, content, name) {
        var input = document.createElement("INPUT");
        parent.appendChild(input);

        input.setAttribute("type", "file");
        input.setAttribute("accept", "application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword");
        input.setAttribute("name", name);
        input.setAttribute("value", content);
        if(content !== "") {
            var txt = document.createElement("P");
            parent.appendChild(txt);
            txt.innerHTML = "\nThe currently selected document is: " + getFileName(content);
        }
    }

    //Input field for presentations
    function inputPres(parent, content, name) {
        var input = document.createElement("INPUT");
        parent.appendChild(input);

        input.setAttribute("type", "file");
        input.setAttribute("accept", "application/vnd.ms-powerpoint, .pps");
        input.setAttribute("name", name);
        input.setAttribute("value", content);
        if(content !== "") {
            var txt = document.createElement("P");
            parent.appendChild(txt);
            txt.innerHTML = "\nThe currently selected document is: " + getFileName(content);
        }
    }

    //UTIL
    //Get filename from URL
    function getFileName(URL) {
        var array = URL.split('/');
        return array[array.length - 1];
    }

    //Get the content of the first matching file type
    function getContent(type, type_obj) {
        var index = type_obj.data.findIndex(element => element === type);
        if(index >= 0) {
            var content = type_obj.data[index + 1];
            //Remove item from the list so other items can be found
            type_obj.data[index] = "";
            type_obj.data[index + 1] = "";
            return content;
        }
        return "";
    }

    //Read file from server
    function loadFile(filePath) {
        var result = null;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", filePath, false);
        xmlhttp.send();
        if (xmlhttp.status==200) {
            result = xmlhttp.responseText;
        }
        return result;
    }

    //Enable & disable the submit button
    function updateSubmit() {
        var text = document.getElementById("exhibit-title").value;
        var button = document.getElementById("Submit");

        if (text.length > 0) {
            button.removeAttribute("disabled");
            button.style.opacity = 1;
        } else {
            button.setAttribute("disabled", "true");
            button.style.opacity = 0.5;
        }
    }
</script>