<?

$smile=opendir(".");
while ($file = readdir($smile))
{
	if (ereg(".*\.gif",$file))
	{
		echo "<IMG SRC=$file>&nbsp;";
	}
}
closedir($smile); 

?>
