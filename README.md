https://www.youtube.com/user/PurnamaABC

https://www.slideshare.net/0DarkKing0/rdiff-and-rsync-implementation-on-moodles-backup-and-restore-feature-of-course-synchronization-over-the-network-presentation

https://www.slideshare.net/0DarkKing0/compatible-course-content-synchronization-model-for-course-distribution-over-the-network-for-tokuen-2016

https://www.slideshare.net/0DarkKing0/portable-and-synchronized-distributed-learning-management-system-in-severe-networked-regions

# file-synchronizer-online-course-sharing
Designed for online course synchronization (updater) written based on rsync algorithm in php.

# General Method Outline

1. Export both course to archive from the LMS. (output usually in form of .zip, .tar.gz, .mbz, etc).
2. Perform rsync algorithm on both archive, for example using rdiff:  
2.a Produce a signature of the client's archive.  
2.b Send signature to the server.  
2.c Use the signature onto the server's archive to produce the delta.  
2.d Send the delta to the client.  
2.e Patch the client's archive using the delta.  
3. Import client's archive to the LMS.

# Dependencies for using these scripts.

apache2, php, php-curl, librsync, rdiff, rdiff, duplicity, MOOSH (will be implemented in the future)

# Disclaimer

These scripts are still beta versions, which in the future will be mold into a plugin for each LMS. In other words finalization needs to wait. COLLABORATIONS AND CONTRIBUTIONS ARE WELCOMED!

# Usage (Beta)

1. Place these scripts into a web server and access the folder via browser. The scripts are tested with ownership of the directory is the webserver (chown -R www-data directory), or simply allow full access if you don't care (chmod -R 777 directory).

Manual: http based rsync between two file.
Moodle: environment set on moodle. (able to identify course, perform automatic backup, in the future perform automatic restore, function to rsync all course at once comming soon).

If manual is chosen:

2. Create a profile (usually course name), refresh, and access the profile.
3. Manually backup the course from any LMS in .mbz, .zip, .tar.gz, or .tar.bz2 format and upload it here.
4. If this is a master server click the master button and it will generate a special script for master and an url to be given to slave server.
5. On the slave server go to settings and input the url given by the master server. 
6. Press update button to synchronize the .mbz archive (if no .mbz archive was uploaded it will manually download the one on the master server). There are also many buttons for debugging which in order are the processes run by the update button.
7. Rdiff method is the default but using the rdiffdir method is less costly however may need root permission to run. Upon running it will test if it is given permission or not. If not it will suggest to run 'visudo' and add 'www-data' ALL=NOPASSWD: /usr/bin/rdiffdir' at the end of the line.
8. The split option is split the download which can be split as desired (for now) where the advantage is that the update process can be continued when the process is interrupted. Currently using MD5 for integrity check.
9. Import the archive back to the LMS.

If Moodle is chosen.

2. It is needed to create a directory fajar-moodle-sync owned by www-data under moodle_directory/local, a notice is also given on this section (i.e. sudo mkdir /var/www/html/moodle/local/fajar-moodle-sync; sudo chown www-data:www-data /var/www/html/moodle/local/fajar-moodle-sync). This step is unnecessary once it is a moodle plugin, or please inform if you have a better way.
3. Go to setting and set the Moodle directory location (i.e. /var/www/html/moodle) and Moodle url (i.e. md.hicc.cs.kumamotou.ac.jp, another i.e. 127.0.0.1/fajar_moodle).
4. If succeeded the click generate course list button. If this doesn't work, try the return button and go back to setting menu, check if i.e. /var/www/html/moodle/local/fajar-moodle-sync/get_course_id.php script exist, then access this location via browser manually. The main menu will have the list of the course available on the Moodle site (to add more list, manually create new course on Moodle and re-run step 3 again).
5. After choosing a menu there will be a button to automatically generate a course backup using
(moodle/admin/cli/backup.php) where the options are the default backup option on moodle's admin section, click it, otherwise manually uploading the .mbz file is also possible.
6. If this is a master server click the master button and it will generate a special script for master and an url to be given to slave server.
7. On the slave server go to settings and input the url given by the master server. 
8. Press update button to synchronize the .mbz archive (if no .mbz archive was uploaded it will manually download the one on the master server). There are also many buttons for debugging which in order are the processes run by the update button.
9. Rdiff method is the default but using the rdiffdir method is less costly however may need root permission to run. Upon running it will test if it is given permission or not. If not it will suggest to run 'visudo' and add 'www-data' ALL=NOPASSWD: /usr/bin/rdiffdir' at the end of the line.
10. The split option is split the download which can be split as desired (for now) where the advantage is that the update process can be continued when the process is interrupted. Currently using MD5 for integrity check.
11. Import the archive back to the LMS.
