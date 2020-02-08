<?php

//CAPTCHA Matching code
session_start();

if ($_SESSION['captcha_code'] == $_POST['captcha']) {
    echo 'true';
} else {
    echo 'false';
}
exit;
