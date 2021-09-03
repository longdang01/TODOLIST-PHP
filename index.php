<?php
    // //Mail
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';

    $mail = new PHPMailer(true);
    //Server settings
    $mail->SMTPDebug = 0;                      
    $mail->isSMTP();                           
    $mail->Host       = 'smtp.gmail.com';      
    $mail->SMTPAuth   = true;                                   
    $mail->Username   = 'danglong2407@gmail.com';                   
    $mail->Password   = 'danghoanglong24';                            
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;          
    $mail->Port       = 465;                                     
    
    session_start();
    //App
    if(file_exists('todo.json') && file_exists('users.json')) {
        $json = file_get_contents('todo.json');
        $todos = json_decode($json, true);

        $jsonUser = file_get_contents('users.json');
        $users = json_decode($jsonUser, true);

        /* Check change Password */
        $oldPass = $_POST['old_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';        
        
        $useCurrent = $_SESSION['user_current'] ?? '';
        $keyUser = array_filter(
            array_keys($users),
            function($key) use ($useCurrent){
                return $key === $useCurrent;
        });
        
        $currentUser = array_intersect_key($users,array_flip($keyUser));
        
        $errors = [];
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if($oldPass != '' && $oldPass != $currentUser[$useCurrent]['password']) {
                $errors[] = "wrong old password";
                echo '<script language="javascript">';
                echo 'alert("wrong old password")';
                echo '</script>';
            } else if($newPass != '' && $confirmPass != '' && $confirmPass != $newPass) {
                $errors[] = "wrong new password";
                echo '<script language="javascript">';
                echo 'alert("wrong new password")';
                echo '</script>';
            } else {
                foreach ($errors as $key =>$item){
                    unset($item[$key]);
                }

                $users[$useCurrent]['password'] = $newPass;

                file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
            }
        }
        /** */

        /* Mail */
        $mail->isHTML(true);
        $mail->setFrom('danglong2407@gmail.com', 'Simp Todo');

        $keysId = [];
        foreach($todos as $key => $value) {
            $keysId[] = $todos[$key]['userId'];
        }   
        $keysId = array_unique($keysId);

        foreach($keysId as $k) {
            $tasks = [];
            $newKey = '';
            $mail->ClearAllRecipients();
            foreach($todos as $key => $value) {
                if($todos[$key]['userId'] == $k && $todos[$key]['dueDay'] == date("Y/m/d")) {
                    $newKey = $k;
                    $tasks[] = $key;   
                }
            }
            if($useCurrent == 1 && count($tasks) != 0) {
                $mail->Subject = "Today's work";
                $mail->addAddress($users[$newKey]['email'], $users[$newKey]['name']);
                $mail->Body    = "Today, you have tasks:  ".implode(",",$tasks);
                $mail->Send();
            }        
        }
        
        /** */

        $todos = array_filter($todos, function($value) {
            return $value['userId'] == $_SESSION['user_current'];
        });

        $type = ($_POST['todo_type']) ?? ((isset($_SESSION['type_task'])) ? $_SESSION['type_task'] : 'All');
        $_SESSION['type_task'] = $type;

        switch ($type) {
            // case 'All': 
            //     // $todos = json_decode($json, true);
            //     break;
            case 'Due Today':
                $todos = array_filter($todos, function($value) {
                    return $value['dueDay'] == date("Y/m/d");
                });
                break;
            case 'Completed':
                $todos = array_filter($todos, function($value) {
                    return $value['completed'] == true;
                });
                break;
            case 'Uncompleted':
                $todos = array_filter($todos, function($value) {
                    return $value['completed'] == false;
                });
                break;
        }

        $todoSearch = $_POST['todo_search'] ?? '';
        if($todoSearch != '') {
            $allowed=array_filter(
                array_keys($todos),
                function($key) use ($todoSearch){
                    return stristr($key,$todoSearch);
            });
           
            $todos = array_intersect_key($todos,array_flip($allowed));
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simp Todo</title>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="./dist/css/app.css">
</head>
<body>
    <div class="app">
        <div class="app-container">
            <div>
                <form action="index.php" method="post" class="frm-search">
                    <h3 style="height: 35px; flex: 2;">Tasks</h3>
                    <input type="hidden" name="todo_search" placeholder="" class="todo-search" />

                    <button class="btn btn-search">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            <div class="app-header">
                <!-- Create -->
                <form action="./handler/new_todo.php" method="post" class="frm-create">
                    <input type="text" name="todo_name" value="<?php echo ($todoSearch != '') ? $todoSearch : '' ?>" placeholder="" class="todo-name" />

                    <span class="focus-border"></span>
                    <label for="todo_name" class="lbl-name">Enter your task</label>
                    <button type="submit" class="btn btn-create">
                        <img style="height: 100%;" src="./img/pen.png" alt="">
                    </button>
                </form>

            </div>   
            <div class="app-main">
                <div class="options">
                    <form action="index.php" method="post" class="frm-filter">

                        <input type="hidden" name="todo_type" value="" class="todo-type">
                        
                        <span class="option-current">
                            <?php echo $type;?>
                        </span>
                        <ul class="option-list blur">
                            <li class="option-item"><a href="#" class="option-link">All</a></li>
                            <li class="option-item"><a href="#" class="option-link">Due Today</a></li>
                            <li class="option-item"><a href="#" class="option-link">Completed</a></li>
                            <li class="option-item"><a href="#" class="option-link">Uncompleted</a></li>
                        </ul>
                        <i style="color: var(--primaryColor);" class="fas fa-chevron-down"></i>
                    </form>
                </div>
                <div class="app-content">
                    <div class="symbol-null">
                        <img title="Empty task" src="./img/symbol_null.png" alt="">
                    </div>
                    
                    <?php foreach($todos as $todoName => $todo): ?>
                    <div class="content-item <?php echo $todo['completed'] ? 'completed' : '' ?>">
                        <span class="item-name"><?php echo $todoName?></span>
                        
                        <form style="line-height: 46px" action="./handler/change_due.php" method="post" class="frm-calendar">
                            <input type="hidden" name="todo_name" value="<?php echo $todoName; ?>">
                            <div class="choice-due">
                                <input name="todo_due" type="date" class="todo-date">
                                
                                <div class="options-date">
                                    <button class="btn-handler btn-delete">Delete</button>
                                    <button class="btn-handler btn-apply">Apply</button>
                                </div>
                            </div>
                            <a class="btn btn-calendar"><i class="fas fa-calendar"></i></a>
                        </form>

                        <form style="line-height: 46px" action="./handler/change_status.php" method="post" class="frm-change">
                            <input type="hidden" name="todo_name" value="<?php echo $todoName; ?>">
                            <button class="btn btn-check"><i class="fas fa-check"></i></button>
                        </form>

                        <form style="line-height: 46px" action="./handler/delete.php" method="post" class="frm-delete">
                            <input type="hidden" name="todo_name" value="<?php echo $todoName; ?>">
                            <button class="btn btn-remove"><i class="far fa-trash-alt"></i></button>
                        </form>                    
                        
                        <div class="item-time">
                            <span>
                                <span>SD:</span>
                                <span class="start-time"><?php echo $todo['startDay']; ?></span>    
                            </span>
                            <span>-</span>
                            <span>
                                <span>FD:</span>
                                <span class="finish-time"><?php echo $todo['finishDay']; ?></span>    
                            </span>
                            <span>-</span>
                            <span>
                                <span>DD:</span>
                                <span class="due-time"><?php echo $todo['dueDay']; ?></span>    
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>             
        </div>

        <div class="app-note">
            <div>
                <span>SD: </span><span>Start Date</span>  
                <span> - </span>         
                <span>FD: </span><span>Finish Date</span>           
                <span> - </span>         
                <span>DD: </span><span>Due Date</span>     
            </div>
            <div style="color: grey; font-size: 1.4rem; word-spacing: 2px">
                Web application made by <a class="link-info" href="https://www.facebook.com/longdang24/" target="_blank" style="margin-left: 6px; ">Long Dang</a>         
            </div>
        </div>

        <div class="options-login">
            <div class="btn-options-login">
                <img src="./img/setting-lines.png" alt="">
            </div>

            <ul class="login-list">
                <li class="login-item">
                    <a href="#" class="login-link open-change">
                        <i class="fas fa-lock"></i>
                        <span>Change your password</span>
                    </a>
                </li>
                <li class="login-item">
                    <a href="#" class="login-link open-sign-out">
                        <i class="fas fa-power-off"></i>
                        <span>Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="modal modal-sign-out">
            <form action="login.php" method="post">
                <div class="modal-main">
                    <div class="modal-header">
                        <h3>Alert</h3>
                    </div>
                    <div class="modal-body">
                        <h3 style="font-size: 1.6rem; margin: 1rem 0;">Are you sure to sign out?</h3>
                    </div>
                    <div class="modal-footer">
                        <div class="footer-btn">
                            <a href="#" class="btn-cancel">Cancel</a>
                            <button style="color: #f88a5b;" class="btn-sign-out">Sure</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal modal-change">
            <div class="modal-main">
                <form action="index.php" method="post" class="frm-change">
                    <div class="modal-header">
                        <h3>Change your password</h3>
                    </div>
                    <div class="modal-body">
                        <input type="password" name="old_password" placeholder="Old password" required>       
                        <input type="password" name="new_password" placeholder="New password" required>       
                        <input type="password" name="confirm_password" placeholder="Confirm new password" required>       
                    </div>
                    <div class="modal-footer">
                        <?php if(!empty($errors)): ?> 
                            <div class="frm-change-error" style="color: red; font-size: 1.4rem; margin-bottom: 1rem;">
                                <?php foreach($errors as $error): ?>
                                    <div><?php echo $error ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>     
                        <?php endif; ?>
                        <div class="footer-btn">
                            <button class="btn-cancel">Cancel</button>
                            <button style="color: #f88a5b;" class="btn-change">Change</button>
                        </div>
                </form>
            </div>
        </div>


        <!-- <div class="alert">
            <span>This name is already on the list</span>          
            <span class="btn-close">&times</span>    
        </div> -->
    </div>


    <script src="./dist/js/jquery-3.6.0.min.js"></script>
    <script src="./dist/js/main.js"></script>
</body>
</html>