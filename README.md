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

apache2, php, php-curl, librsync, rdiff, rdiff, duplicity
