<?php
class Crawler {
	protected $markup = '';
	public $base = '';

	public function __construct($uri) {
		$this->base = $uri;
		$this->markup = $this->getMarkup($uri);
	}
	public function getMarkup($uri) {
		return file_get_contents($uri);
	}
	public function get($type) {
		$method = "_get_{$type}";
		if (method_exists($this, $method)){
			return call_user_func(array($this, $method));
		}
	}
	protected function _get_images() {
		if (!empty($this->markup)){
			preg_match_all('/<img([^>]+)\/>/i', $this->markup, $images);
			return !empty($images[1]) ? $images[1] : FALSE;
		}
	}

#Alexander Simianer
#DHBW Heidenheim / Informatik / DBPS / Übungen
	protected function _get_links() {
		if (!empty($this->markup)){
			//preg_match_all('/<a([^>]+)\>(.*?)\<\/a\>/i', $this->markup, $links);
			preg_match_all('/href=\"(.*?)\"/i', $this->markup, $links);
			return !empty($links[1]) ? $links[1] : FALSE;
		}
	}
}

function crawl ($URL){
	# von Sebastian Eisele und Tobias Zillmann
	$servername = 'localhost';
	$dbname = 'mydb';
	$dbusername = 'root';
	$dbpasswort = '';
	$dbserverdaten = "mysql:host=$servername;dbname=$dbname";
	echo $URL;
	echo "<br><br>";

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
	#
	
	#Speichern des Links falls noch nicht vorhanden ansonsten update des timestamps
	$sql = "SELECT * FROM `links` WHERE `link`='$URL'";	
	
	if(mysqli_num_rows(mysqli_query($verbindung, $sql))){
		$sql="UPDATE `links` SET `timestamp`= NOW() WHERE `link`='$URL'";
	}
	else{
		$sql="INSERT INTO `links` (`link`,`timestamp`) VALUES ('$URL',NOW())";
	}	
	
	$sqlErgebnis = mysqli_query($verbindung, $sql);
	
	$crawl = new Crawler($URL);
	$images = $crawl->get('images');
	$links = $crawl->get('links');
	$expDomain = "/https?(:\/\/)(www\.)*([a-z]|-|\.)+/";
	$expSubDomain = "/https?(:\/\/)(www\.)*(([a-z]|-|\.)+\/)(([a-z]|-)+\/)+/";
	
	foreach($links as $link){		
		if(preg_match($expDomain, $link)){
			array_push($links, $link);
			$sql = "SELECT * FROM `links` WHERE `link` = '$link'";
			$sqlErgebnis = mysqli_query($verbindung, $sql);
			$reihen = mysqli_num_rows($sqlErgebnis);
			echo "reihen1: $reihen";
			if(!$reihen){
				$sql = "INSERT INTO `links` (`link`) VALUES ('$link')";
				$sqlErgebnis = mysqli_query($verbindung, $sql);
			}
			echo "<br>Link: $link";
		}
		if (preg_match($expDomain, $link)){
			array_push($links, $link);
			$sql = "SELECT * FROM `links` WHERE `link` = '$link'";
			$sqlErgebnis = mysqli_query($verbindung, $sql);
			$reihen = mysqli_num_rows($sqlErgebnis);
			echo "reihen2: $reihen";
			if(!$reihen){
				$sql = "INSERT INTO `links` (`link`) VALUES ('$link')";
			}
			echo "<br>Link: $link";
		}
		$sql = "SELECT * FROM `links` WHERE `link` = '$link' AND `timestamp` <  NOW() - INTERVAL '1 DAY'";
		echo $sql;
		$sqlErgebnis = mysqli_query($verbindung, $sql);
		if(mysqli_num_rows($sqlErgebnis)){
			crawl($link);
		}
		mysqli_free_result($sqlErgebnis);
	}		
	mysqli_close($verbindung);

}
#$crawl = new Crawler('http://www.dhbw-heidenheim.de');
#	$images = $crawl->get('images');
#	$links = $crawl->get('links');

?>
<html>
	<body>
		<h2>Webcrawler</h2>
		<form action="/submit.php" method="post">
			<label>DHBW add URL to Database</label>
			<input type="text" name="url"><br><br>			
		</form>
		<?php
			crawl('http://www.dhbw-heidenheim.de');
		?>
	</body>
</html>