# crawler
Crawler in PHP für das Modul Datenbank-Programmierschnittstellen
von Sebastian Eisele und Tobias Zillmann

Es muss lediglich eine Datenbank mit dem Namen mydb vorhanden sein mit folgender Konfiguration, die auch in create_tables.php nachgelesen werden kann.

$servername = 'localhost';
$dbname = 'mydb';
$dbusername = 'root';
$dbpasswort = '';

Beim Aufruf von localhost im Browser werden die Tabellen generiert, falls nicht schon vorhanden.

Aufbau
index.php
create_tables.php die auf mydb.sql zugreift
submit.php in dem der Code für den Crawler liegt.
