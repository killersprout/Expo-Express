<?php

function searchTxt($search){
    global $link;
    // What to look for
    $searchFile = $search;
    // Read from file
    $lines = file('file.txt');
    foreach($lines as $line)
    {
        // Check if the line contains the string we're looking for, and print if it does
        if(strpos($line, $searchFile) !== false)
            echo "Found";
    }
}


function confirm($result){ //Confirms the query
    global $link;
    if(!$result){
        die("Query Failed". mysqli_error($link));
    }

}




