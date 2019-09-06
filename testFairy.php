<?php
$file = $argv[1];
$csvInputArray = readCSV($file);

// Sorting array by last name
usort($csvInputArray, function ($a, $b) {
    return $a['last name'] <=> $b['last name'];
});

createHtmlFile('index.html',createIndexHtml($csvInputArray));

function readCSV($file){
    $csvInputArray = $fields = array();
    $i = 0;
    $handle = @fopen($file, "r");
    if ($handle) {
        while (($row = fgetcsv($handle, 4096, ",",'"')) !== false) {
            if (empty($fields)) {
                $fields = $row;
                continue;
            }
            foreach ($row as $k=>$value) {
                $csvInputArray[$i][$fields[$k]] = $value;
            }
            $i++;
        }
        if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
        }
        fclose($handle);
    }
    return $csvInputArray;
}

function createIndexHtml($csvInputArray)
{
    $cityArray = array();
    // start table
    $htmlCode = '<table>';
    // header row
    $htmlCode .= '<tr>';
    foreach ($csvInputArray[0] as $key => $value) {
        $htmlCode .= '<th>' . htmlspecialchars($key) . '</th>';
    }
    $htmlCode .= '</tr>';

    // data rows
    foreach ($csvInputArray as $contact) {
        $htmlCode .= '<tr>';
        createContactHtml($contact);
        // Counting city population
        if (array_key_exists($contact['city'], $cityArray))
        {
            $cityArray[$contact['city']]++;
        }
        else{
            $cityArray[$contact['city']] = 1;
        }
        foreach ($contact as $key2 => $value2) {
            if ($key2 == "first name") {
                $htmlCode .= '<td> <a href="' . createContactHtmlName($contact) . '">' . htmlspecialchars($value2) . '</a></td>';
            } else {
                $htmlCode .= '<td>' . htmlspecialchars($value2) . '</td>';
            }
        }
        $htmlCode .= '</tr>';
    }
    $htmlCode .= '</table>';
    $htmlCode .= '<br><b>'."City population summary: ".'</b><br>';
    foreach ($cityArray as $city => $count)
    {
        $htmlCode .= ucfirst($city).": ".$count.'<br>';
    }

    return $htmlCode;
}

function createHtmlFile($htmlFileName,$htmlCode)
{
    $fh = fopen($htmlFileName, 'w');
    fwrite($fh, $htmlCode);
    fclose($fh);
}

function createContactHtml($contact)
{
    $htmlCode = "";
    foreach($contact as $key=>$value){
            $htmlCode .= htmlspecialchars(ucfirst($key).": ".$value) . '<br>';
    }

    createHtmlFile(createContactHtmlName($contact),$htmlCode);

}

function createContactHtmlName($contact)
{
    $fileName = $contact['first name']."_".$contact['last name'].".html";
    return $fileName;
}

?>