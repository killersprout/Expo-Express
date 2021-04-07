function what() {
    window.alert("Yooo");
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

function GenerateFromFile(document, exhibit_title, template, data) {
    //Get data from server
    var data = data;
    data = data.split(';');

    var title = exhibit_title;
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
    back.style.float = "left";
    form.appendChild(back);

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
