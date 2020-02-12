<?php

unset($_SESSION['user']);
echo "<script>location.href='".ROOT_URL."auth/?page=login';</script>";
exit;

?>