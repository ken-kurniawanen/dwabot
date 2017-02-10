<?php

require __DIR__ . '/../lib/vendor/autoload.php';

use \LINE\LINEBot\SignatureValidator as SignatureValidator;

// load config
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// initiate app
$configs =  [
	'settings' => ['displayErrorDetails' => true],
];
$app = new Slim\App($configs);

/* ROUTES */
$app->get('/', function ($request, $response) {
	return "Lanjutkan!";
});

$app->post('/', function ($request, $response)
{
	// get request body and line signature header
	$body 	   = file_get_contents('php://input');
	$signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

	// log body and signature
	file_put_contents('php://stderr', 'Body: '.$body);

	// is LINE_SIGNATURE exists in request header?
	if (empty($signature)){
		return $response->withStatus(400, 'Signature not set');
	}

	// is this request comes from LINE?
	if($_ENV['PASS_SIGNATURE'] == false && ! SignatureValidator::validateSignature($body, $_ENV['CHANNEL_SECRET'], $signature)){
		return $response->withStatus(400, 'Invalid signature');
	}
    
	// init bot
	$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($_ENV['CHANNEL_ACCESS_TOKEN']);
	$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $_ENV['CHANNEL_SECRET']]);
    
    
    //user
    $ehe = $bot->getProfile('<userId>');
    $profile = json_decode($ehe, true);
    $dp = $profile['displayName'];
                        
/*
	Start Dirty Word Database
*/
$data = array(
'anjeng',
'ass',
'asshole',
'asshat',
'asu',
'asuw',
'asyu',
'afu',
'bajingan',
'bangsat',
'bastard',
'bewb',
'bewbs',
'bitch',
'bolot',
'boob',
'boobs',
'brengsek',
'blyat',
'cuk',
'cuki',
'cukimay',
'cunt',
'cocote',
'cocotmu',
'cok',
'cyka',
'dema',
'dick',
'dickhead',
'dobol',
'fag',
'faggot',
'fuck',
'fucked',
'fucker',
'fuckhead',
'fucking',
'gago',
'goblok',
'goblog',
'gobloq',
'geblek',
'itil',
'jamput',
'jancok',
'jancuk',
'jembut',
'jerk',
'kampang',
'kampret',
'kentu',
'kentot',
'kimcil',
'kimak',
'kontol',
'kotor',
'kunyuk',
'lahor',
'meki',
'memek',
'motherfucker',
'motherfuckers',
'ndes',
'ndez',
'ngehe',
'ngentot',
'ngewe',
'njing',
'nying',
'pantek',
'pecun',
'peler',
'peli',
'peju',
'pejuh',
'perek',
'puki',
'pukimay',
'puta',
'putang',
'pussy',
'sange',
'shit',
'shithead',
'shithole',
'shitty',
'tae',
'taek',
'tai',
'taik',
'tahi',
'tempik',
'tangina',
'titit',
'titid',
'tits',
'toket',
'tolol',
'udik',
 	);

 $f_separator = "/\b";
 $m_separator = "+([^a-z])";
 $e_separator = "+\b/i";
 $dirtyWords = array();
 	
 foreach ($data as $key => $value){
 	
 	$word = "";
 	$word = $word . $f_separator;
 	
 	for ($i = 0; $i != strlen($value); $i++){
 		
 		$word = $word . $value[$i];
 		if ($i+1 != strlen($value)){
 			
 			$word = $word . $m_separator;
 		}
 	}
 	
 	$word = $word . $e_separator;
 	array_push($dirtyWords, $word);
 }

 /*
End Of Dirty Words Database
 */  
    //get event webhook
	$data = json_decode($body, true);
    
   
	foreach ($data['events'] as $event)
	{
		if ($event['type'] == 'message')
		{   
            
			if($event['message']['type'] == 'text')
			{    
            foreach ($dirtyWords as $dirtyWord) {
                if (preg_match($dirtyWord, $event['message']['text'])) {
                    $result = $bot->replyText($event['replyToken'], "Astaghfirullahaladzim, jangan berkata kotor :(");
                }
                
                //				$result = $bot->replyText($event['replyToken'], $event['message']['text']);

                    }
                
                        
                
				//$result = $bot->replyText($event['replyToken'], $event['message']['text']);

                //$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("Tolong jangan gunakan kata kasar");
                //$pm = $bot->pushMessage($event['source']['userId'], $textMessageBuilder);
				
				return $result->getHTTPStatus() . ' ' . $result->getRawBody();
			}
		}
	}

});

$app->run();