<?php
$userId = $_POST['user_id'] ?? ''; 
$name = $_POST['user_name'] ?? ''; 
$email = $_POST['user_email'] ?? ''; 
$password = $_POST['user_password'] ?? ''; 

if($userId) {
    if(file_exists('../users.json')) {
        $json = file_get_contents('../users.json');
        $jsonArr = json_decode($json, true);
    } else {
        $jsonArr = [];
    }
    if(!empty(array_key_last($jsonArr))) {
        $userId = (int)array_key_last($jsonArr);
        $userId++;
    }
    var_dump($userId); 
    $jsonArr[$userId] = ['name' => $name, 'email' => $email, 'password' => $password];
    file_put_contents('../users.json', json_encode($jsonArr, JSON_PRETTY_PRINT));
}

header('Location: ../login.php');


