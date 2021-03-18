<?php

$cmd = "C:\\xampp\\php\\php.exe " . ROOT_PATH . "jobs\\generate-images.php";
pclose(popen("start /B ". $cmd, "w")); 
echo 'started';
