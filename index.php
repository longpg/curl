<?php
$url = 'https://play.google.com/store/apps/details?id=com.glu.flc2';
if (strpos($url, '&hl=') === false && strpos($url, '?hl=') === false) {
    $url .= '&hl=en';
}
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$data = curl_exec($curl);
curl_close($curl);
$dom = new DOMDocument();
@$dom->loadHTML('<?xml encoding="UTF-8">' . $data);
$xpath = new DOMXPath($dom);
$game = array();
/**
 * Retrieve game name:
 */
$results = $xpath->query("//*[@class='document-title']");
if ($results->length > 0) {
    $review = $results->item(0)->nodeValue;
    $game['name'] = trim($review);
}
/**
 * Retrieve game version:
 */
$results = $xpath->query("//*[@itemprop='softwareVersion']");
if ($results->length > 0) {
    $review = $results->item(0)->nodeValue;
    $game['version'] = trim($review);
}
/**
 * Retrieve game manufacturer:
 */
$results = $xpath->query("//*[@class='document-subtitle primary']");
if ($results->length > 0) {
    $review = $results->item(0)->nodeValue;
    $game['manufacturer'] = trim($review);
}
/**
 * Retrieve game images:
 */
$results = $xpath->query("//*[@itemprop='screenshot']");
if ($results->length > 0) {
    $review = array();
    for ($i = 0; $i < min(array($results->length, 4)); $i++) {
        $review[] = trim($results->item($i)->getAttribute('src'));
    }
    $game['images'] = implode(',', $review);
}
/**
 * Retrieve game description:
 */
$results = $xpath->query("//*[@class='id-app-orig-desc']");
$review = '';
if ($results->length > 0) {
    $review = $results->item(0);
}
$results = $xpath->query("//*[@class='id-app-translated-desc']");
if ($results->length > 0) {
    $review = $results->item(0);
}
if (!empty($review)) {
    $html = '';
    foreach ($review->childNodes as $childNode) {
        $html .= $dom->saveHTML($childNode);
    }
    $game['description'] = $html;
}

foreach($game as $key => $value) {
    echo "<h2>{$key}</h2>";
    if ($key == 'images') {
        echo "<div>";
        foreach (explode(',', $value) as $src) {
            echo "<img src='{$src}' />";
        }
        echo "</div><hr />";
    } else {
        echo "<div>{$value}</div><hr />";
    }
}