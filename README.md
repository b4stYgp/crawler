# crawler
Crawler in PHP für das Modul Datenbank-Programmierschnittstellen
von Sebastian Eisele und Tobias Zillmann

Es muss lediglich eine Datenbank mit dem Namen mydb vorhanden sein mit folgender Konfiguration, die auch in create_tables.php nachgelesen werden kann.

$servername = 'localhost';
$dbname = 'mydb';
$dbusername = 'root';
$dbpasswort = '';

Beim Aufruf von localhost im Browser werden die Tabellen generiert, falls nicht schon vorhanden.

Aufbau:

index.php mit dem Frontend. Hier kann mit dem Parameter "Depth" eingestellt werden wie Tief gecrawled werden soll, da der Crawler und die Specherung der Daten in die Datenbank seine Zeit benötigt, wurde die Abbruchzeit des Scripts von standardmäßigen 120s auf 3600s erhöht. Beim klick auf "start" wird die submit.php in einem neuen Tab aufgerufen, so dass die Suchfunktion bereits genutzt werden kann.

create_tables.php die auf mydb.sql zugreift und die Tabellen generieren lässt

submit.php in dem der Code für den Crawler liegt.
