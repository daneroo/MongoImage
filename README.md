# Copying sample images

    # on cantor
    cd /home/daniel/mosaic/ginette/tint
    mkdir /tmp/coco
    find -name "*-[0-9][0-9][0-9]-100-100*" -exec cp {} /tmp/coco \;

    #on local (dirac)
    mkdir -p /Users/daniel/coco/imgs
    cd /Users/daniel/coco/imgs
    rsync -e ssh -av --progress 192.168.5.2:/tmp/coco/ .

    # now insert into imgtest db, fs.[files|chunks] collection
    echo "db.fs.files.remove();" mongo imgtest
    echo "db.fs.chunks.remove();" mongo imgtest
    find . -type f -exec mongofiles -d imgtest put {} \;

    #how about dump restore the database
    mongodump -d imgtest -o imgtest-mongodump
    echo "db.dropDatabase();" mongo imgtest
    mongorestore -d imgtest imgtest-mongodump/imgtest

    #how about restoring the database to ci.axialdev.net
    -h i.axialdev.net

    # append to /etc/apache2/sites-available/default - allow .htaccess
    <Directory /var/www/MongoImage>
      AllowOverride FileInfo
    </Directory>
    
    # media/.htaccess
    ErrorDocument 404 /MongoImage/media/cache.php

    # rsync
    cd ~/Sites
    rsync --exclude .htaccess -av --progress MongoImage/ root@ci.axialdev.net:/var/www/MongoImage/


Cleanup:
  remove imgtest db on dirac and ci.axialdev.net
  remove AllowOverride in /etc/apache2/sites-available/default
  remove /var/www/MongoImage
  remove dirac:~/coco/...
  remove dirac:~/Sites/MongoImages/...
    