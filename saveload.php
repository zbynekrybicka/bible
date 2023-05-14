<?php

function saveToDb($db, $zkratka, $kapitola, $vers, $content) {
  
    // příprava dotazu na vložení dat
    $query = "INSERT INTO verse (zkratka, kapitola, vers, content) VALUES ('$zkratka', $kapitola, $vers, '$content')";
  
    // vykonání dotazu
    $db->exec($query);
  
}

function getBibleVerse($db, $book, $kapitola, $vers) {
    $bibleUrl = "https://www.bible.com/cs/bible/509/$book.$kapitola.$vers.CSP";
    $maxAttempts = 5;
    $attempts = 0;
    $html = '';
    
    while ($attempts < $maxAttempts) {
        try {
            $html = file_get_contents($bibleUrl);
            if ($html !== false) {
                break;
            }
        } catch (\Exception $e) {}
        $attempts++;
        sleep(1);      
    }

    if ($attempts === $maxAttempts) {
      throw new \Exception("Přes veškeré snažení se to nepodařilo stáhnout.");
    } else {
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $linkElement = $xpath->query('//link')->item(0);
        if (!$linkElement) {
            sleep(60);
            return getBibleVerse($db, $book, $kapitola, $vers);
        } else {
            $link = $linkElement->getAttribute('href');
            if ($link === $bibleUrl) {
                $descriptionElement = $xpath->query("//meta[@name='description']")->item(0);
                if ($descriptionElement) {
                    return $descriptionElement->getAttribute('content');
                } else {
                    sleep(60);
                    return getBibleVerse($db, $book, $kapitola, $vers);
                }
            } else {
                return false;
            }
        }
    }
}