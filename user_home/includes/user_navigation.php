<?php
//Get logged in user
//session_start();

$query = "SELECT * FROM users WHERE user_id='" . $_SESSION['user_id'] . "' AND organization='" . $_SESSION['organization'] . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$role = $row["user_role"];

//Array structure:
//Title, Function (condition), Array, id
//  Title, href, Function (condition)

$items = array();
array_push($items, array('Create & View Exhibit', function($session) { return $session['user_role'] === 'Exhibitor' || $session['user_role'] === 'Organizer'; }, array(), 'demo'));
    array_push($items[0][2], array('Template Selection', 'https://www.expoexpress.online/exhibit-page/Detect_templates.php', function($session) { return $session['user_role'] === 'Organizer'; }));
    array_push($items[0][2], array('Create New Blank Exhibit', 'https://www.expoexpress.online/exhibit-page/Empty_exhibit.php', function($session) { return true; }));
    array_push($items[0][2], array('Create Copy of Published Exhibit', 'https://www.expoexpress.online/exhibit-page/Sub_Edit_Exhibit.php', function($session) { return file_exists(getcwd() . '/../exhibit-page/exhibit_texts/' . $session['user_id'] . "_published.txt"); }));
    array_push($items[0][2], array('Edit Current Draft', 'https://www.expoexpress.online/exhibit-page/File_upload.php', function($session) { return file_exists(getcwd() . '/../exhibit-page/exhibit_texts/' . $session['user_id'] . "_draft.txt"); }));
    array_push($items[0][2], array('View Your Page', 'https://www.expoexpress.online/exhibit-page/Display_exhibit.php?p_id=' . $_SESSION['user_id'], function($session) { return file_exists(getcwd() . '/../exhibit-page/exhibit_texts/' . $session['user_id'] . "_published.txt"); }));

array_push($items, array('Voting', function($session) { return true; }, array(), 'voting_dropdown'));
    array_push($items[1][2], array('Manage Judges', 'div_manage_judge.php', function($session) { return $session['user_role'] == 'Organizer'; }));
    array_push($items[1][2], array('Add Categories for Exhibits', 'add_expo_divisions.php', function($session) { return $session['user_role'] == 'Organizer'; }));
    array_push($items[1][2], array('Add Voting Categories', 'add_voting_questions.php', function($session) { return $session['user_role'] == 'Organizer'; }));
    array_push($items[1][2], array('Add Judging Categories', 'add_judging_questions.php', function($session) { return $session['user_role'] == 'Organizer'; }));
    array_push($items[1][2], array('Vote for Exhibits', 'voting.php', function($session) { return true; }));
    array_push($items[1][2], array('Judge Exhibits', 'judging.php', function($session) { return true; }));
    array_push($items[1][2], array('View Voting Results', 'voting_results.php', function($session) { return true; }));
    array_push($items[1][2], array('View Judging Results', 'div_judging_results.php', function($session) { return true; }));

//Special case for organizer
if ($_SESSION['user_role'] == 'Organizer') {
    $items[1][0] = 'Manage';
}

?>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php" style="color: black">Expo Express</a>
    </div>


    <!-- Top Menu Items -->
    <ul class="nav navbar-right top-nav">

        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color: gray"><i class="fa fa-user "></i><?php echo $_SESSION['user_firstname']; ?><b class="caret"></b></a>
            <ul class="dropdown-menu">
                <!--
                <li>
                    <a href="#"><i class="fa fa-fw fa-user"></i> ??</a>
                </li>

                <li class="divider"></li>

                <li>
                -->
                    <a href="../register/logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                </li>
            </ul>
        </li>
    </ul>



    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">
            <li>
                <a href="index.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
            </li>

            <li>
                <a href="./exhibitors.php?sort=all">View Exhibits</a>
            </li>

            <?php

            foreach ($items as $i => $item) {

                if ($item[1]($_SESSION)) {
                    echo "<li>";
                    echo "<a href='javascript:;' data-toggle='collapse' data-target=#" . "$item[3]><i class='fa fa-fw fa-arrows-v'></i> $item[0] <i class='fa fa-fw fa-caret-down'></i></a>";
                    echo "<ul id='$item[3]' class='collapse'>";

                    foreach ($item[2] as $sub_item) {

                        if ($sub_item[2]($_SESSION)) {
                            echo "<li>";
                            echo "<a href='$sub_item[1]'><i class='fa fa-fw fa-wrench'></i> $sub_item[0]</a>";
                            echo "</li>";
                        }
                    }

                    echo "</ul>";
                    echo "</li>";
                }
            }

            //Only Exhibitors and Organizers can edit, preview and see personal exhibit pages.
            /*if( $role == 'Exhibitor' || $role == 'Organizer') {

                echo "<li>";
                echo "<a href='javascript:;' data-toggle='collapse' data-target='#demo'><i class='fa fa-fw fa-arrows-v'></i> Create Exhibit  & View <i class='fa fa-fw fa-caret-down'></i></a>";
                echo "<ul id='demo' class='collapse'>";

                    if($role == 'Organizer'){
                        echo "<li>";
                        echo "<a href='https://www.expoexpress.online/exhibit-page/Template_selection.php'><i class='fa fa-fw fa-wrench'></i> Template Selection</a>";
                        echo "</li>";
                    }

                    echo "<li>";
                    echo "<a href='https://www.expoexpress.online/exhibit-page/Empty_exhibit.php'><i class='fa fa-fw fa-wrench'></i> Create New Blank Exhibit</a>";
                    echo "</li>";

                    if(file_exists(getcwd() . '/../exhibit-page/exhibit_texts/' . $_SESSION['user_id'] . "_published.txt")) {
                        echo "<li>";
                        echo "<a href='https://www.expoexpress.online/exhibit-page/Sub_Edit_Exhibit.php'><i class='fa fa-fw fa-wrench'></i> Create Copy of Published Exhibit</a>";
                        echo "</li>";
                    }

                    if(file_exists(getcwd() . '/../exhibit-page/exhibit_texts/' . $_SESSION['user_id'] . "_draft.txt")) {
                        echo "<li>";
                        echo "<a href='https://www.expoexpress.online/exhibit-page/File_upload.php'><i class='fa fa-fw fa-wrench'></i> Edit Current Draft</a>";
                        echo "</li>";
                    }

                    if(file_exists(getcwd() . '/../exhibit-page/exhibit_texts/' . $_SESSION['user_id'] . "_published.txt")) {
                        echo "<li>";
                        echo "<a href='https://www.expoexpress.online/exhibit-page/Display_exhibit.php'><i class='fa fa-fw fa-wrench'></i> View Your Page</a>";
                        echo "</li>";
                    }

                echo "</ul>";
                echo "</li>";

            }*/
            ?>

            <?php
                /*echo '<li>';
                echo '<a href="javascript:;" data-toggle="collapse" data-target="#voting_dropdown"><i class="fa fa-fw fa-arrows-v"></i> Voting <i class="fa fa-fw fa-caret-down"></i></a>';
                echo '<ul id="voting_dropdown" class="collapse">';

                    if ($role == 'Organizer')
                    {
                        echo "<li>";
                        echo "<a href='div_manage_judge.php'>Manage Judges</a>";
                        echo "</li>";
                        echo "<li>";
                        echo "<a href='add_expo_divisions.php'>Add Categories</a>";
                        echo "</li>";
                        echo "<li>";
                        echo "<a href='add_voting_questions.php'>Add Voting Questions</a>";
                        echo "</li>";
                        echo "<li>";
                        echo "<a href='add_judging_questions.php'>Add Judging Categories</a>";
                        echo "</li>";
                    }

                    echo "<li>";
                    echo "<a href='voting.php'>Vote for Exhibits</a>";
                    echo "</li>";

                    echo "<li>";
                    echo "<a href='judging.php'>Judge Exhibits</a>";
                    echo "</li>";

                    echo "<li>";
                    echo "<a href='voting_results.php'>View Voting Results</a>";
                    echo "</li>";

                    echo "<li>";
                    echo "<a href='div_judging_results.php'>View Judging Results</a>";
                    echo "</li>";

                echo "</ul>";
                echo "</li>";
                        */
            ?>


                    <li>
                        <a href="javascript:;" data-toggle="collapse" data-target="#exhibit_dropdown"><i class="fa fa-fw fa-arrows-v"></i> Categories <i class="fa fa-fw fa-caret-down"></i></a>
                        <ul id="exhibit_dropdown" class="collapse">
                            <?php

                                // function to create array with table contents
                            function getCatDataNav ($orgName, $eventName, $link)
                            {
                                //multi-dimensional array
                                $data = array();

                                $query = "SELECT * FROM categories WHERE organization='" . $orgName . "' AND event_name='" . $eventName . "'";
                                $result = mysqli_query($link, $query);
                                // for every category in the event
                                while ($row = $result->fetch_assoc())
                                {
                                    // create array with information about category to add to data array
                                    $catInfo = array();
                                    array_push($catInfo, $row['index_cat']);
                                    array_push($catInfo, $row['parent_id']);
                                    array_push($catInfo, $row['category']);
                                    array_push($catInfo, $row['has_children']);

                                    array_push($data, $catInfo);
                                }
                                return $data;
                            }

                            function generateTreeNav ($data, $depth, $parent_id)
                            {
                                if (empty($data))
                                {
                                    return "";
                                }
                                $tree = '<ul>';
                                for ($i=0, $ni=count($data); $i < $ni; $i++) {
                                    if ($data[$i][1] == $parent_id)
                                    {
                                        $tree .= '<li>';
                                        //$tree .= "<a href='https://www.expoexpress.online/user_home/exhibitors.php?link=" . $data[$i][0]."?_?".$data[$i][2] . "' data-toggle='collapse' data-target='#'>".$data[$i][2]."</a>";
                                        $tree .= "<a href='https://www.expoexpress.online/user_home/search.php?sort=all&search=&submit=&division=" . $data[$i][0]."?_?".$data[$i][2] . "'" . "data-toggle='collapse' data-target='#'>".$data[$i][2]."</a>";


                                        $tree .= generateTreeNav($data, $depth+1, $data[$i][0]);
                                        $tree .= '</li>';
                                    }
                                }
                                $tree .= '</ul>';
                                return $tree;
                            }

                                $data = getCatDataNav ($_SESSION['organization'], $_SESSION['event_name'], $link);

                                $tree = generateTreeNav ($data, 0, 0);

                                echo $tree;

                            ?>
                        </ul>
                    </li>

            <li class="active">
                <a href="chat.php"><i class="fa fa-fw fa-file"></i> Live Chat </a>
            </li>


        </ul>
    </div>


    <!-- /.navbar-collapse -->
</nav>