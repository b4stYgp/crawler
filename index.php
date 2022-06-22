<html>
	<body>
		<h2>Webcrawler</h2>
		<form target="_blank" action="/submit.php" method="post" id="crawlerForm">
			<label>DHBW add URL to Database</label>
			<input type="text" name="url"><br><br>
			<p>KEIN https:// -- nur www.xyz.de</p>
			<label>Anzahl iterationen</label>
			<input type="number" name="iterationen">			
		</form>
		<button type="submit" form="crawlerForm" value="Submit">Daten Absenden</button>
	
	<br><br><br>
	<hr>
	<?php
		if(array_key_exists('searchButton', $_POST)) {
			searchbutton();
		}
		function searchButton() {
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
			##
			mysqli_real_escape_string($verbindung, $_POST["suchBegriff"]);
			$suchBergiff = strtolower($_POST["suchBegriff"]);			
			$sql = 'SELECT `id` from `words` WHERE `word` like %$suchBegriff%';
			$sqlErgebnis = mysqli_query($verbindung, $sql);
			if(mysqli_num_rows($sqlErgebnis) > 0 )
			{
				while($reihen = mysqli_fetch_assoc($sqlErgebnis))
				{
					echo '<ul>$reihen["id"]</ul>';					
				}
			}
		}
	?>
	<br><br>
		<h2>DHBW search</h2>
		<form method="post" id="searchForm"><br><br>
			<input type="text" name="suchBegriff" value="Search Term"><br><br>
		</form>
		<button type="submit" name="searchButton" form="searchForm">Daten absenden</button>
	</body>	
	<br><br>
	<hr>
	<br><br>
	
</html>