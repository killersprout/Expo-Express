<?php
session_start();
include "includes/user_header.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// if the user isnt logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: https://www.expoexpress.online/register/index.php");
    exit;
}
$userid = $_SESSION["user_id"];
$role = $_SESSION['user_role'];
$orgName = $_SESSION['organization'];
$event = $_SESSION['event_name'];

$search = "";
$resultSearch = array();
$exists = 0;
$div = "";


$query = "SELECT * FROM users WHERE user_id='" . $userid . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$role = $row["user_role"];
$divName = $row["division_name"];

$judge_division = "";
if ($role == "Judge" || $role == "Organizer") {
    $judge_division = $divName;
    //echo "<th>Judge</th>";
} else {
    //echo "<th style='display: none;'>Judge</th>";
}

function key_check($array, $key, $val) {
    foreach ($array as $item)
        if (isset($item[$key]) && $item[$key] == $val)
            return true;
    return false;
}

//Get catName
$sort_type = "";
$parent_id = "";
if ($_GET['division'])
{
    $catName = $_GET['division'];
}
elseif ($_GET['sort'])
{
    $sort_type = $_GET['sort'];
}

$div = $_GET['division'];
//Sort by most popular

// function to get the id of all the subcategories below the chosen category
function getChildrenID ($idArray, $index, $event_name, $organization, $link)
{
    $query = "SELECT * FROM categories WHERE event_name = '" . $event_name . "' AND organization = '" . $organization . "' AND parent_id = '" . $index . "'";
    $result = mysqli_query($link, $query);
    while ($row = $result->fetch_assoc())
    {
        array_push($idArray, $row["index_cat"]);
        if ($row["has_children"] != 0)
        {
            $idArray =  getChildrenID ($idArray, $row["index_cat"], $event_name, $organization, $link);
        }
    }
    return $idArray;
}

// get all the exhibit ids that are under that division
$exhibit_id = array();

// if the user has selected a category from the side navigation panel
if (!empty($catName) && $catName !== 'Select...')
{
    $catName = explode("?_?", $catName);
    $index_cat = $catName[0];

    // find the category based on the index from the url
    $query = "SELECT * FROM categories WHERE event_name = '" . $event . "' AND organization = '" . $orgName . "' AND index_cat = '" . $index_cat . "'";
    $result = mysqli_query($link, $query);
    $row = $result->fetch_assoc();
    $children_id = array();
    // if it has children get cat_id of the children as well
    if ($row['has_children'] != 0)
    {
        $children_id = getChildrenID ($children_id, $index_cat, $event, $orgName, $link);
    }
    // store all possible division ids in one array
    array_push($children_id, $index_cat);


    for ($i = 0; $i < count($children_id); $i++)
    {
        // get the names of exhibits under that category
        $query = "SELECT exhibit_name FROM users WHERE event_name = '" . $event . "' AND organization = '" . $orgName . "' AND division_name = '" . $children_id[$i]. "'";
        $result = mysqli_query($link, $query);
        while ($row = $result->fetch_assoc())
        {
            if (!empty($row["exhibit_name"]))
            {
                // get the id of those exhibits
                $query = "SELECT exhibit_id FROM exhibits WHERE event= '" . $event . "'  AND title = '" . $row["exhibit_name"]. "'";
                $result_exh = mysqli_query($link, $query);
                if (!empty($row = $result_exh->fetch_assoc()))
                {
                    array_push($exhibit_id, $row["exhibit_id"]);
                }
            }
        }
    }
}

//This will get the results that the user inputs in the search
if (isset($_GET['sort'])) { //check if button is pushed

    $div = $_GET['division'];
    //Store search result in a variable
    $search = $_GET['search'];

    //$query = "SELECT exhibitor, title, exhibit_id, visits, division_name FROM exhibits, users WHERE users.username = exhibits.exhibitor AND exhibits.is_published = 1 AND users.organization = '$organ' ORDER BY RAND()";
    $query = "SELECT exhibitor, title, exhibit_id, visits, division_name FROM exhibits, users WHERE users.username = exhibits.exhibitor AND exhibits.is_published = 1 AND users.event_name = '$event'";

    if ($search !== '') {
        $query = $query . " AND exhibits.title LIKE '%" . $search . "%'";
    }
    //$query = $query . " ORDER BY RAND()";

    $select_exhibits = mysqli_query($link, $query);
    while($result = $select_exhibits->fetch_assoc()) {
        array_push($resultSearch, $result);
    }
}

//Merge Category search & String search
if (count($exhibit_id) > 0) {
    $temp_arr = array();

    for ($i = 0; $i < count($resultSearch); $i++) {
        if (in_array($resultSearch[$i]['exhibit_id'], $exhibit_id)) {
            array_push($temp_arr, $resultSearch[$i]);
        }
    }
    $resultSearch = $temp_arr;
}

?>

<style>
    .grid-container {
        display: grid;
        grid-template-columns: auto auto auto;
        grid-gap: 50px;
        padding: 10px;
    }

    .grid-container > div {
        transition: .5s ease;
        background-color: white;
        text-align: center;
        padding: 20px 0;
        font-size: 30px;
        border-radius: 15px;
        border: 1px solid gray;
        height: 400px;
        overflow: auto;
        cursor: pointer;
        filter: grayscale(0%);
        background-size: cover;
        background-position: center;
    }

    .grid-container > div:hover {
        box-shadow:
                1px 1px #373737,
                2px 2px #373737,
                3px 3px #373737,
                4px 4px #373737,
                5px 5px #373737,
                6px 6px #373737;
        -webkit-transform: translateX(-3px);
        transform: translateX(-3px);
        transition: .5s ease;
    }

    .sub_text_bar {
        background-color: rgba(255, 255, 255, 0.7);
        color = black;
        overflow: hidden;
        position: fixed;
        bottom: 0;
        width: 100%;
        font-size: 20px;
    }
</style>

<div id="wrapper">
    <?php include "includes/user_navigation.php"; ?>


    <div id="page-wrapper">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">

                <?php
                if (!empty($search))
                {
                    echo '<h1 class="page-header">Results for '.$search.'</h1>';
                }
                ?>

                <div class="well">
                    <h4>Exhibit Search</h4>
                    <?php //Connects to new includes/search.php script
                    //need to change so that search.php has same formatting as other
                    //user pages
                    ?>
                    <form action='search.php?exhibit=' method='get'>
                        <input name="sort" value="all" hidden="true"/>
                        <div class="input-group">
                            <input name="search" type="text" class="form-control" value='<?php echo $search ?>'>
                            <span class="input-group-btn">
                                <button name="submit" class="btn btn-default" type="submit">
                                    <span class="glyphicon glyphicon-search"></span>
                                </button>
                            </span>
                        </div>
                        <?
                        $query = "SELECT * FROM categories WHERE event_name = '" . $event . "' AND organization = '" . $orgName . "' AND parent_id = '" . 0 . "'";
                        $result = mysqli_query($link, $query);
                        $repeat = array();

                        $default = $div;
                        if ($default !== 'Select...') {
                            $default = explode('?_?', $default)[1];
                        }

                        echo "<select class='select' name='division' id='category_select' style='margin-top: 10px;'>";
                        echo '<option>' . "Select..." . '</option>';
                        while ($divNames = $result->fetch_assoc()) {
                            $name = $divNames['category'];
                            $index = $divNames["index_cat"];
                            $name = trim($name);
                            if (!in_array($name, $repeat)) {
                                echo '<option value="' . $divNames['index_cat'] . '?_?' . $name . '"' . ($default === $name ? ' selected' : '') . '>' . $name . '</option>';
                                array_push($repeat, $name);
                            }
                        }
                        echo '</select>'; ?>
                        <br><br>
                        <button type="submit" name="sort" class="btn btn-primary" value="all">All Exhibits</button>
                        <button type="submit" name="sort" class="btn btn-primary" value="pop">Most Popular</button>
                        <button type="submit" name="sort" class="btn btn-primary" value="top">Top Rated</button>
                    </form>

                    <form action='exhibitors.php' method='submit'>
                        <button type="submit" class="btn btn-primary">Reset</button>
                    </form>
                </div>

                <div class="grid-container" id="grid">
                    <?php

                    $sort_type = $_GET['sort'];

                    //Move search results into the exhibits array (makes it easier for later)
                    $exhibits = $resultSearch;
                    //print_r($exhibits);

                    //Sort by most popular
                    if ($sort_type === 'pop') {
                        function cmp($a, $b)
                        {
                            return $b['visits']  - $a['visits'];
                        }
                        usort($exhibits, "cmp");
                        #print_r($exhibits);
                    }

                    if ($sort_type === 'top') {
                        /*$query = "SELECT * FROM divisions WHERE event = '" . $event . "' AND organization = '" . $orgName . "'";
                        $result = mysqli_query($link, $query);
                        $repeat = array();
                        echo '<select class="select" name="division" style="margin-top: 10px;">';
                        echo '<option>' . "Select..." . '</option>';
                        while ($divNames = $result->fetch_assoc()) {
                            $name = $divNames['division_name'];
                            $name = trim($name);
                            if (!in_array($name, $repeat)) {
                                echo '<option>' . $name . '</option>';
                                array_push($repeat, $name);
                            }
                        }
                        echo '</select>';
                        */
                        //$div = $_GET['division'];
                        $query = "SELECT * FROM users WHERE event_name = '" . $event . "' AND organization = '" . $orgName . "' AND user_role = '" . "Exhibitor" . "'";
                        $result_exh = mysqli_query($link, $query);

                        $repeat = array();
                        $scores = array();
                        $exh_info = array();
                        $div_info = array();
                        while ($exhibitNames = $result_exh->fetch_assoc()) {
                            $name = trim($exhibitNames['exhibit_name']);
                            if (!in_array($name, $repeat)) {
                                $exh_info[$name] = $exhibitNames['user_id'];
                                $div_info[$name] = $exhibitNames['division_name'];
                                array_push($repeat, $name);
                                $query = "SELECT * FROM judging WHERE event = '" . $event . "' AND organization='" . $orgName . "' AND exhibitname = '" . $name . "'";
                                $result_judge = mysqli_query($link, $query);
                                $totalScore = 0;
                                $judgesVoted = 0;
                                while ($ratings = $result_judge->fetch_assoc()) {
                                    $ratingArr = (explode(", ", $ratings["rating"]));
                                    $num_cat = count($ratingArr);
                                    $totalRating = 0;
                                    for ($j = 0; $j < $num_cat; $j++) {
                                        $totalRating += $ratingArr[$j];
                                    }

                                    // to get average score
                                    $totalScore = $totalScore + $totalRating;
                                    $judgesVoted += 1;

                                }
                                if ($judgesVoted != 0) {
                                    $avgScore = $totalScore / $judgesVoted;
                                    $scores[$name] = $avgScore;
                                } else {
                                    $scores[$name] = 0;
                                }
                                asort($scores);
                            }
                        }

                        //Sort the exhibits array based on the scores array
                        #print_r($exhibits);
                        foreach ($scores as $name => $score) {
                            $index = array_search($name, array_column($exhibits, 'title'));
                            $exhibits = array_merge(array($index => $exhibits[$index]) + $exhibits);
                        }
                    }

                    //print_r($exhibits);
                    /*else if (isset($_POST['most_pop']))
                    {
                        $query = "SELECT * FROM exhibits WHERE event = '" . $event . "'  AND is_published  = '" . 1 . "'";
                        $result = mysqli_query($link, $query);
                        $exh = array();
                        $exh_name = array();
                        while ($row = $result->fetch_assoc())
                        {
                            $exh_id = $row["exhibit_id"];
                            $exh_visits = $row["visits"];
                            $exh[$exh_id] = $exh_visits;
                            $exh_name[$row['title']] = $exh_visits;
                        }
                        arsort($exh);
                        arsort($exh_name);
                        $exh_name = array_keys($exh_name);
                        $exh = array_keys($exh);
                        $num_exh = count($exh_name);
                    }*/
                    ?>
                </div>
            </div>

        </div>
        <!-- /.row -->
        <input type="submit" id="loadMore" name="load_more" class="btn btn-primary" value="Load More Exhibits" onclick="load_more()">
    </div>
    <!-- /.container-fluid -->

</div>



<script>
    function isInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    let exhibits = [];
    let max_exhibits = 12;
    let exhibit_index = 0;

    document.onload = init();

    let load_more_element = document.getElementById("loadMore");
    document.onscroll = function () { if (isInViewport(load_more_element)) { load_more(); } };

    function init() {
        //document.getElementById('category_select').value = '<?php echo $_GET['division']?>'.split('?_?')[1];

        //window.alert('<?php echo $_GET['division']?>'.split('?_?')[1]);

        exhibits = <?php echo json_encode($exhibits); ?>;
        load_more();
    }

    function load_more() {
        for (i = 0; i < max_exhibits && exhibit_index < exhibits.length; i++) {
            create_exhibit(exhibit_index++);
        }
    }

    function create_exhibit(index) {
        //exhibitor, title, exhibit_id, visits, division_name
        var id = exhibits[index]['exhibit_id'];
        var title = exhibits[index]['title'];
        var division = exhibits[index]['division_name'];
        var role = '<?php echo $role; ?>';

        /*var selected_div = '<?php// echo $div; ?>';
        var compare = division.localeCompare(selected_div);
        if (compare == 0)
        {*/
            //Add image as background if there is one available
            var img_link = '';
            var file_path = '/exhibit-page/exhibit_texts/' + id + '_published.txt';
            var file = loadFile(file_path);
            if (file !== null) {
                var start = file.indexOf("Image{[&;}]");
                if (start >= 0) {
                    start += "Image{[&;}]".length;
                    var end = file.indexOf("{[&;}]", start);
                    if (end >= 0) {
                        img_link = file.substring(start, end);
                    }
                }
            }

            if (img_link === '') {
                img_link = 'https://www.expoexpress.online/exhibit-page/example_types/Example_Image.jpg';
            }
            img_link.replace(' ', '%20');

            var parent = document.getElementById("grid");
            var div = document.createElement("DIV");
            div.onclick = function() { location.href = "../exhibit-page/Display_exhibit.php?p_id=" + id; };
            div.style.backgroundImage = "url('" + img_link + "')";
            //div.style.width = '200px';
            //div.style.height = '200px';

            var sub_div = document.createElement("DIV");
            sub_div.className = 'sub_text_bar';
            sub_div.innerText = title;
            div.appendChild(sub_div);

            //temp Fix
            var judge_division = '<?php echo $judge_division; ?>';
            if (role === "Judge" || role === "Organizer") {
                if (judge_division === division)
                {
                    var form = document.createElement("FORM");
                    form.setAttribute("action", "../user_home/judging.php?p_id=" + id);
                    form.setAttribute("method", "post");
                    var input = document.createElement("INPUT");
                    input.className = 'btn btn-primary';
                    input.setAttribute("type", "submit");
                    input.setAttribute("value", "Judge");
                    input.style.verticalAlign = "super";
                    input.style.fontSize = "15px";
                    form.appendChild(input);
                    sub_div.appendChild(form);
                }
            }
            parent.appendChild(div);
        //}

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
</script>

<!-- /#page-wrapper -->
<?php include "includes/user_footer.php"; ?>





