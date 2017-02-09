<?php

require_once('C:\Users\pierr\GitRepo\php-restclient\restclient.php');


$api = new RestClient(
    [
    //    'base_url' => "http://maps.googleapis.com", 
    ]
);

//$result = $api->get("maps/api/geocode/json",['address'=>'Rulles']);
$result =$api->get("http://127.0.0.0/API.php/Application/1");
var_dump($result);



