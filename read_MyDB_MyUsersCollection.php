<?php
try 
{
    // within php use mongo:27017 as the mongo server:port, not
    // localhost:27020 that's for accessing from host computer
    $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017");


    $filter = [];
    $options = [
        'projection' => ['_id' => 0],
        'sort' => [],
    ];

    $query = new MongoDB\Driver\Query($filter, $options);
    $rows = $mng->executeQuery('MyDB.MyUsersCollection', $query);

    foreach ($rows as $row) 
    {
        print "$row->Name : $row->State : $row->Email : $row->Telephone<BR>";
    }

} catch (MongoDB\Driver\Exception\Exception $e) {

    $filename = basename(__FILE__);

    echo "The $filename script has experienced an error.\n"; 
    echo "It failed with the following exception:\n";
 
    echo "Exception:", $e->getMessage(), "\n";
    echo "In file:", $e->getFile(), "\n";
    echo "On line:", $e->getLine(), "\n";       
}

?>

