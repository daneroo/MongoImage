<?php

//error_log("path: " . $_SERVER['REQUEST_URI']);
$path = end(split('/', $_SERVER['REQUEST_URI']));
$pathinfo = pathinfo($path);
$imgid = basename($path,'.'.$pathinfo['extension']);
error_log("img._id: " . $imgid);

// Connect to Mongo and set DB and Collection
$mongo = new Mongo();
$db = $mongo->imgtest;

// GridFS
$gridFS = $db->getGridFS("images");

// Find image to stream
//$image = $gridFS->findOne("daniel.png");
$image = $gridFS->findOne(array("_id"=>new MongoId($imgid)));
//error_log("got: " . $imgid);
//error_log("json: " . json_encode($image->file));
//error_log("mime: " . $image["file"]["metadata"]["mime"]);
//error_log("ext: " . $image["file"]["metadata"]["ext"]);

$mime='image/png';
$ext=".png";
$mime = $image->file["metadata"]["mime"];
$ext = $image->file["metadata"]["ext"];

// Stream image to browser
header('HTTP/1.1 200 OK');
header('Content-type: '.$mime);
$image->write($imgid.'.'.$ext);
echo $image->getBytes();
?>