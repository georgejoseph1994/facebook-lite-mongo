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


// Method to search a users details and return the friendhsip graph with those users
function search_user($body, $response, $email)
{
    //Establishing a connection to the database
    $conn = connect();

    $str = strtoupper($body->search);
    // $query = "SELECT EMAIL, SCREEN_NAME FROM \"User\" WHERE UPPER(EMAIL) LIKE '%". $str."%' OR UPPER(SCREEN_NAME) LIKE '%". $str."%' OR UPPER(FULL_NAME) LIKE '%". $str."%' ORDER BY UTL_MATCH.EDIT_DISTANCE('. $str. ' , UPPER(SCREEN_NAME ) ) FETCH NEXT 10 ROWS ONLY";
    $query = "SELECT EMAIL,SCREEN_NAME,USER_EMAIL_A,USER_EMAIL_B,FRIENDSHIP_GRAPH.STATUS AS FRIENDSHIP_STATUS, START_DATE " .
        "FROM \"User\" LEFT OUTER JOIN FRIENDSHIP_GRAPH " .
        "ON ( ( EMAIL= USER_EMAIL_A AND USER_EMAIL_B = '" . $email . "' ) OR ( EMAIL = USER_EMAIL_B AND USER_EMAIL_A = '" . $email . "')) " .
        "WHERE  " .
        "(UPPER(EMAIL) LIKE '%" . $str . "%' OR " .
        "UPPER(SCREEN_NAME) LIKE '%" . $str . "%' OR " .
        "UPPER(FULL_NAME) LIKE '%" . $str . "%') " .
        "ORDER BY UTL_MATCH.EDIT_DISTANCE( '%" . $str . "%' , UPPER(SCREEN_NAME) ) FETCH NEXT 10 ROWS ONLY";
    $stid = oci_parse($conn, $query);

    $result = @oci_execute($stid);
    $nrows = oci_fetch_all($stid, $queryResults, null, null, OCI_FETCHSTATEMENT_BY_ROW);

    if (!$result) {
        $errorMessage = oci_error($stid);
        $response->code = http_response_code(200);
        $response->status = "Error";
        $response->errorMessage = $errorMessage;
    } else {
        $response->status = "Success";
        $response->code = http_response_code(200);
        $response->results = $queryResults;
    }
    oci_close($conn);
}


// Method to send request to a user
function send_friends_request($body, $response)
{
    try {
        // // Establishing a connection to the database
        $conn = connect();

        $query =  "INSERT INTO FRIENDSHIP_GRAPH VALUES ( :user_email_a , :user_email_b, :status1 ,null)";
        $stid = oci_parse($conn, $query);

        oci_bind_by_name($stid, ':user_email_a', $body->user_email_a);
        oci_bind_by_name($stid, ':user_email_b', $body->user_email_b);
        oci_bind_by_name($stid, ':status1', $body->status1);

        oci_execute($stid);
        $ncols = oci_num_rows($stid);
        $result = oci_commit($conn);
        $errorMessage = oci_error($stid);
        if ($ncols == 0) {
            $response->code = http_response_code(200);
            $response->status = "Error";
            $response->errorMessage = $errorMessage;;
        } else {
            $response->status = "Success";
            $response->code = http_response_code(200);
        }
        oci_close($conn);
    } catch (Exception $e) {
        $response->code = http_response_code(200);
        $response->status = "Error";
        $response->errorMessage = $errorMessage;
        oci_close($conn);
    }
    if ($conn) {
        oci_close($conn);
    }
}


// Method to fetch_friends_request
function fetch_friends_request($body, $response)
{
    // // Establishing a connection to the database
    $conn = connect();

    $query = 'SELECT SCREEN_NAME,EMAIL FROM "User" JOIN FRIENDSHIP_GRAPH ON EMAIL=USER_EMAIL_A WHERE USER_EMAIL_B = \'' . $body->email . '\'  AND FRIENDSHIP_GRAPH.STATUS = \'S\' ';
    $stid = oci_parse($conn, $query);

    // oci_bind_by_name($stid, ':email', $body->user_email_a);

    @oci_execute($stid);
    $nrows = oci_fetch_all($stid, $queryResults, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    $result = oci_commit($conn);
    $errorMessage = oci_error($stid);
    if ($errorMessage) {
        $response->code = http_response_code(200);
        $response->status = "Error";
        $response->errorMessage = $errorMessage;
    } else {
        $response->status = "Success";
        $response->code = http_response_code(200);
        $response->results = $queryResults;
    }
    oci_close($conn);
}

// Method to send request to a user
function respond_friends_request($body, $response)
{
    // Establishing a connection to the database
    $conn = connect();

    $query =  "UPDATE FRIENDSHIP_GRAPH SET STATUS='" . $body->status . "' WHERE USER_EMAIL_A = '" .  $body->user_email_a . "' AND  USER_EMAIL_B = '" .  $body->user_email_b . "'";
    $stid = oci_parse($conn, $query);

    @oci_execute($stid);
    $ncols = oci_num_rows($stid);
    $result = oci_commit($conn);
    if ($ncols == 0) {
        $errorMessage = oci_error($stid);
        $response->code = http_response_code(200);
        $response->status = "Error";
        $response->errorMessage = $errorMessage;
    } else {
        $response->status = "Success";
        $response->code = http_response_code(200);
    }
    oci_close($conn);
}

// Method to send request to a user
function create_post($body, $response)
{
    // // Establishing a connection to the database
    $conn = connect();
    if ($body->root_post_id == null && $body->post_parent_id == null) {
        $query =  "INSERT INTO POST_RESPONSE (POSTRESPONSEID,BODY,LIKECOUNT,USER_EMAIL,POST_PARENT_ID,ROOT_PARENT_ID) VALUES(SEQ_POSTID.NEXTVAL, '" . $body->post_body . "',0, '" . $body->email . "',NULL,NULL)";
    } else {
        $query =  "INSERT INTO POST_RESPONSE (POSTRESPONSEID,BODY,LIKECOUNT,USER_EMAIL,POST_PARENT_ID,ROOT_PARENT_ID) VALUES(SEQ_POSTID.NEXTVAL, '" . $body->post_body . "',0, '" . $body->email . "','" . $body->post_parent_id . "','" . $body->root_post_id . "')";
    }
    // $query =  "INSERT INTO POST_RESPONSE (POSTRESPONSEID,BODY,LIKECOUNT,USER_EMAIL,POST_PARENT_ID,ROOT_PARENT_ID) VALUES(SEQ_POSTID.NEXTVAL, '". $body->post_body ."',0, '".$body->email."',".$body->post_parent_id.",".$body->root_post_id.")";


    $stid = oci_parse($conn, $query);

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
    oci_close($conn);
}


// Method to fetch_all_post
function fetch_post($body, $response)
{
    // Establishing a connection to the database
    $conn = connect();

    $query = "SELECT PR.POSTRESPONSEID, PR.BODY, PR.TIMESTAMP, PR.USER_EMAIL, U.SCREEN_NAME, PR.POST_PARENT_ID, PR.ROOT_PARENT_ID, PR.LIKECOUNT, "
        . "L.USER_EMAIL AS LIKED "
        . "FROM  "
        . "POST_RESPONSE PR LEFT OUTER JOIN LIKES L "
        . "ON L.POSTID = PR.POSTRESPONSEID "
        . "AND L.USER_EMAIL = :email "
        . "LEFT OUTER JOIN \"User\" U "
        . "ON PR.USER_EMAIL = U.EMAIL "
        . "WHERE PR.POSTRESPONSEID IN ( "
        . "SELECT POSTRESPONSEID  "
        . "FROM POST_RESPONSE  "
        . "WHERE USER_EMAIL = :email "
        . "OR "
        . "USER_EMAIL IN ( "
        . "    SELECT EMAIL FROM \"User\" WHERE VISIBILITY ='E' UNION "
        . "    SELECT USER_EMAIL_A FROM FRIENDSHIP_GRAPH WHERE USER_EMAIL_B =:email UNION "
        . "    SELECT USER_EMAIL_B FROM FRIENDSHIP_GRAPH WHERE USER_EMAIL_A =:email UNION "
        . "    SELECT EMAIL FROM \"User\" WHERE VISIBILITY ='P') "
        . ") "
        . "OR "
        . "PR.ROOT_PARENT_ID IN ( "
        . "SELECT POSTRESPONSEID  "
        . "FROM POST_RESPONSE  "
        . "WHERE USER_EMAIL = :email "
        . "OR "
        . "USER_EMAIL IN ( "
        . "SELECT EMAIL FROM \"User\" WHERE VISIBILITY ='E' UNION "
        . "SELECT USER_EMAIL_A FROM FRIENDSHIP_GRAPH WHERE USER_EMAIL_B =:email UNION "
        . "SELECT USER_EMAIL_B FROM FRIENDSHIP_GRAPH WHERE USER_EMAIL_A =:email UNION "
        . "SELECT EMAIL FROM \"User\" WHERE VISIBILITY ='P') "
        . ") "
        . "ORDER BY PR.TIMESTAMP DESC ";

    $stid = oci_parse($conn, $query);

    oci_bind_by_name($stid, ':email', $body->email);

    @oci_execute($stid);
    $nrows = oci_fetch_all($stid, $queryResults, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    $result = oci_commit($conn);
    $errorMessage = oci_error($stid);
    if ($errorMessage) {
        $response->code = http_response_code(200);
        $response->status = "Error";
        $response->errorMessage = $errorMessage;
    } else {
        $response->status = "Success";
        $response->code = http_response_code(200);
        $response->results = createPostTree($queryResults, null);
    }
    oci_close($conn);
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
    // Establishing a connection to the database
    try {
        $conn = connect();

        $query =  "BEGIN INSERT INTO LIKES (POSTID,USER_EMAIL) VALUES(:postid, :email ); UPDATE post_response SET LIKECOUNT = :count WHERE POSTRESPONSEID= :postid; END;";


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
    } catch (exeption $e) {
        $response->status = "Error";
        $response->errorMessage = $e;
        oci_close($conn);
    }
    if ($conn) {
        oci_close($conn);
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
