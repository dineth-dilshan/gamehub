<?php
session_start();
session_destroy();
header('Location: storehome.html');
exit;
?>
