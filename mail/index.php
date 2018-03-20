<?php
$to = "vishal@stagebit.com";
$subject = "My subject";
$txt = "Hello world!";
$headers = "From: webmaster@example.com" . "\r\n";

var_dump(mail($to,$subject,$txt,$headers));
?>