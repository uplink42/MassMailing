<head>
    <meta charset="UTF-8">
    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>Create Mailing | <a href='contacts.php'>Contacts Management</a> | <a href='statistics.php'>Statistics</a>
            </h3></div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <div class="dataTable_wrapper">


                <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Create Mailing</title>

                        <?php
                        require_once('../class/link.php');
                        require_once('../mailer/PHPMailerAutoload.php');

                        $link = new link();
                        $con = $link->connect();
                        $con->set_charset("utf8");
                        ?>

                    <form enctype="multipart/form-data" action="upload.php" method="POST">
                        <input type="hidden" name="MAX_FILE_SIZE" value="310000" />
                        <b>1.</b> Type your newsletter title (Subject): <input type="text" size = "50" name="subject" /> <br> <br>
                        <b>2.</b> Upload your newsletter text here (Word file saved as <b>Web Page, filtered</b>): <input name="uploadedfile" type="file" /><br />
                        <b>3.</b> (optional) Upload your image to attach to the newsletter here (maximum 300 KB): <input name="uploadedfile2" type="file" /><br />
                        <b>4.</b> Select the groups to deliver this newsletter to:


                        <?php
                        $lists = mysqli_query($con, "SELECT * FROM list ORDER BY name ASC");
                        while ($row = mysqli_fetch_array($lists)) {
                            $id = $row['idlist'];
                            $name = $row['name'];
                            //echo "<option value='$id'>$name</option>";
                            echo "<br>   " . "<input type='checkbox' name='$id' value='$id'>" . "   " . $name;
                        }
                        ?>


                        <br><br>

                        <b>5.</b> (optional) Customized greeting: <input type="text" size = "20" name="greeting" /> <select name='title'> <option value='none'>(none)</option> <option value='first'>First name </option></select> (example: Dear John)<br> 
                        <br>

                        <b>6.</b> From INBOX: <select name='from'> 
<?php
$getInboxes = mysqli_query($con, "SELECT * FROM inbox") or die(mysqli_error($con));
while ($row = mysqli_fetch_array($getInboxes)) {
    $id = $row['idinbox'];
    $name = $row['name'];
    $email = $row['email'];
    $nameEmail = $name . " (" . $email . ")";

    echo "<option value='$id'>$nameEmail</option>";
}
?>
                        </select><br><br>
                        <b>7.</b> (optional) From name: <input type="text" size = "20" name="name_inbox" /> (if left empty will use the default inbox names from above)
                        <br><br>
                        <b>8.</b> Make sure everything is correct before pressing Send. <b>Don't close this window while the script is in progress.</b> This can take a long time to process for a lot of destinations.<br><br><input type="submit" value="Send" />
                    </form>



            </div></div></div>
