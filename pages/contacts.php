<head>
    <meta charset="UTF-8">
    <title>Contacts Management</title>
    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="../bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="../bower_components/datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3><a href='newsletter.php'>Create Mailing</a> | Contacts Management | <a href='statistics.php'>Statistics</a>
            </h3></div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <div class="dataTable_wrapper">


                <?php
                require_once('../class/link.php');
                require_once('../class/utils.php');

                $link = new link();
                $con = $link->connect();
                $con->set_charset("utf8");
                ?>

                <form name="group" accept-charset='utf-8' action="contacts.php" method="GET">
                    <b>Go to mailing group:</b>
                    <select name="list">
                        <?php
                        $mailing_lists = mysqli_query($con, "SELECT * FROM list ORDER BY name ASC") or die(mysqli_error($con));

                        echo "<option value='0'>view group:</option>";

                        while ($row = mysqli_fetch_array($mailing_lists)) {
                            $idlist = $row['idlist'];
                            $name = $row['name'];

                            echo "<option value='$idlist'>" . $name . "</option>";
                        }
                        ?>
                    </select>
                    <input type="Submit" name="Submit" value="Go">
                </form>

                <?php
                if (isset($_GET['e'])) {
                    $ide = mysqli_real_escape_string($con, $_GET['e']);
                    $currentFirstname = utils::mysqli_result(mysqli_query($con, "SELECT firstname FROM addresses WHERE idaddresses = '$ide'"), 0, 0);
                    $currentSurname = utils::mysqli_result(mysqli_query($con, "SELECT surname FROM addresses WHERE idaddresses = '$ide'"), 0, 0);
                    $currentEmail = utils::mysqli_result(mysqli_query($con, "SELECT email FROM addresses WHERE idaddresses = '$ide'"), 0, 0);
                    $currentCompany = utils::mysqli_result(mysqli_query($con, "SELECT company FROM addresses WHERE idaddresses = '$ide'"), 0, 0);
                    $currentTitle = utils::mysqli_result(mysqli_query($con, "SELECT job_title FROM addresses WHERE idaddresses = '$ide'"), 0, 0);
                    $currentList = utils::mysqli_result(mysqli_query($con, "SELECT list_idlist FROM addresses WHERE idaddresses = '$ide'"), 0, 0);

                    //edit form
                    ?>
                    <form name="edit" accept-charset='utf-8' action="contacts.php?edit=true" method="POST">
                        <b>Edit contact</b><br> Surname*: <input type ="text" size="30" name="surname" required="required" value="<?php echo $currentSurname ?>">
                        <br>First name(s): <input type= "text" size="30" name="firstname" value="<?php echo $currentFirstname ?>">
                        <br>Email*: <input type="email" size="30" name="email" required="required" value="<?php echo $currentEmail ?>">
                        <br>Company: <input type= "text" size="30" name="company" value="<?php echo $currentCompany ?>">
                        <br>Title: <input type= "text" size="30" name="title" value="<?php echo $currentTitle ?>">
                        <br>Group*: <input type= "hidden" name="id" value="<?php echo $ide ?>">
                        <select name='list'>
    <?php
    $emailing_lists = mysqli_query($con, "SELECT * FROM list ORDER BY name ASC") or die(mysqli_error($con));
    while ($row = mysqli_fetch_array($emailing_lists)) {
        $idlist = $row['idlist'];
        $name = $row['name'];
        $currentList == $idlist ? $selectedStr = "selected" : $selectedStr = "";
        echo "<option value='$idlist' $selectedStr>" . $name . "</option>";
    }
    ?>
                        </select>
                        <br><br><input type="Submit" name="SubmitEdit" value="Update">                               
                    </form>
                    <br>*Surname, E-mail and group are mandatory fields.<br>
    <?php
} else {
    ?>    

                    <form name="add" accept-charset='utf-8' action="contacts.php" method="POST">
                        <b>Add contact:</b> <input type ="text" size="20" name="firstname"  placeholder="First names">
                        <input type= "text" size="15" name="surname" required="required" placeholder="Surname">
                        <input type="email" size="35" name="email" required="required" placeholder="E-mail address">
                        <input type= "text" size="20" name="company" placeholder="Company">
                        <input type= "text" size="15" name="title" placeholder="Job title">
                        <select required name='list'>
    <?php
    $mailing_lists = mysqli_query($con, "SELECT * FROM list ORDER BY name ASC") or die(mysqli_error($con));
    echo "<option value=''>add to group:</option>";
    while ($row = mysqli_fetch_array($mailing_lists)) {
        $idlist = $row['idlist'];
        $name = $row['name'];

        echo "<option value='$idlist'>" . $name . "</option>";
    }
    ?>
                        </select>
                        <input type="Submit" name="Submit2" value="Add">                               
                    </form>                            

                            <?php
                        }   //Draws a table with the currently selected group

                        if (isset($_POST['SubmitEdit'])) {
                            $ide = mysqli_real_escape_string($con, $_POST['id']);
                            $firstname = mysqli_real_escape_string($con, $_POST['firstname']);
                            $surname = mysqli_real_escape_string($con, $_POST['surname']);
                            $email = mysqli_real_escape_string($con, $_POST['email']);
                            $company = mysqli_real_escape_string($con, $_POST['company']);
                            $title = mysqli_real_escape_string($con, $_POST['title']);
                            $idlist = mysqli_real_escape_string($con, $_POST['list']);

                            if ($surname != "" && $email != "" && $idlist != "") {
                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    //email is correct
                                    $checkEmail = mysqli_query($con, "SELECT email FROM addresses WHERE email = '$email' AND list_idlist = '$idlist'")
                                            or die(mysqli_error($con));

                                    if (mysqli_num_rows($checkEmail) < 1) {
                                        //email does not exist in DB, we can update
                                        $update = mysqli_query($con, "UPDATE addresses SET firstname = '$firstname' "
                                                . ", surname = '$surname' "
                                                . ", email = '$email'"
                                                . ", company = '$company'"
                                                . ", job_title = '$title'"
                                                . ", email = '$email'"
                                                . ", list_idlist = '$idlist' WHERE idaddresses = '$ide'")
                                                or die(mysqli_error($con));

                                        echo $update ? "<i class='fa fa-info fa-fw'></i>" . $email . " changed sucessfully!<br>" : "Error ocurred. Try again.";
                                        echo "<meta http-equiv='refresh' content='1;URL=http://marte/mailing/v2/pages/contacts.php?list=$idlist&Submit=Select'>";
                                    } else {
                                        echo "<i class='fa fa-warning fa-fw'></i>";
                                        echo "This email already exists in this group.<br>";
                                        return;
                                    }
                                } else {
                                    echo "<i class='fa fa-warning fa-fw'></i>";
                                    echo "This email is not of a valid format. Check your spelling and try again" . "<br>";
                                    return;
                                }
                            } else {
                                echo "<i class='fa fa-warning fa-fw'></i>";
                                echo "Surname and e-mail are mandatory fields.<br>";
                                return;
                            }
                        }


                        if (isset($_GET['Submit']) && $_GET['list'] != 0) {
                            $idlist = $_GET['list'];
                            //$_SESSION['list'] = $list;
                            //display table
                            $email_list = mysqli_query($con, "SELECT * FROM addresses WHERE list_idlist = '$idlist' ORDER BY surname") or die(mysqli_error($con));
                            $count = utils::mysqli_result(mysqli_query($con, "SELECT COUNT(email) FROM addresses WHERE list_idlist = '$idlist'"), 0, 0);
                            $list_name = utils::mysqli_result(mysqli_query($con, "SELECT name FROM list WHERE idlist = '$idlist'"), 0, 0);

                            echo "<h2><b>" . $list_name . "</b></h2>" . " contains " . $count . " addresses<br><br>";
                            //echo "There are " . $count . " addresses in ". $list_name . "<br><br>"; 
                            echo "<table class='table table-striped table-bordered table-hover' id='dataTables-example'><thead>";
                            echo "<tr><th>Surname</th>" .
                            "<th>First Name(s)</th>" .
                            "<th>E-mail</th>" .
                            "<th>Company</th>" .
                            "<th>Job Title</th>" .
                            "<th></th></tr></thead>";

                            while ($list = mysqli_fetch_array($email_list)) {
                                $firstname = $list['firstname'];
                                $surname = $list['surname'];
                                $email = $list['email'];
                                $id = $list['idaddresses'];
                                $company = $list['company'];
                                $title = $list['job_title'];

                                echo "<tr><td>" . $surname . "</td>"
                                . "<td>" . $firstname . "</td>"
                                . "<td>" . $email . "</td>"
                                . "<td>" . $company . "</td>"
                                . "<td>" . $title . "</td>"
                                // . "<td><img src='../assets/edit_icon.gif'><img src='../assets/remove_icon.gif'></td></tr>";
                                . "<td><a href='contacts.php?e=$id'><img src='../assets/edit2.gif' title='Edit contact'></a>   <a href='contacts.php?rm=$id'><img src='../assets/delete2.gif' title='Remove contact'></a></td></tr>";
                                ?>

                        <?php
                    }

                    echo "</tr>";
                    echo "</table>";
                } else {
                    if (isset($_POST['Submit2'])) {
                        $firstname = mysqli_real_escape_string($con, $_POST['firstname']);
                        $surname = mysqli_real_escape_string($con, $_POST['surname']);
                        $email = mysqli_real_escape_string($con, $_POST['email']);
                        $idlist = mysqli_real_escape_string($con, $_POST['list']);
                        $company = mysqli_real_escape_string($con, $_POST['company']);
                        $title = mysqli_real_escape_string($con, $_POST['title']);

                        //validate data
                        if ($surname != "" && $email != "" && $idlist != "") {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                //email is correct
                                $checkEmail = mysqli_query($con, "SELECT email FROM addresses WHERE email = '$email' AND list_idlist = '$idlist'")
                                        or die(mysqli_error($con));

                                if (mysqli_num_rows($checkEmail) < 1) {
                                    //email does not exist in DB, we can insert
                                    $insert = mysqli_query($con, "INSERT INTO addresses (idaddresses, surname, firstname, email, company, job_title, list_idlist)
                        VALUES ('NULL','$surname','$firstname', '$email', '$company', '$title', '$idlist')") or die(mysqli_error($con));
                                    echo $insert ? "<i class='fa fa-info fa-fw'></i>" . $email . " added sucessfully!<br>" : "<i class='fa fa-warning fa-fw'></i>" . "Error ocurred. Try again <a href='add.php'>here</a>";
                                    echo "<meta http-equiv='refresh' content='1;URL=http://marte/mailing/v2/pages/contacts.php?list=$idlist&Submit=Select'>";
                                } else {
                                    echo "<i class='fa fa-warning fa-fw'></i>";
                                    echo "This email already exists in this group.<br>";
                                    return;
                                }
                            } else {
                                echo "<i class='fa fa-warning fa-fw'></i>";
                                echo "This email is not of a valid format. Check your spelling and try again" . "<br>";
                                return;
                            }
                        } else {
                            echo "<i class='fa fa-warning fa-fw'></i>";
                            echo "Surname and e-mail are mandatory fields.<br>";
                            return;
                        }
                    }
                }

                if (isset($_GET['rm'])) {
                    $idr = mysqli_real_escape_string($con, $_GET['rm']);
                    $id_redirect = utils::mysqli_result(mysqli_query($con, "SELECT list_idlist FROM addresses WHERE idaddresses = '$idr'"), 0, 0);
                    $getRemovedEmail = utils::mysqli_result(mysqli_query($con, "SELECT email FROM addresses WHERE idaddresses = '$idr'"), 0, 0);
                    $delete = mysqli_query($con, "DELETE FROM addresses WHERE idaddresses = '$idr'")
                            or die(mysqli_error($con));

                    echo ($delete ? "<i class='fa fa-info fa-fw'></i>" . $getRemovedEmail . " has been removed successfully" : "Error");
                    echo "<br>";


                    echo "<meta http-equiv='refresh' content='1;URL=http://marte/mailing/v2/pages/contacts.php?list=$id_redirect&Submit=Select'>";
                }

                if (isset($_GET['edit']) && $_GET['edit'] == 'true') {
                    $id_redirect = $_POST['list'];
                    echo "<meta http-equiv='refresh' content='1;URL=http://marte/mailing/v2/pages/contacts.php?list=$id_redirect&Submit=Select'>";
                }
                ?>
                <script src="../bower_components/jquery/dist/jquery.min.js"></script>

                <!-- Bootstrap Core JavaScript -->
                <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

                <!-- Metis Menu Plugin JavaScript -->
                <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

                <!-- DataTables JavaScript -->
                <script src="../bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
                <script src="../bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>

                <!-- Custom Theme JavaScript -->
                <script src="../dist/js/sb-admin-2.js"></script>

                <!-- Page-Level Demo Scripts - Tables - Use for reference -->
                <script>

                    $(document).ready(function () {
                        $('#dataTables-example').DataTable({
                            "lengthMenu": [[50, 100, -1], [50, 100, "All"]]
                        });
                    });
                </script>
            </div></div></div>
