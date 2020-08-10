<?php
if(isset($_POST["upload"])){
	$max_photo_size = 5000;
	$upload_required = true;
	$upload_page = 'upload.php';
	$upload_dir = './';
	$err_msg = false;
	do{
		if(!isset($_FILES['book_image'])){
			$err_msg = 'The form was not send in completely.';
			break;
		}else{
			$book_image = $_FILES['book_image'];
		}
		
		switch($book_image['error']){
			case UPLOAD_ERR_INI_SIZE:
				$err_msg = 'The size of the image is too large, it can not be more than '. $max_photo_size. 'bytes';
				break;
			case UPLOAD_ERR_PARTIAL:
				$err_msg="An error ocurred while uploading the file, please <a href='{$upload_page}'>Try again</a>.";
				break;
			case UPLOAD_ERR_NO_FILE:
				if($upload_required){
					$err_msg = "You did not select a file to be uploaded,please do so <a href='{$upload_page}'>Here</a>";
					break;
				}
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$err_msg = 'The size was too large according to the MAX_FILE_SIZE hidden field in the upload form.';
				break;
			case UPLOAD_ERR_OK:
				if($book_image['size'] > $max_photo_size){
					$err_msg = 'The size of the image is too large, it can not be more than '.$max_photo_size.'bytes';
					break;
				}
				break;
			default:
				$err_msg="An unknown error occurred, please do so <a href='{$upload_page}'>Here</a>";
		}

		if(!in_array($book_image['type'], array('image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'))){
			$err_msg = "You need to upload a PNG or JPEG image,do so <a href='{$upload_page}'>Here</a>";
			break;
		}

	}while(0);

	if(!$err_msg){
		if(!@move_uploaded_file($book_image['tmp_name'], $upload_dir.$book_image['name'])){
			$err_msg = "Error moving the file to its destination,".
            "please try again <a href='{$upload_page}'>here</a>.";
		}else{
			echo 'Upload Success!';
		}
	}else{
		echo $err_msg;
	}
	
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Upload File</title>
</head>
<body>
	<form method="post" enctype="multipart/form-data" action="upload.php">
		<input type="hidden" name="MAX_FILE_SIZE" value="16000" />
		Send this file:<input name="book_image" type="file" /><br />
		<input type="submit" name="upload" value="Upload" />
	</form>
</body>
</html>