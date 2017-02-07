<?php
 class backup {
   // Creating some properties (variables tied to an object)
            public $backup_dir;

   // Assigning the values
            public function __construct($backup_dir, $method, $url, $content_url) {
              $this->backup_dir = $backup_dir;
              $this->method = $method;
	      $this->url = $url;
	      $this->content_url = $content_url;
            }   
  
   // Creating a method (function tied to an object)
	    public function make_signature() {
              foreach (glob($this->backup_dir.'/*.sig') as $filename) {    
                if (file_exists($filename)) {
	          unlink($filename);
	        }
              }

	      foreach (glob($this->backup_dir.'/backup.*') as $filename) {
                if (file_exists($filename)) {
                  if (strcmp($this->method,'rdiffdir') == 0){
                    exec("rm -r $this->backup_dir/backup");
		    mkdir($this->backup_dir.'/backup', 0777);
		    if ((strcmp(pathinfo($filename, PATHINFO_EXTENSION),'mbz')==0) || (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'gz')==0) || (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'bz2')==0)) {
		      exec("tar xf $filename -C $this->backup_dir/backup");
		    } elseif (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'zip')==0) {
                      exec("unzip $filename -d $this->backup_dir/backup");
                    } else {
		      echo '<script> alert("cannot extract, unsupported archive format, use regular rdiff") </script>';
		    }
		    exec("chmod -R 777 $this->backup_dir/backup; rdiffdir signature $this->backup_dir/backup $filename.sig");
		    chmod($filename.sig, 0777);
                  } else {
	            exec("rdiff signature $filename $filename.sig");
                  }
	        }
              }
	    }

	    public function send_signature() {
	      foreach (glob($this->backup_dir.'/*sig') as $filename) {
                if (file_exists($filename)) {
	          $target_url = $this->url.'rdiff_make_patch.php';
	          $file_name_with_full_path = realpath($filename);
 	          $post = array('extra_info' => '123456','file_contents'=>new \CURLFile($file_name_with_full_path), 'method' => $this->method);
	          $ch = curl_init();
 	          curl_setopt($ch, CURLOPT_URL,$target_url);
 	          curl_setopt($ch, CURLOPT_POST,1);
 	          curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
 	          curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
 	          $result=curl_exec ($ch);
 	          curl_close ($ch);
 	          echo $result;
                } 
	      }
	    }

	    public function receive_signature() {
	      foreach (glob($this->backup_dir.'/*sig') as $filename) {		    
	        if (file_exists($filename)) {
		  unlink($filename);
		}
	      }
                $uploaddir = realpath('./').'/';
                $uploadfile = $uploaddir.basename($_FILES['file_contents']['name']);
                echo '<pre>';
	        if (move_uploaded_file($_FILES['file_contents']['tmp_name'], $uploadfile)) {
	          echo 'File is valid, and was successfully uploaded.\n';
	        } else {
	          echo 'Possible file upload attack!\n';
	        }
	        echo 'Here is some more debugging info:';
	        print_r($_FILES);
	        echo "\n<hr />\n";
	        print_r($_POST);
		print "</pr" . "e>\n";
		chmod($uploadfile, 0777);
            }
	    
	    public function generate_delta() {
              foreach (glob('*delta') as $filename) {
                if (file_exists($filename)) {
		  unlink($filename);
		}
	      }
	      foreach (glob('backup.*') as $filename) {
		if (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'sig')!=0) {
		  if (strcmp($this->method,"rdiffdir") == 0){
		    exec("rm -r backup");	
		    mkdir('backup', 0777);	
		    if ((strcmp(pathinfo($filename, PATHINFO_EXTENSION),'mbz')==0) || (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'gz')==0) || (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'bz2')==0)) {
		      exec("tar xf $filename -C backup");
		    } elseif (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'zip')==0) {
                      exec("unzip $filename -d backup");
                    } else {
		      echo 'cannot extract, unsupported archive format, use regular rdiff';
		    }     
		    exec("chmod -R 777 backup; rdiffdir delta $filename.sig backup $filename.delta");
		    chmod($filename.delta, 0777);
                  } else {
                    exec("rdiff delta $filename.sig $filename $filename.delta");
                  }
	        }
	      }
	      if (file_exists('/md5sum')) {
		  unlink('/md5sum');
	      }
	      foreach (glob('*delta') as $filename) {
	        $md5file = fopen("/md5sum", "w") or die("Unable to open file!");
	        fwrite($md5file, md5_file($filename));
	        fclose($md5file);
	      }
	    }

	    public function download_patch() {
              if (file_exists($this->backup_dir.'/md5sum')) {
		  unlink($this->backup_dir.'/md5sum');
	      }
	      //This is the file where we save the    information
	      $fp = fopen ($this->backup_dir.'/md5sum','w+');
              $url = $this->url.'md5sum';
	      $headers=get_headers('http://'.$url);
              $start_time = time();
              while (!stripos($headers[0],"200 OK")){
		$headers=get_headers('http://'.$url);
                if ((time() - $start_time) > 20) {
      		  return false; // timeout, function took longer than 300 seconds
    		}
              }
	      //Here is the file we are downloading, replace spaces with %20
	      $ch = curl_init(str_replace(" ","%20",$url));
	      curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	      // write curl response to file
	      curl_setopt($ch, CURLOPT_FILE, $fp); 
	      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	      // get curl response
	      curl_exec($ch); 
	      curl_close($ch);
	      fclose($fp);

              foreach (glob($this->backup_dir.'/backup.*') as $filename) {
	        foreach (glob($this->backup_dir.'/*.sig') as $signature) {
		  if (file_exists($signature)) {
	            if ((strcmp(pathinfo($filename, PATHINFO_EXTENSION),'sig')!=0) && (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'delta')!=0) && (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'backup')!=0)) { 		
	            //This is the file where we save the    information
	              $fp = fopen($filename.'.delta','w+');
		      if ((strcmp(pathinfo($filename, PATHINFO_EXTENSION),'gz')==0) || (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'bz2')==0)) { 
		        $url = $this->url.'/backup.tar.'.pathinfo($filename, PATHINFO_EXTENSION).'.delta';
		      } else {
		        $url = $this->url.'/backup.'.pathinfo($filename, PATHINFO_EXTENSION).'.delta';
		      }
		      //Here is the file we are downloading, replace spaces with %20
	              $ch = curl_init(str_replace(" ","%20",$url));
	              curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	              // write curl response to file
	              curl_setopt($ch, CURLOPT_FILE, $fp); 
	              curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	              // get curl response
	              curl_exec($ch); 
	              curl_close($ch);
		      fclose($fp);
		    }
       		  }
	        }
	      }
	    }
	    
	    public function get_content() {
              //This is the file where we save the    information
	      $extension = pathinfo($this->content_url, PATHINFO_EXTENSION);
	      $fp = fopen ($this->backup_dir.'/backup.'.$extension,'w+');
              $url = $this->content_url;
	      //Here is the file we are downloading, replace spaces with %20
	      $ch = curl_init(str_replace(" ","%20",$url));
	      curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	      // write curl response to file
	      curl_setopt($ch, CURLOPT_FILE, $fp); 
	      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	      // get curl response
	      curl_exec($ch); 
	      curl_close($ch);
	      fclose($fp);
	      //echo '<br>'.$this->content_url;
		
	      //This is the file where we save the    information
	      $fp = fopen ($this->backup_dir.'/md5sum','w+');
              $url = $this->url.'/md5sum';
	      //Here is the file we are downloading, replace spaces with %20
	      $ch = curl_init(str_replace(" ","%20",$url));
	      curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	      // write curl response to file
	      curl_setopt($ch, CURLOPT_FILE, $fp); 
	      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	      // get curl response
	      curl_exec($ch); 
	      curl_close($ch);
	      fclose($fp);
	    }

	    public function apply_patch() {
	      foreach (glob($this->backup_dir.'/backup.*') as $filename) {
	        foreach (glob($this->backup_dir.'/*.delta') as $delta) {	      
	       	  if (file_exists($delta)) {
		    if ((strcmp(pathinfo($filename, PATHINFO_EXTENSION),'sig')!=0) && (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'delta')!=0) && (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'backup')!=0)) { 	      
	              if (strcmp($this->method,'rdiffdir')==0) {
                        exec("sudo rdiffdir signature $this->backup_dir/backup $this->backup_dir/test.sig");
                        if(file_exists($this->backup_dir.'/test.sig')){
                          rename($filename,$filename.'.backup');
			  unlink($this->backup_dir.'/test.sig');
			  chmod($filename.'.delta', 0777);
			  exec("chmod -R 777  $this->backup_dir/backup; sudo rdiffdir patch $this->backup_dir/backup $filename.delta");
			  if ((strcmp(pathinfo($filename, PATHINFO_EXTENSION),'mbz')==0) || (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'gz')==0)) {
			    exec("tar cfz $this->backup_dir/backup.tar.gz $this->backup_dir/backup");
			    if ((strcmp(pathinfo($filename, PATHINFO_EXTENSION),'mbz')==0)) {
                              rename($this->backup_dir.'/backup.tar.gz',$filename);
			    }
			  } elseif (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'bz2')==0) {
			    exec("tar cfj $this->backup_dir/backup.tar.bz2 $this->backup_dir/backup");
		          } elseif (strcmp(pathinfo($filename, PATHINFO_EXTENSION),zip)==0) {
                            exec("zip -r $this->backup_dir/backup.zip $this->backup_dir/backup");
                          } else {
		            echo 'Unknown format';
			  }
			  echo "update complete";
                        } else {
                          echo 'rdiffdir execution not permitted, add this (www-data ALL=NOPASSWD: /usr/bin/rdiffdir
) to /etc/sudoers via visudo ... Terminated';
                        }
                      } else {
                        rename($filename,$filename.'.backup');
	                exec("rdiff patch $filename.backup $filename.delta $filename");
                        echo "update complete";
                      }
                    }
		  }
                }	
              }
            }	

	public function check_md5() {
	  if (file_exists($this->backup_dir.'/md5sum')) {
	    foreach (glob($this->backup_dir.'/*.delta') as $filename) {
	      echo '<br>';
	      echo $filename.' md5 checksum = '.$md5clientfile = md5_file($filename);
	      echo '<br>';
	      echo 'server file md5 checksum = '.$md5serverfile = file_get_contents($this->backup_dir.'/md5sum');
	      echo '<br>';
	      if (strcmp($md5clientfile,$md5serverfile)==0) {
		echo 'md5 checksum match';
	      } else {
		echo 'md5 checksum mismatch';
	      }
	    }
	  }
	}
  }
?>
