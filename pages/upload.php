<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 0);

require_once('../mailer/PHPMailerAutoload.php');
require_once('../class/link.php');
require_once('../class/utils.php');
ignore_user_abort(true);

$link = new link();
$con = $link->connect();
$con->set_charset("utf8");
$body = "";
$signature = "";
$q = mysqli_set_charset($con, 'utf8');

function smtpmailer($to, $from, $from_name, $subject, $body, $file) {
    global $error;
    $mail = new PHPMailer();  // create a new object
    $mail->CharSet = "UTF-8"; //Important for Portuguese mailings
    $mail->IsSMTP(); // enable SMTP
    $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true;  // authentication enabled
    $mail->SMTPSecure = 'tls';   // secure transfer enabled
    $mail->Host = '';
    $mail->Port = ;
    $mail->Username = GUSER;
    $mail->Password = GPWD;
    $mail->SetFrom($from, $from_name);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);
    $mail->AddAttachment($file);
    $mail->IsHTML(true);
    $mail->AltBody = strip_tags($body);
    $mail->SMTPDebug = 0; //Used for verbose debugging
    if (!$mail->Send()) {
        //echo $error = 'Mail error: '.$mail->ErrorInfo; 
        $error = "Error sending email to '.$to";
        echo "<br>";
        return false;
    } else {
        echo $error = 'Message sent to ' . $to;
        echo "<br>";
        return true;
    }
}

function file_get_contents_utf8($fn) {
    $content = file_get_contents($fn);
    return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}

// Where the file is going to be placed 

/* Add the original filename to our target path.  
  Result is "uploads/filename.extension" */
//$group = $_POST['list'];
$subject_form = $_POST['subject'];
$checkedGroups = array();

$totalgroups = utils::mysqli_result(mysqli_query($con, "SELECT max(idlist) FROM list"), 0, 0); // WARNING:
for ($i = 1; $i <= $totalgroups; $i++) {
    if (!empty($_POST[$i])) {
        $val = $_POST[$i];
        array_push($checkedGroups, $val);
    }
}

$checkedGroupsVal = "(" . implode(',', $checkedGroups) . ")";
$number = utils::mysqli_result(mysqli_query($con, "SELECT COUNT(email) FROM addresses WHERE list_idlist IN $checkedGroupsVal"), 0, 0);

if (empty($subject_form)) {
    echo "Subject is empty. Try again.";
    exit();
}

$target_base_path = "uploads/";

$target_path = $target_base_path . basename($_FILES['uploadedfile']['name']);
$target_path2 = $target_base_path . basename($_FILES['uploadedfile2']['name']);

$MailFileType = pathinfo($target_path, PATHINFO_EXTENSION);
$imageFileType = pathinfo($target_path2, PATHINFO_EXTENSION);

if ($MailFileType != "htm" && $MailFileType != "html") {
    echo "Sorry, only HTM or HTML files allowed. You must save your document in Word as WEB PAGE (FILTERED)" . "<br>";
    echo "<a href='newsletter.php'>Try again</a>";
    exit();
    //$uploadOk = 0;
}

if (!empty($imageFileType) && $imageFileType != "jpg" && $imageFileType != "gif" && $imageFileType != "jpeg" && $imageFileType != "png") {
    echo "Sorry, only image files allowed" . "<br>";
    echo "<a href='newsletter.php'>Try again</a>";
    exit();
    //$uploadOk = 0;
}

if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
    echo "The file " . basename($_FILES['uploadedfile']['name']) .
    " has been uploaded" . "<br>";
} else {
    echo "Your Word File is too big. Maximum size is 300 KB" . "<br>";
    echo "<a href='newsletter.php'>Try again</a>";
    exit();
}

//echo $_FILES ["uploadedfile2"]["error"];
if ($_FILES ["uploadedfile2"]["error"] == UPLOAD_ERR_OK && move_uploaded_file($_FILES['uploadedfile2']['tmp_name'], $target_path2)) {
    echo "The file " . basename($_FILES['uploadedfile2']['name']) .
    " has been uploaded" . "<br>";
} else {
    if ($_FILES ["uploadedfile2"]["error"] != 4) {
        echo "Image file is too big (maximum of 300 KB allowed)<br>";
        echo "<a href='newsletter.php'>Try again</a>";
        exit();
    }
}

//FROM INBOX AND SIGNATURES
if (isset($_POST['from'])) {
    $fromStr = $_POST['from'];

    $from = utils::mysqli_result(mysqli_query($con, "SELECT email FROM inbox WHERE idinbox = '$fromStr'"), 0, 0);
    $signature = utils::mysqli_result(mysqli_query($con, "SELECT signature FROM inbox WHERE idinbox = '$fromStr'"), 0, 0);

    if (!empty($_POST['name_inbox'])) {
        $from_name = $_POST['name_inbox'];
    } else {
        $from_name = utils::mysqli_result(mysqli_query($con, "SELECT name FROM inbox WHERE idinbox = '$fromStr'"), 0, 0);
    }
} else {
    echo "No sender specified.";
    return;
}

$usermailbox = utils::mysqli_result(mysqli_query($con, "SELECT username FROM inbox WHERE idinbox = '$fromStr'"), 0, 0);
$passwordmailbox = utils::mysqli_result(mysqli_query($con, "SELECT password FROM inbox WHERE idinbox = '$fromStr'"), 0, 0);
define('GUSER', $usermailbox); //mail server authentication
define('GPWD', $passwordmailbox);

echo "<br>";

ob_start();
echo file_get_contents_utf8($target_path);

$body = ob_get_contents();

ob_end_clean();
$subject_form2 = mysqli_real_escape_string($con, $_POST['subject']);


$get_emails_group = mysqli_query($con, "SELECT * FROM addresses WHERE list_idlist IN $checkedGroupsVal")
        or die(mysqli_error($con));

$dt = new DateTime();
$tz = new DateTimeZone('Europe/Lisbon');
$dt->setTimezone($tz);
$datetime = $dt->format('Y-m-d H:i:s');

//create newsletter entry
mysqli_query($con, "INSERT INTO newsletter (idnewsletter, title, datetime) VALUES ('NULL','$subject_form2','$datetime')")
        or die(mysqli_error($con));
$last_id = $con->insert_id;

while ($emails = mysqli_fetch_array($get_emails_group)) {
    echo $id = $emails['idaddresses'];
    $firstname = $emails['firstname'];
    $surname = $emails['surname'];
    $email = $emails['email'];

    $file = $target_path2;
    //$from = "pr@savoyresort.com";
    //$from_name = "Maria do Carmo";
    $subject = $subject_form;
    $to = $email;
    $greetingStr = "";
    $greetingName = "";

    !empty($_POST['greeting']) ? $greetingStr = $_POST['greeting'] : $finalGreeting = "";

    ($_POST['title'] != 'none') ? $greetingName = utils::mysqli_result(mysqli_query($con, "SELECT firstname FROM addresses WHERE idaddresses = '$id'"), 0, 0) : $greetingName = "";

    !empty($_POST['greeting']) ? $finalGreeting = $greetingStr . " " . $greetingName . "," . "<br>" : $finalGreeting = "";

    //echo $finalGreeting;
    $finalFormatGreeting = "<font face='arial' size='3'>" . $finalGreeting . "</font>";
    $bodyFinal = $finalFormatGreeting . $body . "<br>" . $signature;
    
    //echo $finalGreeting;
    //$bodyFinal = $finalGreeting . $body;
//echo $body;
    $sendmail = smtpmailer($to, $from, $from_name, $subject, $bodyFinal, $file);
    //sleep(1);

    $dt = new DateTime();
    $tz = new DateTimeZone('Europe/Lisbon');
    $dt->setTimezone($tz);
    $datetime = $dt->format('Y-m-d H:i:s');

    if ($sendmail) {
        mysqli_query($con, "INSERT INTO log (idlog, send_result, read_result, newsletter_idnewsletter, addresses_idaddresses, time_sent)
				VALUES ('NULL', '1', '0', '$last_id', '$id', '$datetime')") or die(mysqli_error($con));
    } else {
        mysqli_query($con, "INSERT INTO log (idlog, send_result, read_result, newsletter_idnewsletter, addresses_idaddresses, time_sent)
				VALUES ('NULL', '0', '0', '$last_id', '$id', '$datetime')") or die(mysqli_error($con));
    }
}

echo "End. You can close this window now" . "<br>";

//Send confirmation message to sender
$to = utils::mysqli_result(mysqli_query($con, "SELECT email FROM inbox WHERE idinbox = '$fromStr'"), 0, 0);
$number = utils::mysqli_result(mysqli_query($con, "SELECT COUNT(email) FROM addresses WHERE list_idlist IN $checkedGroupsVal"), 0, 0);
$confirmSubject = $subject . "(sent to " . $number . " destinations";
smtpmailer($to, $from, $from_name, $confirmSubject, $body, $file);
?>