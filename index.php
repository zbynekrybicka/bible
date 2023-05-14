<html>
    <body>
<?php
try {
  $db = new PDO('sqlite:bible.db');

  $stmt = $db->prepare('SELECT k.nazev, v.kapitola, v.vers, v.obsah 
                        FROM knihy k 
                        INNER JOIN verse v ON k.zkratka = v.kniha 
                        ORDER BY k.ROWID, v.kapitola, v.vers');
  $stmt->execute();

  $current_book = "";
  $current_chapter = 0;

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $book = $row['nazev'];
    $kapitola = $row['kapitola'];
    $vers = $row['vers'];
    $obsah = $row['obsah'];

    if ($current_book != $book) {
      if ($current_book !== "") {
        echo "</ol>";
      }
      echo "<h1>$book</h1>";
      $current_book = $book;
      $current_chapter = 0;
    }

    if ($current_chapter != $kapitola) {
      if ($current_chapter > 0) {
        echo "</ol>";
      }
      echo "<h2>$kapitola</h2><ol>";
      $current_chapter = $kapitola;
    }

    echo "<li>$obsah</li>";
  }

  $db = null;
} catch (PDOException $e) {
  echo $e->getMessage();
}
?>
</body>
</html>