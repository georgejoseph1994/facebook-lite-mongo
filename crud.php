<?php
include_once("../app/vendor/autoload.php");
/*
*   Method to register a new user
*/
function register_user($body, $response)
{
    try {
        $client = new MongoDB\Client("mongodb://mongo:27017");
        $collection = $client->facebook->member;

        $insertOneResult = $collection->insertOne([
            'email' => $body->email,
            'full_name' => $body->full_name,
            'screen_name' => $body->screen_name,
            'dob' => $body->dob,
            'gender' => $body->gender,
            'status' => $body->status,
            'location' => $body->location,
            'visibility' => $body->visibility,
            'friendships' => [],
            'password_hash' => password_hash($body->password, PASSWORD_DEFAULT),
        ]);

        //Setting the response object
        $response->status = "Success";
        $response->code = http_response_code(200);

        //Setting the results object for adding to the session
        $results = new stdClass();
        $results->email = $body->email;
        $results->full_name =  $body->full_name;
        $results->screen_name = $body->screen_name;
        $results->dob = $body->dob;
        $results->gender = $body->gender;
        $results->status = $body->status;
        $results->location = $body->location;
        $results->visibility = $body->visibility;
        $_SESSION['user'] = $results;
    } catch (Exception $e) {
        $filename = basename(__FILE__);
        echo "The $filename script has experienced an error.\n";
        echo "It failed with the following exception:\n";
        echo "Exception:", $e->getMessage(), "\n";
        echo "In file:", $e->getFile(), "\n";
        echo "On line:", $e->getLine(), "\n";
        $response->code = http_response_code(400);
        $response->status = "Error";
        $response->errMsg = $e->getMessage();
    }
}

/*
*   Method to check if a users login credentials are correct for login
*/
function login_user($body, $response)
{
    try {
        $client = new MongoDB\Client("mongodb://mongo:27017");
        $collection = $client->facebook->member;

        $document = $collection->findOne([
            'email' => $body->email,
        ]);
        if (password_verify($body->password, $document->password_hash)) {
            //Setting the success response object
            $response->status = "Success";
            $response->code = 200;
            http_response_code(200);

            //Setting the results object for adding to the session
            $results = new stdClass();
            $results->email = $document->email;
            $results->full_name =  $document->full_name;
            $results->screen_name = $document->screen_name;
            $results->dob = $document->dob;
            $results->gender = $document->gender;
            $results->status = $document->status;
            $results->location = $document->location;
            $results->visibility = $document->visibility;
            $_SESSION['user'] = $results;
        } else {
            $response->status = "Error";
            $response->code = 400;
            http_response_code(400);
        }
    } catch (Exception $e) {
        $filename = basename(__FILE__);
        echo "The $filename script has experienced an error.\n";
        echo "It failed with the following exception:\n";
        echo "Exception:", $e->getMessage(), "\n";
        echo "In file:", $e->getFile(), "\n";
        echo "On line:", $e->getLine(), "\n";
        $response->code = http_response_code(400);
        $response->status = "Error";
        $response->errMsg = $e->getMessage();
    }
}


/*
* Method to update a users details
*/
function update_user($body, $response)
{
    try {
        $client = new MongoDB\Client("mongodb://mongo:27017");
        $collection = $client->facebook->member;

        $collection->updateOne(
            ['email' => $body->email],
            [
                '$set' =>
                [
                    'full_name' => $body->full_name,
                    'screen_name' => $body->screen_name,
                    'dob' => $body->dob,
                    'gender' => $body->gender,
                    'status' => $body->status,
                    'location' => $body->location,
                    'visibility' => $body->visibility
                ]
            ]
        );
        if ($body->password != "*************") {
            $collection->updateOne(
                ['email' => $body->email],
                [
                    '$set' =>
                    ['password_hash' => password_hash($body->password, PASSWORD_DEFAULT)]
                ]
            );
        }

        //Setting the results object for adding to the session
        $results = new stdClass();
        $results->email = $body->email;
        $results->full_name =  $body->full_name;
        $results->screen_name = $body->screen_name;
        $results->dob = $body->dob;
        $results->gender = $body->gender;
        $results->status = $body->status;
        $results->location = $body->location;
        $results->visibility = $body->visibility;
        $_SESSION['user'] = $results;

        //Setting the response object
        $response->status = "Success";
        $response->res = $results;
        $response->code = 200;
        http_response_code(200);
    } catch (Exception $e) {
        $filename = basename(__FILE__);
        echo "The $filename script has experienced an error.\n";
        echo "It failed with the following exception:\n";
        echo "Exception:", $e->getMessage(), "\n";
        echo "In file:", $e->getFile(), "\n";
        echo "On line:", $e->getLine(), "\n";
        $response->code = 400;
        http_response_code(400);
        $response->status = "Error";
        $response->errMsg = $e->getMessage();
    }
}


/*
* Method to search a users details and return the friendhsip graph with those users
*/
function search_user($body, $response)
{
    try {
        $client = new MongoDB\Client("mongodb://mongo:27017");
        $collection = $client->facebook->member;

        //Getting the list of users similar to the search
        $regex = new MongoDB\BSON\Regex('^' . $body->search);
        $cursor = $collection->find([
            'screen_name' => $regex
        ]);
        $response->users = [];

        foreach ($cursor as $user) {
            if ($user->visibility != "P") {
                $temp = new StdClass();
                $temp->email = $user->email;
                $temp->screen_name = $user->screen_name;
                $temp->friendships = $user->friendships;
                array_push($response->users, $temp);
            }
        };

        $response->status = "Success";
        $response->code = 200;
        http_response_code(200);
    } catch (Exception $e) {
        $filename = basename(__FILE__);
        echo "The $filename script has experienced an error.\n";
        echo "It failed with the following exception:\n";
        echo "Exception:", $e->getMessage(), "\n";
        echo "In file:", $e->getFile(), "\n";
        echo "On line:", $e->getLine(), "\n";
        $response->status = "Error";
        $response->code = 400;
        http_response_code(400);
        $response->errMsg = $e->getMessage();
    }
}


// Method to send request to a user
function send_friends_request($body, $response)
{
    try {
        $client = new MongoDB\Client("mongodb://mongo:27017");
        $collection = $client->facebook->member;
        if($body->status1=="S"){
            $collection->updateOne(
                ['email' => $body->user_email_a],
                [
                    '$addToSet' => [
                        'friendships' => [
                            'email' => $body->user_email_b,
                            'screen_name' => $body->screen_name_b,
                            'status' => 'S'
                        ]
                    ]
                ]
            );
    
            $collection->updateOne(
                ['email' => $body->user_email_b],
                [
                    '$addToSet' => [
                        'friendships' => [
                            'email' => $body->user_email_a,
                            'screen_name' => $body->screen_name_a,
                            'status' => 'R'
                        ]
                    ]
                ]
            );
        }
        $response->status = "Success";
        $response->code = 200;
        http_response_code(200);
    } catch (Exception $e) {
        $filename = basename(__FILE__);
        echo "The $filename script has experienced an error.\n";
        echo "It failed with the following exception:\n";
        echo "Exception:", $e->getMessage(), "\n";
        echo "In file:", $e->getFile(), "\n";
        echo "On line:", $e->getLine(), "\n";
        $response->status = "Error";
        $response->code = 400;
        http_response_code(400);
        $response->errMsg = $e->getMessage();
    }
}


// Method to fetch_friends_request
function fetch_friendships($body, $response)
{
    try {
        $client = new MongoDB\Client("mongodb://mongo:27017");
        $collection = $client->facebook->member;

        $document = $collection->findOne([
            'email' => $body->email,
        ]);
        $response->status = "Success";
        $response->code = 200;
        http_response_code(200);
        $response->results = $document->friendships;

    } catch (Exception $e) {
        $filename = basename(__FILE__);
        echo "The $filename script has experienced an error.\n";
        echo "It failed with the following exception:\n";
        echo "Exception:", $e->getMessage(), "\n";
        echo "In file:", $e->getFile(), "\n";
        echo "On line:", $e->getLine(), "\n";
        $response->code = http_response_code(400);
        $response->status = "Error";
        $response->errMsg = $e->getMessage();
    }
}

// Method to send request to a user
function respond_friends_request($body, $response)
{
    try {
        $client = new MongoDB\Client("mongodb://mongo:27017");
        $collection = $client->facebook->member;
        if($body->status=="A" || $body->status=="N"){
            $collection->updateOne(
                ['email' => $body->user_email_a, 'friendships.email' => $body->user_email_b],
                [
                    '$set' =>
                    [
                        'friendships.$.status' => $body->status,
                    ]
                ]
            );
            $collection->updateOne(
                ['email' => $body->user_email_b, 'friendships.email' => $body->user_email_a],
                [
                    '$set' =>
                    [
                        'friendships.$.status' => $body->status,
                    ]
                ]
            );
        }
        //Setting the response object
        $response->status = "Success";
        $response->code = 200;
        http_response_code(200);
        
    } catch (Exception $e) {
        $filename = basename(__FILE__);
        echo "The $filename script has experienced an error.\n";
        echo "It failed with the following exception:\n";
        echo "Exception:", $e->getMessage(), "\n";
        echo "In file:", $e->getFile(), "\n";
        echo "On line:", $e->getLine(), "\n";
        $response->code = 400;
        http_response_code(400);
        $response->status = "Error";
        $response->errMsg = $e->getMessage();
    }
}

// Method to send request to a user
function create_post($body, $response)
{
    try {
        $client = new MongoDB\Client("mongodb://mongo:27017");
        $collection = $client->facebook->posts;

        $insertOneResult = $collection->insertOne([
            'post_body' => $body->post_body,
            'email' => $body->email,
            'timestamp' => date('Y-m-d H:i:s'),
            'likes'=> [],
            'comment' =>[]
        ]);

        //Setting the response object
        $response->status = "Success";
        $response->code = 200;
        http_response_code(200);

    } catch (Exception $e) {
        $filename = basename(__FILE__);
        echo "The $filename script has experienced an error.\n";
        echo "It failed with the following exception:\n";
        echo "Exception:", $e->getMessage(), "\n";
        echo "In file:", $e->getFile(), "\n";
        echo "On line:", $e->getLine(), "\n";
        $response->code = http_response_code(400);
        $response->status = "Error";
        $response->errMsg = $e->getMessage();
    }
}


// Method to fetch_all_post
function fetch_post($body, $response)
{
    try {
        $client = new MongoDB\Client("mongodb://mongo:27017");
        $collection = $client->facebook->member;
       
        $cursor = $collection->find([
            'visibility' => 'E'
        ]);

        $userlist=[];
        $userAssociativeArr=[];
        foreach ($cursor as $user) {
            if($user->email == $body->email){
                foreach($user->friendships as $friends){
                    if( !in_array($friends->email,$userlist)){
                        array_push($userlist, $friends->email );
                        $userAssociativeArr[$friends->email]=$friends->screen_name;
                    }
                }
                array_push($userlist, $user->email);
                $userAssociativeArr[$user->email]=$user->screen_name;
            }else{
                if(!in_array($user->email,$userlist)){
                    array_push($userlist, $user->email);
                    $userAssociativeArr[$user->email]=$user->screen_name;
                }
            }
           
        };

        $postCollection = $client->facebook->posts;

        $cursor = $postCollection->find([
            'email' => [
                '$in' => $userlist
            ]
        ]);
        $postlist = [];
        foreach ($cursor as $post) {
            $post->screen_name=$userAssociativeArr[$post->email];
            array_push($postlist, $post);
        }

        // $response->arr=$userAssociativeArr;
        $response->postlist=$postlist;
        // $response->userlist=$userlist;
        $response->status = "Success";
        $response->code = 200;
        http_response_code(200);

    } catch (Exception $e) {
        $filename = basename(__FILE__);
        echo "The $filename script has experienced an error.\n";
        echo "It failed with the following exception:\n";
        echo "Exception:", $e->getMessage(), "\n";
        echo "In file:", $e->getFile(), "\n";
        echo "On line:", $e->getLine(), "\n";
        $response->status = "Error";
        $response->code = 400;
        http_response_code(400);
        $response->errMsg = $e->getMessage();
    }
}


function createPostTree($array, $postParentId)
{
    $posts = array();
    foreach ($array as $row) {
        if ($row['POST_PARENT_ID'] == $postParentId) {
            $post = new STDClass();
            $post->postId = $row['POSTRESPONSEID'];
            $post->text = $row['BODY'];
            $post->parentId = $row['POST_PARENT_ID'];
            $post->rootParentId = $row['ROOT_PARENT_ID'];
            $post->like = $row['LIKED'];
            $post->email = $row['USER_EMAIL'];
            $post->screenName = $row['SCREEN_NAME'];
            $post->timestamp = $row['TIMESTAMP'];
            $post->noOfLikes = $row['LIKECOUNT'];
            $post->children = createPostTree($array, $row['POSTRESPONSEID']);
            array_push($posts, $post);
        }
    }
    return $posts;
}


// Method to like
function like_post($body, $response)
{
//     try {
//         $client = new MongoDB\Client("mongodb://mongo:27017");
//         $collection = $client->facebook->posts;
//             $collection->updateOne(
//                 ['email' => $body->user_email_a],
//                 [
//                     '$addToSet' => [
//                         'friendships' => [
//                             'email' => $body->user_email_b,
//                             'screen_name' => $body->screen_name_b,
//                             'status' => 'S'
//                         ]
//                     ]
//                 ]
//             );
//         }
//         $response->status = "Success";
//         $response->code = 200;
//         http_response_code(200);
//     } catch (Exception $e) {
//         $filename = basename(__FILE__);
//         echo "The $filename script has experienced an error.\n";
//         echo "It failed with the following exception:\n";
//         echo "Exception:", $e->getMessage(), "\n";
//         echo "In file:", $e->getFile(), "\n";
//         echo "On line:", $e->getLine(), "\n";
//         $response->status = "Error";
//         $response->code = 400;
//         http_response_code(400);
//         $response->errMsg = $e->getMessage();
//     }
}

// Method to like
function update_post($body, $response)
{
    try {
        $client = new MongoDB\Client("mongodb://mongo:27017");
        $collection = $client->facebook->posts;
        $collection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectID($body->_id)],
            [
                '$set' => [
                    'comment' =>$body->comment,
                    'likes'=>$body->likes,
                ]
            ]
        );
        $response->test = $body->_id;
        $response->body=$body;
        $response->status = "Success";
        $response->code = 200;
        http_response_code(200);
    } catch (Exception $e) {
        $filename = basename(__FILE__);
        echo "The $filename script has experienced an error.\n";
        echo "It failed with the following exception:\n";
        echo "Exception:", $e->getMessage(), "\n";
        echo "In file:", $e->getFile(), "\n";
        echo "On line:", $e->getLine(), "\n";
        $response->status = "Error";
        $response->code = 400;
        http_response_code(400);
        $response->errMsg = $e->getMessage();
    }
}


// Method to unlike
function unlike_post($body, $response)
{
    // Establishing a connection to the database
    try {
        $conn = connect();

        $query =  "BEGIN DELETE FROM LIKES WHERE POSTID=:postid AND USER_EMAIL=:email ; UPDATE post_response SET LIKECOUNT = :count WHERE POSTRESPONSEID= :postid; END;";


        $stid = oci_parse($conn, $query);
        oci_bind_by_name($stid, ':email', $body->email);
        oci_bind_by_name($stid, ':postid', $body->postid);
        oci_bind_by_name($stid, ':count', $body->count);

        oci_execute($stid);
        $ncols = oci_num_rows($stid);
        $result = oci_commit($conn);

        $errorMessage = oci_error($stid);

        if ($errorMessage || $ncols == 0) {
            $response->errorMessage = $errorMessage;
            $response->code = http_response_code(200);
            $response->status = "Error";
        } else {
            $response->status = "Success";
            $response->code = http_response_code(200);
        }
    } catch (Exception $e) {
        $response->status = "Error";
        $response->errorMessage = $e;
        oci_close($conn);
    }
    if ($conn) {
        oci_close($conn);
    }
}

// Method to unlike
function delete_user($body, $response)
{
    // Establishing a connection to the database
    try {
        $conn = connect();

        $query =  "DELETE from \"User\" where EMAIL = :email";


        $stid = oci_parse($conn, $query);

        oci_bind_by_name($stid, ':email', $body->email);
        oci_execute($stid);
        $ncols = oci_num_rows($stid);
        $result = oci_commit($conn);

        $errorMessage = oci_error($stid);

        if ($errorMessage || $ncols == 0) {
            $response->errorMessage = $errorMessage;
            $response->code = http_response_code(200);
            $response->status = "Error";
        } else {
            $response->status = "Success";
            $response->code = http_response_code(200);
            $_SESSION['user'] = null;
        }
    } catch (Exception $e) {
        $response->status = "Error";
        $response->errorMessage = $e;
        oci_close($conn);
    }
    if ($conn) {
        oci_close($conn);
    }
}
