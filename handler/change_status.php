<?php

$json = file_get_contents('../todo.json');
$jsonArr = json_decode($json, true);

$todoName = $_POST['todo_name'];
$jsonArr[$todoName]['completed'] = !$jsonArr[$todoName]['completed'];

if($jsonArr[$todoName]['completed']) {
    $jsonArr[$todoName]['finishDay'] = date("Y/m/d");
} else {
    $jsonArr[$todoName]['finishDay'] = '?';
}


file_put_contents('../todo.json', json_encode($jsonArr, JSON_PRETTY_PRINT));

header('Location: ../index.php');