<?php

/*Loading Required Files*/
require __DIR__ . '/../lib/vendor/autoload.php';

/* Database Configuration */
$hostname   = "localhost";
$database   = "kotor"; 
$username   = "kotor";    
$password   = "jokem123";   

try {

    $dbo = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
} catch (PDOException $e) {

    $msg = $e->getMessage();
    echo $msg;
    die();
}

/* Boot Up Apps*/
use \LINE\LINEBot\SignatureValidator as SignatureValidator;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$configs =  [
	'settings' => [
        'displayErrorDetails' => true
        ]
    ];
$app = new Slim\App($configs);



/*
| Routes
| Define Routes Here
*/
$app->get('/', function ($request, $response) {

    $table_dirtyWords = $dbo->prepare("SELECT * FROM words");
    if ($table_dirtyWords -> execute()){
        return print_r($table_dirtyWords->fetch());
    }

	
});

$app->post('/', function ($request, $response) {

	/* Get Header Data */
	$body 	   = file_get_contents('php://input');
	$signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

	/* Logging to Console*/
	file_put_contents('php://stderr', 'Body: '.$body);

	/* Validation */
	if (empty($signature)){
		return $response->withStatus(400, 'Signature not set');
	}
	
	if($_ENV['PASS_SIGNATURE'] == false && ! SignatureValidator::validateSignature($body, $_ENV['CHANNEL_SECRET'], $signature)){
		return $response->withStatus(400, 'Invalid signature');
	}
    
	/* Initialize */
	$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($_ENV['CHANNEL_ACCESS_TOKEN']);
	$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $_ENV['CHANNEL_SECRET']]);
    
    
    /* User Profile */
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
'fvck',    
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
 $m_separator = "+(|[^a-z])*";
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
    
   
	foreach ($data['events'] as $event){
        
        if (stripos($event['message']['text'], "bye dwabot") !== false) {
            if ($event['source']['type'] == 'room') {
                $response = $bot->leaveRoom($event['source']['roomId']);
                return $response->getHTTPStatus() . ' ' . $response->getRawBody();  
            }
            elseif ($event['source']['type'] == 'group') {
                $response = $bot->leaveGroup($event['source']['groupId']);
                return $response->getHTTPStatus() . ' ' . $response->getRawBody();      
            }        
        }
        
        if (stripos($event['message']['text'], "about dwabot") !== false) {
            $response = $bot->replyText($event['replyToken'], 
"Dirty Word Alert Bot will scan any dirty word and its possible combination. Put me in your group or multichat and I will do the job.

DWABot did not alert some 'word' or didn't work well? Please click 'Suggest Word & Bug Report'
Have an idea for future DWABot feature? please click 'Suggest Feature'
*via personal

--DWABot v0.2-alpha--");
            return $response->getHTTPStatus() . ' ' . $response->getRawBody();          
        }
        
		elseif ($event['type'] == 'message'){   
            
			if($event['message']['type'] == 'text'){
                
                foreach ($dirtyWords as $dirtyWord) {
                    if (preg_match($dirtyWord, $event['message']['text'])) {
                        $response = $bot->replyText($event['replyToken'], "Astaghfirullahaladzim, jangan berkata kotor :(");
                    }
                }
                		
				return $response->getHTTPStatus() . ' ' . $response->getRawBody();
			}
		}
        
        
        elseif ($event['type'] == 'join'){
            $response = $bot->replyText($event['replyToken'], "Thanks for inviting me, i will alert your dirty friend");
            
            return $response->getHTTPStatus() . ' ' . $response->getRawBody();    
        }
	}

});

$app->run();