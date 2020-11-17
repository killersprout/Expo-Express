<?php
// Connect to Database
$link = mysqli_connect('mysql.expoexpress.online', 'expoexpressonlin', 'uUAkiKn5', 'expoexpress_online');

// Check connection
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT is_published, exhibitor, title, types FROM exhibits";
$result = mysqli_query($link, $sql);

$title = "";
$str = "";
//Check if rows exist
if (mysqli_num_rows($result) > 0) {
    //Retrieve first row
    $row = mysqli_fetch_assoc($result);
    //Retrieve data to output
    $str = $row["types"];
    //Retrieve title
    $title = $row["title"];
} else {
    //echo "0 results";
}

mysqli_close($link);

if(isset($_POST['back'])) {
    header("Location: File_upload.php");
}

if(isset($_POST['publish'])) {
    echo "Publish";
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
        grid-template-columns: repeat(3, 1fr);
        grid-gap: 10px;
        grid-auto-rows: minmax(100px, 300px);
    }

    .wrapper > div {
        padding: 10px;
        background-color: #214e5c;
    }

    .title {
        text-align: center;
        background-color: #15343e;
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
</style>

<head>
    <title>Template Preview</title>
</head>

<body onresize="resizeGrid()" onload="GenerateFromFile()" style="background-color: #4f808e; color: white;">

<!--<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=https://www.expoexpress.online/exhibit-page/images/Edouard Gruyters CSE4510 Lab #7 (1).docx'></iframe>-->

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
            if (type === "IFRAME") {
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
        data = data.split(';');

        var title = "<?php echo $title ?>";
        var parent_div = document.createElement("DIV");
        parent_div.setAttribute("class", "title");
        var title_elm = document.createElement("H1");
        title_elm.innerText = title;
        parent_div.appendChild(title_elm);
        document.body.appendChild(parent_div);

        //Get template file from the server
        var file = loadFile('/exhibit-page/template1.txt');
        file = file.split('\n');
        //Remove any newline or carriage return characters
        for (var i = 0; i < file.length; i++) {
            file[i] = file[i].replace(/^\s+|\s+$/g, '');
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

        GenerateLayout(layout);

        //Add publish and back button
        var form = document.createElement("FORM");
        form.setAttribute("type", "submit");
        form.setAttribute("method", "post");
        document.body.appendChild(form);
        var back = document.createElement("BUTTON");
        back.innerText = "Back";
        back.setAttribute("name", "back");
        back.setAttribute("class", "button");
        form.appendChild(back);
        var publish = document.createElement("BUTTON");
        publish.innerText = "Publish";
        publish.setAttribute("name", "publish");
        publish.setAttribute("class", "button");
        publish.style.position = "absolute";
        publish.style.right = "8px";
        form.appendChild(publish);

        resizeGrid();
    }

    function GenerateLayout(layout) {
        //Check if div exists
        var parent_div = document.getElementById("parent");
        if (parent_div != null) {
            parent_div.remove();
            //window.alert("Removing parent");
        }

        parent_div = document.createElement("DIV");
        parent_div.setAttribute("id", "parent");
        parent_div.setAttribute("class", "wrapper");
        document.body.appendChild(parent_div);

        var i;
        for (i = 0; i < layout.length; i++) {
            var item = document.createElement("DIV");
            item.setAttribute("name", layout[i].type);
            item.style.gridColumn = layout[i].pos_x + "/ span " + layout[i].size_x;
            item.style.gridRow = layout[i].pos_y + "/ span " + layout[i].size_y;
            item.innerHTML = layout[i].type /*+ ": " + layout[i].content*/;

            parent_div.appendChild(item);

            //Add preview for each type
            if (layout[i].type === "Video" && layout[i].content !== "") {
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

    //Preview Powerpoint Document
    function preview_pres(parent, path) {
        var item = document.createElement("BR");
        parent.appendChild(item);
        item = document.createElement("IFRAME");
        //item.scr = "https://view.officeapps.live.com/op/embed.aspx?src=" + path;
        item.scr = "https://docs.google.com/gview?url=" + path + "&embedded=true";
        //item.setAttribute("wdith", "1366px");
        //item.setAttribute("height", "623px");
        //item.setAttribute("frameborder", "0");
        //https://view.officeapps.live.com/op/embed.aspx?src=https://www.expoexpress.online/exhibit-page/Template/images/Lab1.docx
        parent.appendChild(item);
    }

    //Preview Word Document
    function preview_doc(parent, path) {
        var item = document.createElement("BR");
        parent.appendChild(item);
        item = document.createElement("IFRAME");
        item.scr = "https://view.officeapps.live.com/op/embed.aspx?src=" + path;
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
        //img.width = 300;
        //img.height = 300;
        img.src = path;
        parent.appendChild(img);
    }

    //Preview Descriptions
    function preview_desc(parent, text) {
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