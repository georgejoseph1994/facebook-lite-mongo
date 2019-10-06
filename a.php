<?php
$manager = new MongoDB\Driver\Manager("mongodb://mongo:27017/test");

$bulk = new MongoDB\Driver\BulkWrite();
$bulk->insert(['name' => 'John Doe']);

$writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
$result = $manager->executeBulkWrite('test.mycollection', $bulk);

var_dump($result);
?>
