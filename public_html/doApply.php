<?php
require __DIR__ . '/includes/header.php';


error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);


if(isValid())
{
        $sql = "INSERT INTO Applications
                        (DiscordID, DiscordName, RSIName, Timezone, Division, Biography, IPAddress, Timestamp, Completed)
                VALUES
                        (:DiscordID, :DiscordName, :RSIName, :Timezone, :Division, :Biography, :IPAddress, NOW(), 'No')";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':DiscordID', trim($_SESSION['Application']['DiscordID']));
        $stmt->bindParam(':DiscordName', trim($_SESSION['Application']['DiscordUsername']));
        $stmt->bindParam(':RSIName', trim($_POST['RSIName']));
        $stmt->bindParam(':Timezone', trim($_POST['Timezone']));
        $stmt->bindParam(':Division', trim($_POST['Division']));
        $stmt->bindParam(':Biography', trim($_POST['Bio']));
        $stmt->bindParam(':IPAddress', $_SERVER['REMOTE_ADDR']);

        $stmt->execute();

	postApplication($_POST);
	$_SESSION['Application']['Step'] = 5;
	redirect('Apply?result=success');
	exit;
}
else
{
	redirect("Apply?error=There was an issue with the Captcha.  Please submit your application again!");
	exit;
}


function isValid()
{
    try {

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = ['secret'   => '6Ld_OuQSAAAAAD7mo7mDROQQMzBbhkl8YFQ0Z1av',
                 'response' => $_POST['g-recaptcha-response'],
                 'remoteip' => $_SERVER['REMOTE_ADDR']];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return json_decode($result)->success;
    }
    catch (Exception $e) {
        return null;
    }
}

