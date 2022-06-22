<html>
	<body>
		<h2>Webcrawler</h2>
		<form target="_blank" action="/submit.php" method="post" id="crawlerForm">
			<label>DHBW add URL to Database</label>
			<input type="text" name="url"><br><br>
			<p>KEIN https:// -- nur www.xyz.de</p>
			<label for="iterations"> Depth:</label>
			<select name="iterations" id="iterations">
			  <option value="0">0</option>
			  <option value="1">1</option>
			  <option value="2">2</option>
			  <option value="3">3</option>
			  <option value="4">4</option>
			  <option value="5">5</option>
			</select>				
		</form>
		<button type="submit" form="crawlerForm" value="Submit">Daten Absenden</button>
	
	<br><br><br>
	<hr>	
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
			$suchBegriff = strtolower($_POST["suchBegriff"]);			
			$sql = "SELECT `id` from `words` WHERE `words` like '%$suchBegriff%'";#Get Suchbegriff Id
			$sqlErgebnis = mysqli_query($verbindung, $sql);
			if(mysqli_num_rows($sqlErgebnis) > 0 )
			{
				echo "<ul>";
				while($reihen = mysqli_fetch_assoc($sqlErgebnis))
				{	
					$suchBegriffId = $reihen["id"];
					echo "<li>$suchBegriff has id: $suchBegriffId</li>";
					$sql = "SELECT `links`.`id`, `links`.`link` FROM `words_links`
							LEFT JOIN `links` 
							ON `words_links`.`id_links` = `links`.`id` 
							WHERE `id_words` = '$suchBegriffId'";
					$sqlErgebnis = mysqli_query($verbindung, $sql);
					echo "<ul>";
					while($reihen = mysqli_fetch_assoc($sqlErgebnis))
					{
						$linkId = $reihen["id"];
						$link = $reihen["link"];
						echo "<li>wordlink found. The id_link is $linkId <br> Link: $link</li>";
					}
					echo "</ul>";
				}
				mysqli_free_result($sqlErgebnis);				
				echo "</ul>";
			}
			
		}
	?>
	
</html>