#MassMailer
This is a simple script that allows one to easily store and manage contacts into groups and send mailing lists to one or several groups at once. Greeting and signature customization are also possible. Customization is simple and e-mails sent trough this tool respect most spam filter's rules, resulting in a low rate of delivery failures.

#Requirements:
PHP 5.6+

MySQL 5.5 or above

Windows or Linux server running Apache 2.2 or above

Access to a working e-mail server (e.g exchange or gmail)

#Dependencies:
Bootstrap 3.3.6

jQuery 2.0+ (earlier versions may work but I never tested)

PHPMailer library 5.0+ (earlier versions may work but I never tested)

#How to use
First you're going to have to create a database in your local MySQL server. Import the mailing2.sql file and you're good to go. Don't forget to change the /class/link.php file with your MySQL username and password:

       $hostname = "localhost";
       $username = "";
       $password = "";
       $database = "mailing2";

1. Configure your e-mail server (address and port) in /pages/upload.php
    
    $mail->Host = '$yourservername';
    $mail->Port = '$yourserverport';
    
2. Create user accounts manually in the database table 'inbox' (name and password are your mail server credentials for each user)

3. Create contact groups you're going to use in the database (table 'list')

At this point you should be done manually altering the database

4. Create some contacts  (/pages/contacts.php)

5. Go to the newsletter page (/pages/newsletter.php) and fill in the required info:

*Subject: your e-mail subject

*Text: upload the text file to send. To ensure HTML formatting is strictly enforced you should save your documents in .HTM format. This is easily done by any popular text editor (such as Microsoft Word). For MS Word I advise you to save your files as "webpage, filtered" to get rid of Office's specific tags which can trigger some spam filters.

*Attachment (optional): send an image in .gif, .jpg, .jpeg and .png format (maximum 300 Kb but this can be manually changed in pages/newsletter.php): name="MAX_FILE_SIZE" value="310000" (size in bytes)
 
*Type in an optional greeting (such as Dear <first name>, or simply Dear collegue <none>, etc). This uses whatever first names you've set in the contacts page.
 
*Pick which inbox to send the e-mails from.
 
*Pick "From" name, meaning the name you see as the e-mail sender. If left blank, defaults to the "Name" field you've set in the database.
 
*Hit send! The script continues executing even if you close the page. It will send a confirmation message to the sender after it's done. You can keep track of progress and previous mailings in the Statistics page.
You can control the speed of which e-mails are sent by adding a simple sleep() command in pages/upload.php.


    
    
