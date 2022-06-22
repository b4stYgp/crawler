<?php
$servername = 'localhost';
$dbname = 'mydb';
$dbusername = 'root';
$dbpasswort = '';
$dbserverdaten = "mysql:host=$servername;dbname=$dbname";

#Datenbankverbindung herstellen
$verbindung = mysqli_connect($servername, $dbusername, $dbpasswort);

if(!$verbindung)
{
	echo "keine Verbindung";
	exit;
}

$datenbank = mysqli_select_db($verbindung, $dbname);

if(!$datenbank)
{
	echo "keine Verbindung zur Datenbank";
	exit;
}
if ($result = mysqli_query($verbindung, "SHOW TABLES LIKE 'links'")) {
    if($result->num_rows == 1) {
		
    } else {
		$sqlScript = file_get_contents("mydb.sql");
		$commands = explode(";",$sqlScript);
		
		foreach($commands as $command){	
			$sqlErgebnis = mysqli_query($verbindung, "$command;");
		}
		mysqli_close($verbindung);
	}
}
?>
