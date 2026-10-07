<?php

require_once __DIR__.'/../includes/functions.php';log_action('logout', 'Administrator logged out');$_SESSION=[];session_destroy();redirect('index.php');
