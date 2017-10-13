<?php
session_start();
require('lib_main.php'); 
?>

<html>
<head>
<title>LMS Backup Rdiff Synchronization</title>
<link rel="stylesheet" type="text/css" href="main.css"/>
</head>
<body>

<h1>LMS Backup Rdiff Synchronizer</h1>
<h3>Based on rdiff: a controlled rsync application.</h3>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
<select name="option">
  <option value="create_new_profile">create new profile</option>
  <?php foreach(glob('./*', GLOB_ONLYDIR) as $dir) {?>
    <option value ="<?php echo substr($dir,2);?>"><?php echo substr($dir,2);?></option>
  <?php } ?>
</select>
<input type="submit" value="choose">
</form><a href="../index.php"> Main Console</a>

<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  if (strcmp($_POST['option'],'create_new_profile')==0){
    ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
    new profile: <input type="text" name="new_profile">
    <input type="hidden" name="option" value="create_new_profile">
    <input type="submit" value="create profile">
    <?php
    $new_profile = $_POST['new_profile'];
    if (file_exists($new_profile)) {
      echo '<script> alert("Profile exist, make another name") </script>';
    } else {
      mkdir('./'.$new_profile);
    }
  } else {
    $_SESSION['option'] = $_POST['option'];
    ?>

    <form enctype="multipart/form-data" action="upload.php" method="POST">
    Insert Content Archive: <input name="userfile" type="file" />
    <input type="hidden" name="option" value="<?php echo $_POST['option'];?>">
    <input type="submit" value="upload" />
    </form>

    <table border="1" style="width:30%">
      <tr>
        <th>File</th>
	<th>
	  <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	      <select name="size" onchange="this.form.submit()">
                <option selected disabled><?php if (isset($_POST['size'])) { echo $_POST['size']; } else { echo 'Size'; }?></option>
		<option value="Byte">Byte</option>
		<option value="KiloByte">Kilo Byte</option>
		<option value="MegaByte">Mega Byte</option>
              </select>
	      <input type="hidden" name="option" value="<?php echo $_POST['option'];?>">
              <?php if (isset($_POST['file_action'])) { ?> <input type="hidden" name="file_action" value="<?php echo $_POST['file_action'];?>"> <?php } ?>
          </form>
        </th>
	<th>
          <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	      <select name="file_action"  onchange="this.form.submit()">
                <option selected disabled><?php if (isset($_POST['file_action'])) { echo $_POST['file_action']; } else { echo 'Action'; }?></option>
                <option value="Delete">Delete</option>
	      </select>
	      <input type="hidden" name="option" value="<?php echo $_POST['option'];?>">
	      <?php if (isset($_POST['size'])) { ?> <input type="hidden" name="size" value="<?php echo $_POST['size']; ?>"> <?php } ?>
          </form>
        </th>
      </tr>
      <?php foreach(array_diff(scandir($_POST['option']),array('..', '.')) as $file_name) {?>
      <tr>
	  <td><a href="<?php echo $_POST['option'] . '/' . $file_name;?>"> <?php echo $file_name ?></a></td>
	  <td><?php 
                if (strcmp($_POST['size'],'MegaByte')==0) {
		  echo filesize($_POST['option'] . '/' . $file_name) / 1024 / 1024;
		} elseif (strcmp($_POST['size'],'KiloByte')==0) {
		  echo filesize($_POST['option'] . '/' . $file_name) / 1024;
		} else {
		  echo filesize($_POST['option'] . '/' . $file_name);
		}
              ?>
          </td>
          <td>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	       <input type="hidden" name="the_file" value="<?php echo $_POST['option'] . '/' . $file_name;?>">
	       <input type="hidden" name="option" value="<?php echo $_POST['option'];?>">
	       <?php if (isset($_POST['file_action'])) { ?> <input type="hidden" name="file_action" value="<?php echo $_POST['file_action'];?>"> <?php } ?>
               <?php if (isset($_POST['size'])) { ?> <input type="hidden" name="size" value="<?php echo $_POST['size']; ?>"> <?php } ?>
               <input type="submit" value="execute">
            </form>
          </td>
      </tr>
      <?php
        } 
        if (strcmp($_POST['file_action'],'Delete')==0 && isset($_POST['the_file'])) {
	  unlink($_POST['the_file']);
	  if (file_exists($_POST['the_file'])) {
            $the_file = $_POST['the_file'];
	    exec("rm -r $the_file");
	  }
        }
      ?>
    </table>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
    <input type="hidden" name="option" value="<?php echo $_POST['option'];?>">
    <select name="method">
      <option value="rdiff">rdiff</option>
      <option value="rdiffdir">rdiffdir</option>
    </select>
    <?php 
      $filename = glob($_POST['option'].'/backup.*');
      if(count($filename)>0){ ?>
	   <select name="update" onclick="this.form.submit()">
      	     <option value="update">update (automatic)</option>
      	     <option value="make_signature">make signature (manual)</option>
	     <option value="send_signature">send signature (manual)</option>
	     <option value="get_delta">get delta (manual)</option>
	     <option value="apply_patch">apply patch (manual)</option>
	     <option value="test">test</option>
    	   </select>
    <?php
	} else { ?>
	  <input type="submit" name="get_content" value="get_content">
    <?php
	}
    ?>
          Split<input type="text" name="split">
    </form>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
    <input type="hidden" name="option" value="<?php echo $_POST['option'];?>">
    <input type="submit" name="master" value="master">
    <input type="submit" name="check_md5" value="check_md5">
    <input type="submit" name="undo" value="undo">
    <!--<input type="submit" name="test" value="test">-->
    </form>
    <button onclick="myFunction()">Refresh</button>

    <script>
    function myFunction() {
        location.reload();
    }
    </script>

    <a href="settings.php"> Settings</a> 
    <?php
    if (isset($_POST['update'])) {
      require($_POST['option'] .'/varset.php');
      $_SESSION['method'] = $_POST['method'];
      $manual_backup = new backup($_POST['option'], $_POST['method'], $_POST['split'], $url, $content_url);
      switch ($_POST['update']) {
      case 'update':
        $manual_backup->make_signature();
        $manual_backup->send_signature();
        $manual_backup->download_patch();
        $manual_backup->apply_patch();
        break;
      case ('make_signature'):
        $manual_backup->make_signature();
        break;
      case 'send_signature':
        $manual_backup->send_signature();
        break;
      case 'get_delta':
        $manual_backup->download_patch();
        break;
      case 'apply_patch':
        $manual_backup->apply_patch();
        break;
      case 'test':
	echo '<script> alert("'.$_POST['split'].'") </script>';
      default:
        //echo '<script> alert("'.$_POST['update'].'"); </script>';
        break;
      }
    }
    /*if(isset($_POST['update'])){
      require($_POST['option'] .'/varset.php');
      $method = $_POST['method'];
      $_SESSION['method'] = $method;
      $manual_backup = new backup($_POST['option'], $method, $url);
      $manual_backup->make_signature();
      $manual_backup->send_signature();
      echo '<script> alert("'.$_POST['update'].'"); </script>';
    }*/
    if(isset($_POST['get_content'])){
      require($_POST['option'] .'/varset.php');
      $method = $_POST['method'];
      $_SESSION['method'] = $method;
      $manual_backup = new backup($_POST['option'], $_POST['method'], $_POST['split'], $url, $content_url);
      $manual_backup->get_content();
      //echo "<br>".$content_url;
    }
    if(isset($_POST['master'])){
      if (!copy('rdiff_make_patch.php', $_POST['option'] . '/rdiff_make_patch.php')) {
        echo '<script> alert("failed to copy rdiff_make_patch.php... you need the right permission") </script>';
      } else {
        echo "<br> give this url to slave: " . $_SERVER[HTTP_HOST] . str_replace('rdiff_update_console.php', $_POST['option'], $_SERVER[REQUEST_URI]) . '/';
	foreach (glob($_POST['option'].'/backup.*') as $filename) {
	  if ((strcmp(pathinfo($filename, PATHINFO_EXTENSION),'sig')!=0) && (strcmp(pathinfo($filename, PATHINFO_EXTENSION),'delta')!=0)) {
	    echo "<br> This is the content's url: " . $_SERVER[HTTP_HOST] . str_replace('rdiff_update_console.php', $filename, $_SERVER[REQUEST_URI]);
	  }
	}
      }
    }
    if(isset($_POST['undo'])){
      foreach (glob($_POST['option'] . '/*.sig') as $filename) {    
        if (file_exists($filename)) {
	  unlink($filename);
	}
      }
      foreach (glob($_POST['option'] . '/*.delta') as $filename) {
        if (file_exists($filename)) {
	  unlink($filename);
        }
      }
      foreach (glob($_POST['option'] . '/*.backup') as $filename) {
        if (file_exists($filename)) {
	  rename($filename,str_replace(".backup", "", $filename));
	}
      }
    }
    if(isset($_POST['check_md5'])){
      require($_POST['option'] .'/varset.php');
      $method = $_POST['method'];
      $_SESSION['method'] = $method;
      $manual_backup = new backup($_POST['option'], $method, $url);
      $manual_backup->check_md5();
    }
    /*if(isset($_POST['test'])){
      $filename = glob($_POST['option'].'/backup.*');
	if(count($filename)>0){
	  echo $filename.'<br>';
	} else {
	  echo 'empty';
	}
      
    }*/
  }
 }      
?>
