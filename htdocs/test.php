<?php

  $myfile = fopen("data.json", "r") or die("Unable to open file!");
  $json = fgets($myfile);
  fclose($myfile);

  function botEventsRequestHandler(){

    $requestHandler = json_decode($json);
    return $requestHandler['events'];
  }

  function botEventReplyToken($source){

    return $source['replyToken'];
  }

  foreach (botEventsRequestHandler() as $source) {


    print_r($source);
    echo "\n \n";

    echo botEventReplyToken($source);

    }


?>