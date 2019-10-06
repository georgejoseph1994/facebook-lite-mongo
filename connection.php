<?php
include_once("../app/vendor/autoload.php");
try {
    echo "Check point 1 -- before connection established<BR>";
    $client = new MongoDB\Client("mongodb://mongo:27020");
    echo "Check point 2 -- after connection established<BR>";
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
