<?php
  $json = "
  {
  "events": [
    {
      "replyToken": "nHuyWiB7yP5Zw52FIkcQobQuGDXCTA",
      "type": "message",
      "timestamp": 1462629479859,
      "source": {
        "type": "user",
        "userId": "U206d25c2ea6bd87c17655609a1c37cb8"
      },
      "message": {
        "id": "325708",
        "type": "text",
        "text": "Hello, world"
      }
    },
    {
      "replyToken": "nHuyWiB7yP5Zw52FIkcQobQuGDXCTA",
      "type": "follow",
      "timestamp": 1462629479859,
      "source": {
        "type": "user",
        "userId": "U206d25c2ea6bd87c17655609a1c37cb8"
      }
    }
  ]
}
  ";

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