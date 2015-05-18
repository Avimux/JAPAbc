<?php
error_reporting(E_ALL);

session_start();
$time = time();

switch($_REQUEST['command']) {
    case 'getuser':
        if(isset($_SESSION['username'])) {
            echo('["'.$_SESSION['username'].'"]');
        } else {
            echo('[null]');
        }
        break;
    case 'setuser':
        $_SESSION['username'] = strip_tags($_REQUEST['login']);
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $_SESSION['color'] = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];;
        break;
    case 'chatloop':
        while(time() - $time <= 10) {
            if(apc_exists("chat_json")) {
                $chatarray = apc_fetch("chat_json");
                if(!$chatarray) {
                    die("[\"Error reading cache\"]");
                }
                //if(!isset($_SESSION['lastmsg'])) {
                    //$_SESSION['lastmsg'] = "";
                //}
                //if($chatarray != $_SESSION['lastmsg']) {
                    //$_SESSION['lastmsg'] = $chatarray;
                    echo $chatarray;
                    break;
                //}
            }
            usleep(250000);
        }
        break;
    case 'newmsg':
        if(apc_exists("chat_json")) {
            $chatarray = json_decode(apc_fetch("chat_json"));
        } else {
            $chatarray = [];
            apc_add("chat_json", json_encode($chatarray));
        }
        array_push($chatarray, [time(),$_SESSION['username'],$_SESSION['color'],strip_tags($_REQUEST['msg'])]);
        
        while(time() - $chatarray[0][0] > 10) {
            array_shift($chatarray);
        }
        
        apc_store("chat_json", json_encode($chatarray));
        break;
    case 'logout':
        $_SESSION['username'] = null;
        break;
}
?>