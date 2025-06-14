<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    $logout_message = "<div class='msgln'><span class='left-info'>User <b class='user-name-left'>" . $_SESSION['name'] . "</b> has left the chat session.</span><br></div>";
    file_put_contents("log.html", $logout_message, FILE_APPEND | LOCK_EX);
    session_destroy();
    header("Location: index.php");
    exit;
}

// Handle login
if (isset($_POST['enter'])) {
    if ($_POST['name'] != "") {
        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
        header("Location: index.php");
        exit;
    } else {
        echo '<span class="error">Please type in a name</span>';
    }
}

// Display login form if not logged in
function loginForm()
{
    echo
    '<div id="loginform">
        <p>Type in a name!</p>
        <form action="index.php" method="post">
            <label for="name">Name &mdash;</label>
            <input type="text" name="name" id="name" />
            <input type="submit" name="enter" id="enter" value="Enter" />
        </form>
    </div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Chat Application">
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // Send message
            $("#submitmsg").click(function () {
                var clientmsg = $("#usermsg").val();
                var formData = new FormData();

                var words = clientmsg.split(" ");
                const forbiddenWords = ["fuck", "shit", "bitch", "nigger", "chink", "your mom", "ass"];
                var found = false;

                for (let i = 0; i < forbiddenWords.length; i++) {
                    if (clientmsg.toLowerCase().includes(forbiddenWords[i])) {
                        found = true;
                        break;
                    }
                }

                if (found) {
                    var check = confirm("Sending this message may hurt the recipient. Are you sure you want to proceed?");
                    if (check) {
                        var imagePath = "image.png"; // Default image path for flagged messages
                        $.post("post.php", {image: imagePath});
                        $("#usermsg").val("");
                    }
                } else {
                    $.post("post.php", { text: clientmsg });
                    $("#usermsg").val("");
                }
                return false;
            });

            // Load chat log
            function loadLog() {
                var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20;

                $.ajax({
                    url: "log.html",
                    cache: false,
                    success: function (html) {
                        $("#chatbox").html(html);
                        var newscrollHeight = $("#chatbox")[0].scrollHeight - 20;
                        if (newscrollHeight > oldscrollHeight) {
                            $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal');
                        }
                    }
                });
            }

            setInterval(loadLog, 500);

            // Exit chat
            $("#exit").click(function () {
                var exit = confirm("Are you sure you want to end the session?");
                if (exit == true) {
                    window.location = "index.php?logout=true";
                }
            });
        });
    </script>
</head>
<body>
    <?php
    if (!isset($_SESSION['name'])) {
        loginForm();
    } else {
    ?>
        <div id="wrapper">
            <div id="menu">
                <p class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></p>
                <p class="logout"><a id="exit" href="#">Exit Chat</a></p>
            </div>
            <div id="chatbox">
                <?php
                if (file_exists("log.html") && filesize("log.html") > 0) {
                    $contents = file_get_contents("log.html");
                    echo $contents;
                }
                ?>
            </div>
            <form name="message" action="">
                <input name="usermsg" type="text" id="usermsg" />
                <input name="submitmsg" type="submit" id="submitmsg" value="Send" />
            </form>
        </div>
    <?php
    }
    ?>
</body>
</html>