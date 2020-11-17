<?php

?>

<!DOCTYPE html>
<html>

<head>
    <title>Graphics Test</title>
</head>

<body>
    <canvas id="myCanvas" width="200" height="100" style="border:1px solid #000000;">
        Your browser does not support the HTML canvas tag.
    </canvas>

    <script>
        var c = document.getElementById("myCanvas");
        var ctx = c.getContext("2d");
        ctx.moveTo(0,0);
        ctx.lineTo(200,100);
        ctx.stroke();
    </script>
</body>

</html>