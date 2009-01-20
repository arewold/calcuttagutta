<?php
	require_once("common.php");

	error_reporting(E_ALL);

	if(isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
       	$sql = "SELECT data, filename, size, filetype FROM files WHERE picid=" . $_REQUEST['id'];
		$result = DB_get_table($sql);

		// ikke bruk rows_affected, ingen output da, fucked header?
		// dette virker forsaavidt heller ikke.
		if (!$result) {
			echo 'Ikke noe resultat';
		} else {
			$data = mysql_result($result, 0, "data");
			$name = mysql_result($result, 0, "filename");
			$size = mysql_result($result, 0, "size");
			$type = mysql_result($result, 0, "filetype");

			header("Content-type: $type");
			header("Content-length: $size");
			header("Content-Disposition: attachment; filename=$name");
			header("Content-Description: PHP Generated Data");
			echo $data;
		}
	} else {
		echo 'Manglende eller ugyldig id';
	}
?>
