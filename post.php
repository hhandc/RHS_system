<?php
session_start();
if (isset($_SESSION['name'])) {
    if (isset($_POST['text'])) {
        // Handle text message
        date_default_timezone_set('Asia/Seoul');
        $text = stripslashes(htmlspecialchars($_POST['text']));
        $text_message = "<div class='msgln'><span class='chat-time'>" . date("g:i A") . "</span> <b class='user-name'>" . $_SESSION['name'] . "</b>: " . $text . "<br></div>";
        file_put_contents("log.html", $text_message, FILE_APPEND | LOCK_EX);
    } elseif (isset($_POST['image'])) {
        // Handle image message
        date_default_timezone_set('Asia/Seoul');
        $image = $_POST['image']; // Expecting an image path (replace with actual logic to upload and handle images)
        $image_message = "<div class='msgln'><span class='chat-time'>" . date("g:i A") . "</span> <b class='user-name'>" . $_SESSION['name'] . "</b>: <img src='" . $image . "' alt='Image' class='chatimg' /><button>show message</button><br></div>";
        file_put_contents("log.html", $image_message, FILE_APPEND | LOCK_EX);
    }
}
?>