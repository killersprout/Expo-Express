<?php
include "../db/config.php";
$descriptionError = $linksError = "";
$description = $externalLinks = "" ;
$paramId = $paramDescription = $paramLink =  "";
$successMessage = "";

if (isset($_POST['submitText'])) {
    if(empty($_POST["description"])){

        $descriptionError = "Please enter a Description.";
    }
    else {
        $description = $_POST["description"];
    }

    if(empty($_POST['externalLinks'])){
        $linksError = "Please enter atleast one link.";
    }
    else {
        $externalLinks = $_POST["externalLinks"];
    }

    $id = 0;

    $query = "INSERT INTO textResources (id, description, links) VALUES (?, ?, ?);";

    if($stmt = mysqli_prepare($link, $query)) {
        mysqli_stmt_bind_param($stmt, "iss", $paramId, $paramDescription, $paramLink);

        $paramId = $id;
        $paramDescription = $description;
        $paramLink = $externalLinks;

        if (mysqli_stmt_execute($stmt)){
            $successMessage = "Updated Successfully.";
        }
        else {
            $descriptionError = "Error Occurred;";
            echo "Error Occurred;";
        }
    }

}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

    <title>Upload Images</title>
    <style>
        .container {
            max-width: 450px;
        }
        .imgGallery img {
            padding: 8px;
            max-width: 100px;
        }
    </style>
</head>

<body>

<div class="container mt-5">
    <form action="upload_img.php" enctype="multipart/form-data" method="post" class="mb-3">
        <h3 class="text-center mb-5">Upload Images</h3>

        <div class="user-image mb-3 text-center">
            <div class="imgGallery">
                <!-- Image preview -->
            </div>
        </div>

        <div class="custom-file">
            <input type="file" name="fileUpload[]" class="custom-file-input" id="chooseFile" multiple>
            <label class="custom-file-label" for="chooseFile">Select files</label>
        </div>

        <button type="submit" name="upload" class="btn btn-primary btn-block mt-4">
            Upload Files
        </button>
    </form>

    <!-- Display response messages -->
    <?php if(!empty($response)) {?>
        <div class="alert <?php echo $response["status"]; ?>">
            <?php echo $response["message"]; ?>
        </div>
    <?php }?>
</div>

<div class="wrapper">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <div class="form-group <?php echo (!empty($descriptionError)) ? 'has-error' : ''; ?>">
            <label>Project Description</label>
            <input type="text" name="description" class="form-control" value="<?php echo $description; ?>">
            <span class="help-block"><?php echo $descriptionError; ?></span>
        </div>

        <div class="form-group <?php echo (!empty($linksError)) ? 'has-error' : ''; ?>">
            <label>External Links</label>
            <input type="text" name="externalLinks" class="form-control" value="<?php echo $externalLinks; ?>">
            <span class="help-block"><?php echo $linksError; ?></span>
        </div>

        <div class="form-group">
            <input type="submit" name="submitText" class="btn btn-primary" value="Submit">
        </div>
    </form>
</div>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

<script>
    $(function() {
        // Multiple images preview with JavaScript
        var multiImgPreview = function(input, imgPreviewPlaceholder) {

            if (input.files) {
                var filesAmount = input.files.length;

                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();

                    reader.onload = function(event) {
                        $($.parseHTML('<img>')).attr('src', event.target.result).appendTo(imgPreviewPlaceholder);
                    }

                    reader.readAsDataURL(input.files[i]);
                }
            }

        };

        $('#chooseFile').on('change', function() {
            multiImgPreview(this, 'div.imgGallery');
        });
    });
</script>


</body>

</html>