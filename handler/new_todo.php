<?php
session_start();

$todoName = $_POST['todo_name'] ?? ''; 
$todoName = trim($todoName);
$userId = $_SESSION['user_current'] ?? '';

if($todoName) {
    if(file_exists('../todo.json')) {
        $json = file_get_contents('../todo.json');
        $jsonArr = json_decode($json, true);
    } else {
        $jsonArr = [];
    }
    // $errors = [];
    // foreach($jsonArr as $key => $value) {
    //     if($key === $todoName) {
    //         $errors[] = 'This name is already on the list';
    //     }
    // }
    $newItem = [$todoName => ['completed' => false, 'startDay' => date("Y/m/d"), 'finishDay' => '?', 'dueDay' => '?', 'userId' => $userId]];

    $jsonArr = array_merge($newItem, $jsonArr);
    file_put_contents('../todo.json', json_encode($jsonArr, JSON_PRETTY_PRINT));
}

header('Location: ../index.php');


