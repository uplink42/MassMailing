<?php

require_once('../class/link.php');
require_once('../class/utils.php');
session_start();

$link = new link();
$con = $link->connect();
$con->set_charset("utf8");

$getMailings = mysqli_query($con, "SELECT title, datetime, count(email) AS count FROM v_log_details GROUP BY title") or die(mysqli_error($con));

echo "<table border = '1'>";
echo "<tr><th>Newsletter title</th><th>Time sent</th><th>Recipients</th></tr>";

while ($mailing = mysqli_fetch_array($getMailings)) {
    $title = $mailing['title'];
    $count = $mailing['count'];
    $datetime = $mailing['datetime'];
    echo "<tr><td>" . $title . "</td><td>" . $datetime . "</td><td>" . $count . "</td></tr>";
}
echo "</table>";

echo "<br>" . "Emails sent: ";


$getLog = mysqli_query($con, "SELECT * FROM v_log_details ORDER BY datetime DESC") or die(mysqli_error($con));

echo "<table class = 'table-striped table-bordered table-hover' id='dataTables-example'>";
echo "<tr><th>Newsletter title</th><th>Date&time</th><th>Email</th><th>Send result</th></tr>";

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