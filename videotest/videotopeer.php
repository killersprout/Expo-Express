<!DOCTYPE html>
<html>
<head>
    <script src='https://cdn.scaledrone.com/scaledrone.min.js'></script>
    <meta charset="utf-8">
    <title>Video Test</title>
    <meta name="viewport" content="width=device-width">
    <style>
        body {
            background: grey;
            display: flex;
            height: 100vh;
            margin: 0;
            align-items: center;
            justify-content: center;
            padding: 0 50px;
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
        }
        video {
            max-width: calc(50% - 100px);
            margin: 0 50px;
            box-sizing: border-box;
            border-radius: 2px;
            padding: 0;
            background: black;
        }
        .copy {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 16px;
            color: white;
        }
    </style>
</head>
<body>

    <div class="copy">Send your URL to start a video call</div>

        <video id="localVideo" autoplay muted></video>
        <video id="remoteVideo" autoplay></video>

        <script src="script.js"></script>


</body>
<footer class="">
    <a href="epasswordReset.php" class="">Reset Your Password</a><br>
    <a href="elogout.php" class="">Sign Out of Your Account</a><br>
    <a href="../eregister/ewelcome.php" class="">Return to Home Page</a>
</footer>
</html>