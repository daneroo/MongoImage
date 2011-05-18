<?php

//error_log("path: " . $_SERVER['REQUEST_URI']);
$imgid = end(split('/', $_SERVER['REQUEST_URI']));
$imgid = current(split('.png', $imgid));
error_log("img: " . $imgid);

// Connect to Mongo and set DB and Collection
$mongo = new Mongo();
$db = $mongo->imgtest;

// GridFS
$gridFS = $db->getGridFS();

// Find image to stream
//$image = $gridFS->findOne("daniel.png");
$image = $gridFS->findOne(array("_id"=>new MongoId($imgid)));

// Stream image to browser
header('HTTP/1.1 200 OK');
header('Content-type: image/png');
$image->write($imgid.'.png');
echo $image->getBytes();
?>