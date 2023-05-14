<!DOCTYPE html>
<html>
<head>
	<title>Bible formulář</title>
	<meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/css/bootstrap.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container">
	<h1>Vložit nový biblický text</h1>
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <form action="verse.php" method="POST" id="form">
                <div class="form-group">
                    <label for="title">Nadpis:</label><br>
                    <input type="text" id="title" class="form-control" name="title" value="<?=$_POST['title'] ?? ''; ?>"><br>
                </div>

                <div class="form-group">
                    <label for="content">Obsah:</label><br>
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <textarea id="content" name="content1"  class="form-control" rows="15" id="content1"><?=$_POST['content1'] ?? ''; ?></textarea><br>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <textarea id="content" name="content2"  class="form-control" rows="15" id="content2"><?=$_POST['content2'] ?? ''; ?></textarea><br>
                        </div>
                    </div>
                </div>

                <input class="btn btn-light" type="submit" name="Akce" value="Zobrazit">

                <input class="btn btn-primary" type="submit" name="Akce" value="Uložit">
            </form>
        </div>
        <div class="col-sm-6 col-xs-12">
	<?php
    require "saveload.php";
	// Připojení k databázi
	$db = new PDO('sqlite:bible.db');

    $stmt = $db->prepare('SELECT nazev FROM knihy');
    $stmt->execute();
    $knihy = $stmt->fetchAll(PDO::FETCH_COLUMN);

	// Zpracování předaného formuláře
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$title = $_POST['title'];
        $content = $_POST['content1'] . "\n" . $_POST['content2'];
        if (strlen($content) === 0) {
            throw new Exception("Nelze uložit prázdnou kapitolu!");
        }
		$content = preg_replace("/\n\-/", "<li>", $content);
        $content = preg_replace("/\t\-/", "&nbsp;&nbsp;<li>", $content);
        $content = preg_replace("/\n/", "<br />", $content);
        $content = preg_replace("/^\-/", "<li>", $content);

		// Regex pro nalezení odkazů na verše
		$pattern = '/(' . implode("|", $knihy) . ')\s(\d+)(:(\d+-?\d*))?/';
        preg_match_all($pattern, $content, $result);

        // var_dump($result);

		// Nahrazení odkazů na verše jejich obsahem z databáze
		$content = preg_replace_callback($pattern, function($match) use ($db) {
            @list(, $kniha, $kapitola, , $verse) = $match;
            if ($verse) {
			    $range = explode('-', $verse);
            }

			// Dotaz na obsah verše/veršů v databázi
			$query = 'SELECT vers || obsah FROM verse JOIN knihy on knihy.zkratka = verse.kniha WHERE knihy.nazev = ? AND verse.kapitola = ?';
            if ($verse) {
                if (count($range) == 2) {
                    $query .= ' AND verse.vers BETWEEN ? AND ?';
                } else {
                    $query .= ' AND verse.vers = ?';
                }
                $stmt = $db->prepare($query);

                // Parametry dotazu
                $stmt->bindValue(1, $kniha);
                $stmt->bindValue(2, $kapitola);

                if (count($range) == 2) {
                    $stmt->bindValue(3, $range[0]);
                    $stmt->bindValue(4, $range[1]);
                } else {
                    $stmt->bindValue(3, $range[0]);
                }
            } else {
                $stmt = $db->prepare($query);

                // Parametry dotazu
                $stmt->bindValue(1, $kniha);
                $stmt->bindValue(2, $kapitola);
            }


			// Spuštění dotazu a získání obsahu verše/veršů
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_COLUMN);


            if (!$result) {
                echo $match[0] . " se nepodařilo najít";
                $stmt = $db->prepare('SELECT zkratka FROM knihy WHERE nazev = ?');
                $stmt->bindValue(1, $kniha);
                $stmt->execute();
                $zkratka = $stmt->fetchAll(PDO::FETCH_COLUMN);
                if (!$zkratka) {
                    throw new Exception("Kniha $kniha neexistuje.");
                }
                if (count($range) == 2) {
                    $result = "";
                    for ($i = $range[0]; $i <= $range[1]; $i++) {
                        $verse = getBibleVerse($db, implode($zkratka), $kapitola, $range[0]);
                        saveToDB($db, implode($zkratka), $kapitola, $range[0], $verse);    
                        $result .= " " . $verse;
                    }
                } else {
                    $result = getBibleVerse($db, implode($zkratka), $kapitola, $range[0]);
                    saveToDB($db, implode($zkratka), $kapitola, $range[0], $result);    
                }
            } else {
                $result = implode(' ', $result);
            }

			// Náhrada odkazu na verš/verše jejich obsahem
			return $match[0] . " &ndash; <tt>" . $result . "</tt>";
		}, $content);


        if (($_POST['Akce'] ?? '') === "Uložit") {
            $stmt = $db->prepare("INSERT INTO lekce (nazev, obsah) values (:nazev, :obsah)");
            $stmt->bindValue("nazev", $title);
            $stmt->bindValue("obsah", $content);
            $stmt->execute();
            header('Location: verse.php');
        } else {
		    echo '<h2>' . $title . '</h2>';
		    echo '<p>' . $content . '</p>';
        }
	}
?></div>
</div>
    </div>
    <script>
        window.onload = () => {
            document.forms[0].<?=strlen($_POST['content2'] ?? '') > 0 ? 'content2' : 'content1'; ?>.focus();
        }
  // přidáme posluchač události stisku klávesy
  document.addEventListener("keydown", function(event) {
    if (event.key === "F2") {
      // zde můžete vložit kód, který se má provést po stisku klávesy F2
      document.getElementById('form').submit();
    }
  });
</script>
</body>
</html>
