<?php

$path = end(split('/', $_SERVER['REQUEST_URI']));
$pathinfo = pathinfo($path);
$imgid = basename($path,'.'.$pathinfo['extension']);
//error_log("img._id: " . $imgid);

// Connect to Mongo and set DB and Collection
$mongo = new Mongo();
$db = $mongo->imgtest;
$gridFS = $db->getGridFS("images");

// Find image to stream
$image = $gridFS->findOne(array("_id"=>new MongoId($imgid)));
$mime = $image->file["metadata"]["mime"];
$ext = $image->file["metadata"]["ext"];

// Stream image to browser
header('HTTP/1.1 200 OK');
header('Content-type: '.$mime);
// Cache the image for next time
$image->write($imgid.'.'.$ext);
// output the actual image bytes.
echo $image->getBytes();
?>