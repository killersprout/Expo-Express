<?php

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

?>
<script type='text/javascript' src='https://cdn.scaledrone.com/scaledrone.min.js'></script>
<meta charset="utf-8">
<title>Chat Test</title>
<meta name="viewport" content="width=device-width">

<style>
    body {
        box-sizing: border-box;
        margin: 0;
        padding: 13px;
        display: flex;
        flex-direction: column;
        max-height: 100vh;
        font-family: -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .members-count,
    .members-list,
    .messages {
        border: 1px solid #e4e4e4;
        padding: 15px;
        margin-bottom: 15px;
    }

    .messages {
        flex-shrink: 1;
        overflow: auto;
    }

    .message {
        padding: 5px 0;
    }
    .message .member {
        display: inline-block;
    }

    .member {
        padding-right: 10px;
        position: relative;
    }

    .message-form {
        display: flex;
        flex-shrink: 0;
    }
    .message-form__input {
        flex-grow: 1;
        border: 1px solid #dfdfdf;
        padding: 10px 15px;
        font-size: 16px;
    }
    .message-form__button {
        margin: 10px;
    }
</style>




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
                    <div class="members-count">-</div>
                    <div class="messages"></div>
                    <form class="message-form" onsubmit="return false;">
                        <input class="message-form__input" placeholder="Type a message.." type="text"/>
                        <input class="message-form__button" value="Send" type="submit"/>

                    </form>

                </div>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
    <script>
        // PS! Replace this with your own channel ID
        // If you use this channel ID your app will stop working in the future
        const CLIENT_ID = ' ';

        const drone = new ScaleDrone(CLIENT_ID, {
            data: { // Will be sent out as clientData via events
                name: getUserName(),
                color: getRandomColor(),
            },
        });

        let members = [];

        drone.on('open', error => {
            if (error) {
                return console.error(error);
            }
            console.log('Successfully connected to Scaledrone');

            const room = drone.subscribe('observable-room');
            room.on('open', error => {
                if (error) {
                    return console.error(error);
                }
                console.log('Successfully joined room');
            });

            room.on('members', m => {
                members = m;
                updateMembersDOM();
            });

            room.on('member_join', member => {
                members.push(member);
                updateMembersDOM();
            });

            room.on('member_leave', ({id}) => {
                const index = members.findIndex(member => member.id === id);
                members.splice(index, 1);
                updateMembersDOM();
            });

            room.on('data', (text, member) => {
                if (member) {
                    addMessageToListDOM(text, member);
                } else {
                    // Message is from server
                }
            });
        });

        drone.on('close', event => {
            console.log('Connection was closed', event);
        });

        drone.on('error', error => {
            console.error(error);
        });

        /*
        function getRandomName() {
            const adjs = ["autumn", "hidden", "bitter", "misty", "silent", "empty", "dry", "dark", "summer", "icy", "delicate", "quiet", "white", "cool", "spring", "winter", "patient", "twilight", "dawn", "crimson", "wispy", "weathered", "blue", "billowing", "broken", "cold", "damp", "falling", "frosty", "green", "long", "late", "lingering", "bold", "little", "morning", "muddy", "old", "red", "rough", "still", "small", "sparkling", "throbbing", "shy", "wandering", "withered", "wild", "black", "young", "holy", "solitary", "fragrant", "aged", "snowy", "proud", "floral", "restless", "divine", "polished", "ancient", "purple", "lively", "nameless"];
            const nouns = ["waterfall", "river", "breeze", "moon", "rain", "wind", "sea", "morning", "snow", "lake", "sunset", "pine", "shadow", "leaf", "dawn", "glitter", "forest", "hill", "cloud", "meadow", "sun", "glade", "bird", "brook", "butterfly", "bush", "dew", "dust", "field", "fire", "flower", "firefly", "feather", "grass", "haze", "mountain", "night", "pond", "darkness", "snowflake", "silence", "sound", "sky", "shape", "surf", "thunder", "violet", "water", "wildflower", "wave", "water", "resonance", "sun", "wood", "dream", "cherry", "tree", "fog", "frost", "voice", "paper", "frog", "smoke", "star"];
            return (
                adjs[Math.floor(Math.random() * adjs.length)] +
                "_" +
                nouns[Math.floor(Math.random() * nouns.length)]
            );
        }
         */

        function getUserName() {
          var userName = <?php echo json_encode($_SESSION['username'],JSON_HEX_TAG); ?>;
          return userName;
        }

        function getRandomColor() {
            return '#' + Math.floor(Math.random() * 0xFFFFFF).toString(16);
        }

        //------------- DOM STUFF

        const DOM = {
            membersCount: document.querySelector('.members-count'),
            membersList: document.querySelector('.members-list'),
            messages: document.querySelector('.messages'),
            input: document.querySelector('.message-form__input'),
            form: document.querySelector('.message-form'),
        };

        DOM.form.addEventListener('submit', sendMessage);

        function sendMessage() {
            const value = DOM.input.value;
            if (value === '') {
                return;
            }
            DOM.input.value = '';
            drone.publish({
                room: 'observable-room',
                message: value,
            });
        }

        function createMemberElement(member) {
            const { name, color } = member.clientData;
            const el = document.createElement('div');
            el.appendChild(document.createTextNode(name));
            el.className = 'member';
            el.style.color = color;
            return el;
        }

        function updateMembersDOM() {
            DOM.membersCount.innerText = `${members.length} users in room:`;
            DOM.membersList.innerHTML = '';
            members.forEach(member =>
                DOM.membersList.appendChild(createMemberElement(member))
            );
        }

        function createMessageElement(text, member) {
            const el = document.createElement('div');
            el.appendChild(createMemberElement(member));
            el.appendChild(document.createTextNode(text));
            el.className = 'message';
            return el;
        }

        function addMessageToListDOM(text, member) {
            const el = DOM.messages;
            const wasTop = el.scrollTop === el.scrollHeight - el.clientHeight;
            el.appendChild(createMessageElement(text, member));
            if (wasTop) {
                el.scrollTop = el.scrollHeight - el.clientHeight;
            }
        }

    </script>
<?php include "includes/user_footer.php"; ?>
