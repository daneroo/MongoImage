<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <pre style="display: none;">
            <?php
            $m = new Mongo("mongodb://localhost/", array("persist" => "onlyone"));
            $db = $m->selectDB("imgtest");
            $collection = $db->selectCollection("fs.files");

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
