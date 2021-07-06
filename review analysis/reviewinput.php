<?php

// Description: Sentiment Analyzer API usage example.
// Copyright: (C) 2016 EffectiveSoft Ltd. All Rights Reserved.
// Technical support: technical-support@effective-soft.com

header('Content-Type: text/plain');

# list of reviews in JSON format
$reviews = array(
    array(
        "id" => "snt1",
        "text" => "Very good comfort and very kind service in this modern hotel, very close to Chennai Airport."
    ),
    array(
        "id" => "snt2",
        "text" => "The service and restaurants are excellent, especially like the Samudra restaurant providing lots of tempting alternatives."
    ),
    array(
        "id" => "snt3",
        "text" => "Often travel to Chennai on business and this was the first time I tried the Trident, Chennai. I'll be staying here on all future trips. Excelllent service and the food was terrific."
    ),
    array(
        "id" => "snt4",
        "text" => "The ambiance of the restaurants is very poor, Cinnamon is more laid back, Samudra is more exclusive. Food is excellent and of course with Trident totally safe. Staff are very warm and welcoming. The rooms are not brand new."
    )
);

$json_reviews = json_encode($reviews);

# ----------- FUNCTIONS -----------
# function for printing sentences
function printSentences($response) {
    foreach($response['sentences'] as $resp_sent) {
        echo "\n" . 'Sentence Weight = ' . $resp_sent['w'] . "\t" . $resp_sent['text'];
    }
}

# function for printing categorized opinions
function printTree($node, $height)
{
    for ($i = 0; $i < $height; $i++)
    {
        echo "\t";
    }
    echo $node['t'];

    if ($node['w'] != 0)
    {
        echo "\t" . $node['w'] . "\n";
    }
    else
    {
        echo "\n";
    }
    $children = $node['children'];
    $height++;
    foreach ($children as $child)
    {
        printTree($child, $height);
    }
}

# ----------- END FUNCTIONS -----------


# ----------- cURL -----------
# set the URL for POST request, specify url, parameters for information processing, ontology for opinion categorization and API key for authorization purposes (change YourAPIKey to the Intellexer API key)
$link = "http://api.intellexer.com/analyzeSentiments?apikey=75145b1e-d9d7-4688-949f-b66d7f0fcbb9&ontology=hotels&loadSentences=true";
$header = array('Content-type: application/json');

# curl connection initialization
$ch = curl_init();

#set cURL options
curl_setopt_array($ch, array(
    CURLOPT_URL => $link,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HEADER => false,
    CURLOPT_HTTPHEADER => $header,
    CURLOPT_POSTFIELDS => $json_reviews,
    CURLOPT_FOLLOWLOCATION => true
));

# perform the request
$results = curl_exec($ch);

# error checking
if (curl_errno($ch))
{
    echo 'CURL error: ' . curl_error($ch);
}
else
{
    # parse JSON results
    $json_results = json_decode($results, true);

    # print "Sentences with sentiment objects and phrases"
    echo 'Sentences with sentiment objects and phrases: ' . "\n";
    printSentences($json_results);

    # print "Categorized Opinions with sentiment polarity (positive/negative)"
    echo "\n\n" . 'Categorized Opinions with sentiment polarity (positive/negative)' . "\n";
    printTree($json_results['opinions'], 0);
}

curl_close($ch);
# ----------- END cURL -----------
?>