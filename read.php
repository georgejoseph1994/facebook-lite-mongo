<?php
include_once("../app/vendor/autoload.php");
try {
    
    $client = new MongoDB\Client("mongodb://mongo:27017");

    // $collection = $client->MyDB->MyUsersCollection;
    $collection = $client->test->mycollection;
    $document = $collection->findOne([
      'name' => 'John Doe'
    ]);
    echo "Display the first doc that matches the conditions<BR>";
    echo $document->Name . "<BR>";
    echo $document->name . "<BR>";

    $cursor = $collection->find([
      'username' => 'admin'
    ]);

    echo "Display all docs that matches the conditions<BR><BR>";
    foreach ($cursor as $d) {
      echo $d;
        echo $d->_id. " ". $d->username ." " .$d->name . "<BR>";
    }
    
}
catch (MongoDB\Driver\Exception\Exception $e) {

    $filename = basename(__FILE__);

    echo "The $filename script has experienced an error.\n";
    echo "It failed with the following exception:\n";

    echo "Exception:", $e->getMessage(), "\n";
    echo "In file:", $e->getFile(), "\n";
    echo "On line:", $e->getLine(), "\n";
}


?>
