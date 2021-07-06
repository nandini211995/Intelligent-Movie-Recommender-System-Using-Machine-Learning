<?php

// Description: Sentiment Analyzer API usage example.
// Copyright: (C) 2016 EffectiveSoft Ltd. All Rights Reserved.
// Technical support: technical-support@effective-soft.com

header('Content-Type: text/plain');

# list of reviews in JSON format
$reviews = array(
    array(
        "id" => "snt1",
        "text" => "Firstly i am not good at writing reviews so apology for any mistakes.

Coming to the movie it is another fine horror flick by vikram bhatt joins one among the bollywood's best horror movies ever.

The story revolves 7 friends on their reunion decide to go to a locked down hotel Grandios when they hear about that hotel owner had committed suicide following dreadful deaths have been caused in the hotel room number 2046 due to paranormal acitivity.Least they know that hotel was built upon the mental asylum which was burned down due to short circuit. will they able to survive against the onslaught of evil??? 

The movie contains some scary moments accompanied by high quality stereo effects which will make your heart quiver by fear.Good acting by the star cast and less story leading to shortened run time around 1hr 30min unlike most of the Bolly horror flicks.only one con in the film is last 2 to 3 scenes which could be done better.

overall its a must watch movie don't miss it..Go with your mates and raid the late night shows..."
    )
    
    
    
);

$json_reviews = json_encode($reviews);

# ----------- FUNCTIONS -----------
# function for printing sentences



function printSentences($response) {
$neg_avg=0;
$neg_sent=0;
$neg_high=-999999;
$neg_low=0;

$pos_avg=0;
$pos_sent=0;
$pos_high=0;
$pos_low=999999;


$net_avg=0;
$net_sent=0;
    foreach($response['sentences'] as $resp_sent) {
        echo "\n" . 'Sentence Weight = ' . $resp_sent['w']  ;
if($resp_sent['w']>0)
{
$pos_avg= ($pos_avg+$resp_sent['w'])/2  ;
$pos_sent++;
if($resp_sent['w']>$pos_high){
$pos_high=$resp_sent['w'];

}
if($resp_sent['w']<$pos_low){
$pos_low=$resp_sent['w'];

}



}


else if($resp_sent['w']<0)
{

$neg_avg= ($neg_avg+$resp_sent['w'])/2  ;
$neg_sent++;
if($resp_sent['w']>$neg_high){
$neg_high=$resp_sent['w'];

}
if($resp_sent['w']<$neg_low){
$neg_low=$resp_sent['w'];

}


}
else{
$net_avg= 2.5 ;
$net_sent++;
}


    }

$pos_scaled=5*($pos_avg-$pos_low)/($pos_high-$pos_low);
$neg_scaled=5*($neg_avg-$neg_low)/($neg_high-$neg_low);
$F_factor=max($pos_sent,$neg_sent,$net_sent)/min($pos_sent,$neg_sent,$net_sent);
echo "\n" . 'Positive Weight Average = ' . $pos_avg  ;
echo "\n" . 'Positive Sentence Count= ' . $pos_sent  ;
echo "\n" . 'Positive High= ' . $pos_high;
echo "\n" . 'Positive Low= ' . $pos_low;
echo "\n" . 'Positive Scaled Average = ' . $pos_scaled;
echo "\n" . 'Negative Weight Average = ' . $neg_avg  ;
echo "\n" . 'Negative Sentence Count= ' . $neg_sent  ;
echo "\n" . 'Negative High= ' . $neg_high;
echo "\n" . 'Negative Low= ' . $neg_low;
echo "\n" . 'Negative Scaled Average = ' . $neg_scaled;
echo "\n" . 'Neutral Weight = ' . $net_avg  ;
echo "\n" . 'Neutral Sentence Count= ' . $net_sent  ;
echo "\n" . 'Factor = ' . $F_factor  ;
if($pos_sent==max($pos_sent,$neg_sent,$net_sent)){

$net_avg  =$net_avg  /$F_factor;
$neg_scaled=$neg_scaled/$F_factor;
}

else if($neg_sent==max($pos_sent,$neg_sent,$net_sent)){
$pos_scaled=$pos_scaled/$F_factor;
$net_avg  =$net_avg  /$F_factor;
}
else{
$pos_scaled=$pos_scaled/$F_factor;
$neg_scaled=$neg_scaled/$F_factor;

}
$rating=5+($pos_scaled+$neg_scaled+$net_avg)/3;

echo "\n" . 'Rating  = ' . $rating  ;

}

# function for printing categorized opinions
/*function printTree($node, $height)
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
} */

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