<?php

$json = file_get_contents('../todo.json');
$jsonArr = json_decode($json, true);

var_dump($_POST);


$todoName = $_POST['todo_name'];
$todoDue = $_POST['todo_due'];

if($todoDue === '') {
    $jsonArr[$todoName]['dueDay'] = '?';
} else {
    $jsonArr[$todoName]['dueDay'] = date("Y/m/d", strtotime($todoDue));
}

file_put_contents('../todo.json', json_encode($jsonArr, JSON_PRETTY_PRINT));

header('Location: ../index.php');