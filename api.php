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
        elseif($method=="account/update"){
            update_user($body,$response);
        }
        elseif($method=="search"){
          search_user($body,$response);
        }
        elseif($method=="sendReq"){
          send_friends_request($body,$response);
        }
        elseif($method =="fetchFriendships"){
          fetch_friendships($body,$response);
        }
        elseif($method =="respondReq"){
          respond_friends_request($body,$response);
        }
        elseif($method =="respondReq"){
          respond_friends_request($body,$response);
        }
        elseif($method =="createPost"){
            create_post($body,$response);
        }
        elseif($method == "fetchPost"){
          fetch_post($body,$response);
        }
        elseif($method == "updatePost"){
          update_post($body,$response);
        }
        elseif($method == "deleteUser"){
          delete_user($body,$response);
        }
    }
    echo json_encode($response);
}

?>

