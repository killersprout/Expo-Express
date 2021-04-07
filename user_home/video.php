<?php
ob_start();
session_start();
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
include "includes/user_header.php";

// if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ./register");
    exit;
}

$userid = $_SESSION["user_id"];
$role = $_SESSION['user_role'];

if(isset($_GET['p_id'])){
    $id= $_GET['p_id'];
}

?>
<script src='https://cdn.scaledrone.com/scaledrone.min.js'></script>
<meta charset="utf-8">
<title>Video</title>
<meta name="viewport" content="width=device-width">
<style>

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

<script>
    //Roomcode
    if (!location.hash) {
        location.hash = <?php echo json_encode($id,JSON_FORCE_OBJECT) ;?>;
    }

    const roomHash = location.hash.substring(1);

    // Your code here
    const drone = new ScaleDrone(''); 

    // Room name needs to be prefixed with 'observable-'
    const roomName = 'observable-expoexpress' + roomHash;
    //const roomName = 'observable-expoexpress';
    const configuration = {
        iceServers: [{
            urls: 'stun:stun.l.google.com:19302'
        }]
    };
    let room;
    let pc;

    function onSuccess() {};

    function onError(error) {
        console.error(error);
    };

    drone.on('open', error => {
        if (error) {
            return console.error(error);
        }
        room = drone.subscribe(roomName);
        room.on('open', error => {
            if (error) {
                onError(error);
            }
        });
        // We're connected to the room and received an array of 'members'
        // connected to the room (including us). Signaling server is ready.
        room.on('members', members => {
            console.log('MEMBERS', members);
            // If we are the second user to connect to the room we will be creating the offer
            const isOfferer = members.length === 2;
            startWebRTC(isOfferer);
        });
    });

    // Send signaling data via Scaledrone
    function sendMessage(message) {
        drone.publish({
            room: roomName,
            message
        });
    }

    function startWebRTC(isOfferer) {
        pc = new RTCPeerConnection(configuration);

        // 'onicecandidate' notifies us whenever an ICE agent needs to deliver a
        // message to the other peer through the signaling server
        pc.onicecandidate = event => {
            if (event.candidate) {
                sendMessage({'candidate': event.candidate});
            }
        };

        // If user is offerer let the 'negotiationneeded' event create the offer
        if (isOfferer) {
            pc.onnegotiationneeded = () => {
                pc.createOffer().then(localDescCreated).catch(onError);
            }
        }

        // When a remote stream arrives display it in the #remoteVideo element
        pc.onaddstream = event => {
            remoteVideo.srcObject = event.stream;
        };


        navigator.mediaDevices.getUserMedia({
            audio: true,
            video: true,
        }).then(stream => {
            // Display your local video in #localVideo element
            localVideo.srcObject = stream;
            // Add your stream to be sent to the conneting peer
            pc.addStream(stream);
        }, onError);

        // Listen to signaling data from Scaledrone
        room.on('data', (message, client) => {
            // Message was sent by us
            if (client.id === drone.clientId) {
                return;
            }

            if (message.sdp) {
                // This is called after receiving an offer or answer from another peer
                pc.setRemoteDescription(new RTCSessionDescription(message.sdp), () => {
                    // When receiving an offer lets answer it
                    if (pc.remoteDescription.type === 'offer') {
                        pc.createAnswer().then(localDescCreated).catch(onError);
                    }
                }, onError);
            } else if (message.candidate) {
                // Add the new ICE candidate to our connections remote description
                pc.addIceCandidate(
                    new RTCIceCandidate(message.candidate), onSuccess, onError
                );
            }
        });
    }

    function localDescCreated(desc) {
        pc.setLocalDescription(
            desc,
            () => sendMessage({'sdp': pc.localDescription}),
            onError
        );
    }


</script>

<div id="wrapper">

    <!-- Navigation -->
    <?php include "includes/user_navigation.php"; ?>

    <div id="page-wrapper">

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    <?php echo " " . $role . ", " .$_SESSION['user_firstname'] . " " .$_SESSION['user_lastname'];?>
                </h1>

                <div  class="wrapper">
                    <!--
                    <div class="copy">Send your URL to start a video call</div>
                    -->

                    <video id="localVideo" autoplay muted></video>
                    <video id="remoteVideo" autoplay></video>
                    <!--
                     How do we give this to the other person?
                     We can post on the exhibit page for only judge and exhibitors to see?
                     -->
                    <h1>Room code: <?php echo $id; ?> </h1>

                </div>

            </div>
            <!-- /.row -->

        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /#page-wrapper -->
    <?php include "includes/user_footer.php"; ?>
