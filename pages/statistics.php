<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="30" />
    <title>Statistics</title>
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
            <h3><a href='newsletter.php'>Create Mailing</a> | <a href='contacts.php'>Contacts Management</a> | Statistics</h3>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <div class="dataTable_wrapper">


                <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Statistics</title>

                        <?php
                        require_once('../class/link.php');
                        require_once('../class/utils.php');
                        session_start();

                        $link = new link();
                        $con = $link->connect();
                        $con->set_charset("utf8");

                        $getMailings = mysqli_query($con, "SELECT title, datetime, count(email) AS count FROM v_log_details GROUP BY idnewsletter ORDER BY datetime DESC ") or die(mysqli_error($con));

                        echo "<h2><b>Mailings sent: </b></h2>";

                        echo "<table class = 'table-striped table-bordered table-hover' id='dataTables-example'>";
                        echo "<thead><tr><th>Time sent</th><th>Mailing Title</th><th>Recipients</th></tr></thead>";

                        while ($mailing = mysqli_fetch_array($getMailings)) {
                            $title = $mailing['title'];
                            $count = $mailing['count'];
                            $datetime = $mailing['datetime'];
                            echo "<tr><td>" . $datetime . "</td><td>" . $title . "</td><td>" . $count . "</td></tr>";
                        }
                        echo "</table>";

                        echo "<h2><br>" . "<b>Emails sent: </b></h2>";


                        $getLog = mysqli_query($con, "SELECT * FROM v_log_details ORDER BY datetime DESC") or die(mysqli_error($con));

                        echo "<table class = 'table-striped table-bordered table-hover' id='dataTables-example2'>";
                        echo "<thead><tr><th>Mailing title</th><th>Date&time</th><th>Email</th><th>Send result</th></tr></thead>";

                        while ($entries = mysqli_fetch_array($getLog)) {
                            $title = $entries['title'];
                            $datetime = $entries['time_sent'];
                            $firstname = $entries['firstname'];
                            $surname = $entries['surname'];
                            $email = $entries['email'];
                            $group = $entries['group'];

                            $entries['send_result'] == '1' ? $result = "Sent" : $result = "Failed";
                            $result == "Sent" ? $color = "green" : $color = "red";

                            echo "<tr><td>" . $title . "</td><td>" . $datetime . "</td><td>" . $email . "</td><td><font color=$color>" . $result . "</td></tr>";
                        }
                        echo "</table>";
                        ?>

            </div></div></div>
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

    <script>

        $(document).ready(function () {
            $('#dataTables-example').DataTable({
                responsive: true,
                "order": [[0, "desc"]],
                "lengthMenu": [[25, 50, -1], [25, 50, "All"]]
            });
        });


    </script>

    <script>

        $(document).ready(function () {
            $('#dataTables-example2').DataTable({
                responsive: true,
                "order": [[1, "desc"]],
                "lengthMenu": [[50, 100, -1], [50, 100, "All"]]
            });
        });


    </script>