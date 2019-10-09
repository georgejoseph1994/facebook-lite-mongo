<?php
require "crud.php";
session_start();
header("Content-Type:application/json");

//getting the uri and resolving it
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER["REQUEST_METHOD"];
$uri = explode( '/', $uri );
$body=json_decode(file_get_contents('php://input'));
$method = $body->method;
$response = new STDClass();

switch($requestMethod){
    case 'GET':{
      if($method == "test"){
        echo "This is an api file.";
      }
    }
    case 'POST':{
        if($method=="signup"){
          register_user($body,$response );
        }
        elseif($method=="login"){
          login_user($body,$response );
        }
        elseif($uri[3]=="account"){
          if($uri[4]=="update"){
            update_user($body,$response);
          }
        }
        elseif($uri[3]=="search"){
          search_user($body,$response,$_SESSION['user']->email);
        }
        elseif($uri[3]=="friends"){
          if($uri[4]=="send"){
            send_friends_request($body,$response);
          }elseif($uri[4]=="fetch"){
            fetch_friends_request($body,$response);
          }elseif($uri[4]=="respond"){
            respond_friends_request($body,$response);
          }
        }
        elseif($uri[3]=="posts"){
          if($uri[4]=="create"){
            create_post($body,$response);
          }elseif($uri[4]=="fetch"){
            fetch_post($body,$response);
          }
        }
        elseif($uri[3]=="like"){
          like_post($body,$response);
        }
        elseif($uri[3]=="unlike"){
          unlike_post($body,$response);
        }
        elseif($uri[3]=="deleteUser"){
          delete_user($body,$response);
        }
    }
    echo json_encode($response);
}

?>

