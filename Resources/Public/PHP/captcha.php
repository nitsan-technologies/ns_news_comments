<?php

session_name('ns_news_comments_captcha');
session_start();
include('phptextClass.php');
/*create class object*/
$phptextObj = new phptextClass();
/*phptext function to genrate image with text*/
$phptextObj->phpcaptcha('#162453', '#fff', 120, 40, 10, 25);
