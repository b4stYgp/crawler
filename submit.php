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
			$links = !empty($links[1]) ? $links[1] : FALSE;
			$new_links = array();

			foreach ($links as $link) {
				if(str_starts_with($link, "https://") || str_starts_with($link, "http://")){
					array_push($new_links, $link);
				}
			}
			return $new_links;
		}
	}
}

function set_title_words($url){
		# https://stackoverflow.com/a/4349078/14522363
		$str = file_get_contents($url);
		if(strlen($str)>0){
			$str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
			preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
			$title_complete = $title[1];
			$title_complete = preg_replace('/[0-9\@\.\;\"|-~+)(]+/', '', $title_complete); # removes special characters
			$title_complete = preg_replace('/[\s]+/mu', ' ', $title_complete); # removes multiple whitespaces
			$title_words = explode(" ",$title_complete);
			
			
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
			
			# get the id of the link
			$sql = "SELECT * FROM `links` WHERE `link` = '$url'";
			echo $sql;
			$sqlErgebnis = mysqli_query($verbindung, $sql);
			$row = $sqlErgebnis->fetch_row();
			$link_id = $row[0];
			echo "</br>LINK_ID: $link_id";
			
			foreach($title_words as $word){	
				# check if word already in list
				$sql = "SELECT * FROM `words` WHERE words = '$word'";
				$sqlErgebnis = mysqli_query($verbindung, $sql);
				$reihen = mysqli_num_rows($sqlErgebnis);
				if($reihen <= 0){
					$sql = "INSERT INTO `words`(`words`) VALUES ('$word')";
					echo "</br>$sql";
					$sqlErgebnis = mysqli_query($verbindung, $sql);
				}
				
				# get the id of the current word
				$sql = "SELECT * FROM `words` WHERE words = '$word'";
				echo $sql;
				$sqlErgebnis = mysqli_query($verbindung, $sql);
				$row = $sqlErgebnis->fetch_row();
				$word_id = $row[0];
				
				# insert new linking (between link and word)
				$sql = "INSERT INTO `words_links`(`id_words`, `id_links`) VALUES ($word_id, $link_id)";
				echo "</br>$sql";
				$sqlErgebnis = mysqli_query($verbindung, $sql);
			}
		}
	}


function crawl ($URL,$iteration){
	# von Sebastian Eisele und Tobias Zillmann
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
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	#Speichern des Links falls noch nicht vorhanden ansonsten update des timestamps
	$sql = "SELECT * FROM `links` WHERE `link`='$URL'";	
	
	if(mysqli_num_rows(mysqli_query($verbindung, $sql))){
		$sql="UPDATE `links` SET `timestamp`= NOW() WHERE `link`='$URL'";
	}
	else{
		$sql="INSERT INTO `links` (`link`,`timestamp`) VALUES ('$URL',NOW())";
	}
	$sqlErgebnis = mysqli_query($verbindung, $sql);
	set_title_words($URL);
	
	$crawl = new Crawler($URL);
	$images = $crawl->get('images');
	$links = $crawl->get('links');
	if($links != NULL)
	{
		foreach($links as $link){	
			array_push($links, $link);
			$link = str_replace("'","%27",$link); #manche Links scheinen ' zu besitzen...
			$sql = "SELECT * FROM `links` WHERE `link` = '$link'";
			$sqlErgebnis = mysqli_query($verbindung, $sql);
			$reihen = mysqli_num_rows($sqlErgebnis);
			if(!$reihen){
				$sql = "INSERT INTO `links` (`link`) VALUES ('$link')";
				$sqlErgebnis = mysqli_query($verbindung, $sql);
			}
			echo "<br>Link: $link";
			$sql = "SELECT * FROM `links` WHERE `link` = '$link' AND (`timestamp` <  (NOW() - 86400) OR `timestamp` is NULL)";
			$sqlErgebnis = mysqli_query($verbindung, $sql);
			if(mysqli_num_rows($sqlErgebnis)){
				if($iteration < 0)
				{
					crawl($link,$iteration+1);
				}
				else {
					set_title_words($link);
				}
			}
			mysqli_free_result($sqlErgebnis);
		}				
	}		
	mysqli_close($verbindung);
}
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

if(isset($_POST))
{
	mysqli_real_escape_string($verbindung, $_POST["url"]);
	mysqli_close($verbindung);
	$link = "https://";
	$link .= $_POST["url"];	
	crawl($link,0);
}
else{
	echo "error";
}

?>
