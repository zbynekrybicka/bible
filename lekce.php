<!DOCTYPE html>
<html>
<head>
	<title>Lekce Nové nebe nová Země</title>
	<meta charset="utf-8">
<style>
    body {
        font-weight: bold;
        font-family: verdana;
        font-size: 10pt;
    }
    tt {
        font-weight: normal;
    }
</style>
</head>
<body>
<?php
	$db = new PDO('sqlite:bible.db');

    $stmt = $db->prepare('SELECT nazev, obsah FROM lekce');
    $stmt->execute();
    $lekceList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($lekceList as $lekce) { 
        echo "<h2>{$lekce['nazev']}</h2>";
        echo $lekce['obsah'];
    }
?>

</body>
</html>