<?php
include "../db/config.php";

$errors = []; // Store errors here

$target_dir = "uploads/images/";
// Valid file extensions
$extensions_arr = array("jpg","jpeg","png","gif");

if (isset($_POST['upload'])) {

    if (!empty(array_filter($_FILES['fileUpload']['name']))) {
        foreach($_FILES['fileUpload']['name'] as $id=>$val){
            // Check extension
            $name = $_FILES['fileUpload']['name'][$id];
            $tempLocation    = $_FILES['fileUpload']['tmp_name'][$id];
            $targetFilePath  = $target_dir . $name;
            $target_file = $target_dir . basename($_FILES['fileUpload']['name'][$id]);

            // Select file type
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

            // Check extension
            if (in_array($imageFileType, $extensions_arr)) {

                // Convert to base64
                $imgContent = addslashes(file_get_contents($tempLocation));
                // Insert record

                $query = "INSERT INTO image(id, name, image) VALUES('" . $id . "', '" . $name . "', '" . $imgContent . "')";

                // Upload file
                move_uploaded_file($tempLocation, $targetFilePath);

                if (mysqli_query($link, $query)) {
                    echo "Uploaded Successfully\n";
                } else {
                    $errors[] = "MYSQL Error When Uploading Image";
                }
            }
            else {
                $errors[] = "Not a Valid File Extension";
            }
        }
    }
    foreach($errors as $value){
        echo $value . "<br>";
    }

}
?>