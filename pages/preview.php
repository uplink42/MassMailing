<html>
<head>
    <meta charset="UTF-8">
    <title>Preview</title>
    
<?php
require_once('../class/link.php');
require_once('../class/utils.php');
require_once('../mailer/PHPMailerAutoload.php');

    $link = new link();
    $con = $link->connect();
    $con->set_charset("utf8");
	$body = "";
	
	function file_get_contents_utf8($fn) 
    {
     $content = file_get_contents($fn);
      return mb_convert_encoding($content, 'UTF-8',
          mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
    }


	$subject_form = $_POST['subject'];
		
		$checkedGroups = array();
		
		for ($i=3;$i<=15;$i++)
		{
			if(!empty($_POST[$i]))
			{
				$val = $_POST[$i];
				array_push($checkedGroups,$val);
			}
		}
		
		$checkedGroupsVal = "(" . implode(',',$checkedGroups) . ")";
		$number = utils::mysqli_result(mysqli_query($con, "SELECT COUNT(email) FROM addresses WHERE list_idlist IN $checkedGroupsVal"),0,0);	
	
	if (empty($subject_form ))
	{
		echo "Subject is empty. Try again.";
		exit();
	}
	
	
	

    $target_base_path = "uploads/";
	

    $target_path = $target_base_path . basename( $_FILES['uploadedfile']['name']);
	$target_path2 = $target_base_path . basename( $_FILES['uploadedfile2']['name']); 	

    $MailFileType = pathinfo($target_path,PATHINFO_EXTENSION);
	$imageFileType = pathinfo($target_path2,PATHINFO_EXTENSION);
    
    if($MailFileType != "htm" && $MailFileType != "html") 
    {
        echo "Sorry, only HTM or HTML files allowed. You must save your document in Word as WEB PAGE (FILTERED)". "<br>";
		echo "<a href='newsletter.php'>Try again</a>";
        exit();
    //$uploadOk = 0;
    }
	
	if(!empty($imageFileType) && $imageFileType != "jpg" && $imageFileType != "gif" && $imageFileType != "jpeg" && $imageFileType != "png")
    {
        echo "Sorry, only image files allowed". "<br>";
		echo "<a href='newsletter.php'>Try again</a>";
        exit();
    //$uploadOk = 0;
    }

    if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) 
    {
        echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
        " has been uploaded". "<br>";
    } 
    else
    {   
        echo "Your Word File is too big. Maximum size is 300 KB". "<br>";
		echo "<a href='newsletter.php'>Try again</a>";
		 exit();
    }
	
	//echo $_FILES ["uploadedfile2"]["error"];
	if($_FILES ["uploadedfile2"]["error"] == UPLOAD_ERR_OK && move_uploaded_file($_FILES['uploadedfile2']['tmp_name'], $target_path2))
	{
		echo "The file ".  basename( $_FILES['uploadedfile2']['name']). 
		" has been uploaded". "<br>";
	}
	else
	{
		if($_FILES ["uploadedfile2"]["error"] != 4)
		{
			echo "Image file is too big (maximum of 300 KB allowed)<br>";
			echo "<a href='newsletter.php'>Try again</a>";
			exit();
		}
	}
	
	//FROM INBOX AND SIGNATURES
	if(isset($_POST['from']))
	{
		$fromStr = $_POST['from'];

		$from_name = utils::mysqli_result(mysqli_query($con, "SELECT name FROM inbox WHERE idinbox = '$fromStr'"),0,0);
		$from = utils::mysqli_result(mysqli_query($con, "SELECT email FROM inbox WHERE idinbox = '$fromStr'"),0,0);
		$signature = utils::mysqli_result(mysqli_query($con, "SELECT signature FROM inbox WHERE idinbox = '$fromStr'"),0,0);
	}


			
			
	echo "<br>E-mail preview:<br>";
	echo "<b>Subject: </b>" . $subject_form . "<br>";
	
	$getToGroups = mysqli_query($con, "SELECT name FROM list WHERE idlist IN $checkedGroupsVal") or die(mysqli_error($con));
	$grpStr = "";
	
	while($groups=mysqli_fetch_array($getToGroups))
	{
		$grp = $groups['name'];
		$grpStr .= $grp . " ";
	}
	
	
	echo "<b>To: </b>" . $grpStr;

	ob_start();
    echo file_get_contents_utf8($target_path);
	
    $body = ob_get_contents();
	
			$greetingStr = "";
			$greetingName = "";
			
			$greeting = $_POST['greeting'];
			$title = $_POST['title'];
			
			!empty($_POST['greeting']) ? $greetingStr = $_POST['greeting'] : $finalGreeting = "";
	
			($_POST['title'] != 'none') ? $greetingName = "[first name]" : $greetingName = "";
	
			!empty($_POST['greeting']) ? $finalGreeting = $greetingStr . " ". $greetingName . "<br>" : $finalGreeting = "";
	 
			//echo $finalGreeting;
			
			$bodyFinal = $finalGreeting . $body;
			
			
	$bodyFinal .= $signature;
    ob_end_clean();	
	
	echo "<br>".$bodyFinal;
	
?>

<form enctype="multipart/form-data" action="upload2.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="310000" />
<input type="hidden" size = "50" name="subject" value=<?php echo $subject_form?> /> <br> <br>
<input type="hidden" name="uploadedfile" value=<?php echo $_FILES['uploadedfile']['tmp_name']?> type="file" /><br />
<input type="hidden" name="uploadedfile2" value=<?php echo $_FILES['uploadedfile2']['tmp_name']?> type="file" /><br />
<input type="hidden" size = "50" name="checkedgroups" value=<?php echo $checkedGroupsVal?> /> <br> <br>
<input type="hidden" size = "20" name="greeting" value=<?php echo $greeting?> /> 
<input type="hidden" size = "20" name="title" value=<?php echo $title?> /> 
<input type="hidden" size = "20" name="from" value=<?php echo $fromStr?> /> 


<b>7.</b> Make sure everything is correct before pressing Send. <b>Don't close this window while the script is in progress.</b> This can take a long time to process for a lot of destinations.<br><br><input type="submit" value="Send" />
</form>

	
	
	
			
