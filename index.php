<?php
$dbName = "imgtest";
$collName = "images";


$m = new Mongo("mongodb://localhost/", array("persist" => "onlyone"));
$db = $m->selectDB("imgtest");

function clearCache() {
    foreach (glob("media/*.png") as $filename) {
        error_log('deleteing ' . $filename);
        unlink($filename);
    }
}

function clearImages($db, $collName) {
    $filesColl = $db->selectCollection($collName . ".files");
    $filesColl->drop();
    $chunksColl = $db->selectCollection($collName . ".chunks");
    $chunksColl->drop();
}

function encodeImage($img) {
    $fd = fopen($img, 'rb');
    $size = filesize($img);
    $cont = fread($fd, $size);
    fclose($fd);
    $encimg = base64_encode($cont);
    return $encimg;
}

function uploadImages($db, $collName) {
    $dir = '/Users/daniel/coco/imgs';
    $fnames = array('catou', 'daniel', 'felix', 'laurence');
    // GridFS
    $gridFS = $db->getGridFS($collName);
    foreach ($fnames as $fname) {
        $fullfname = $dir . '/' . $fname . '.png';
        error_log('uploading ' . $fname);
        // err
        $info = getimagesize($fullfname);
        $extra = array(
            "filename" => $fname . '.png',
            "uploadDate" => new MongoDate(),
            "metadata" => array(
                "eko" => true,
                "width" => $info[0],
                "height" => $info[1],
                "mime" => $info['mime']
            )
        );

        //var_dump($info);

        $gridFS->storeFile($fullfname, $extra);

        $b64 = encodeImage($fullfname);
        // here is where we transport!
        $decoded = base64_decode($b64);
        $gridFS->storeBytes($decoded, $extra);
    }
}

clearCache();
clearImages($db, $collName);
uploadImages($db, $collName);

$collection = $db->selectCollection($collName . ".files");
?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <pre style="ZZdisplay: none;">
            <?php
            $query = null;
            $fields = null;
            $sortfields = array("md5" => -1);
            $sortfields = array("filename" => -1);
//$cursor = $collection->find($query, $fields);
            $cursor = $collection->find();
            $cursor->sort($sortfields);
            $images = array_values(iterator_to_array($cursor));

            echo "\n";
            foreach ($images as $image) {
                $image['_id'] = "" . $image['_id'];
                echo json_encode($image) . "\n";
            }
            ?>
        </pre>
        <?php
        $i = 0;
        foreach ($images as $image) {
            if (($i % 6) == 0) {
                echo '<div style="clear:both;"></div>' . "\n";
            }
            $i++;
            echo '<div style="float:left;"><img width="100" height="100" src="media/' . $image['_id'] . '.png" alt="Angry face" /></div>' . "\n";
            //echo '<div style="float:left;"><img src="media/'.$image['_id'].'" alt="Angry face" /></div>'."\n";
        }
        ?>
    </body>
</html>
