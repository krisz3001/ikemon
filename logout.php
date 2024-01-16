<?php 

    session_start();

    define("GUARD", 1);

    include_once("lib/userStorage.php");
    include_once("lib/auth.php");

    $userStorage = new UserStorage();

    $auth = new Auth($userStorage);
    $auth->logout();
    
    redirect("/");
?>