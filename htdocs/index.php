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
                        
    //dirty word
$dirtyWords=array(
'/\ba+([^a-z])*n+([^a-z])*j+([^a-z])*e+([^a-z])*n+([^a-z])*g+\b/i',//anjeng
'/\ba+([^a-z])*s+([^a-z])*s+\b/i',//ass
'/\ba+([^a-z])*s+([^a-z])*s+([^a-z])*h+([^a-z])*o+([^a-z])*l+([^a-z])*e+\b/i',//asshole
'/\ba+([^a-z])*s+([^a-z])*s+([^a-z])*h+([^a-z])*a+([^a-z])*t+\b/i',//asshat
'/\ba+([^a-z])*s+([^a-z])*u+\b/i',//asu
'/\ba+([^a-z])*s+([^a-z])*u+([^a-z])*w+\b/i',//asuw
'/\ba+([^a-z])*s+([^a-z])*y+([^a-z])*u+\b/i',//asyu
'/\ba+([^a-z])*f+([^a-z])*u+\b/i',//afu
'/\bb+([^a-z])*a+([^a-z])*j+([^a-z])*i+([^a-z])*n+([^a-z])*g+([^a-z])*a+([^a-z])*n+\b/i',//bajingan
'/\bb+([^a-z])*a+([^a-z])*n+([^a-z])*g+([^a-z])*s+([^a-z])*a+([^a-z])*t+\b/i',//bangsat
'/\bb+([^a-z])*a+([^a-z])*s+([^a-z])*t+([^a-z])*a+([^a-z])*r+([^a-z])*d+\b/i',//bastard
'/\bb+([^a-z])*e+([^a-z])*w+([^a-z])*b+\b/i',//bewb
'/\bb+([^a-z])*e+([^a-z])*w+([^a-z])*b+([^a-z])*s+\b/i',//bewbs
'/\bb+([^a-z])*i+([^a-z])*t+([^a-z])*c+([^a-z])*h+\b/i',//bitch
'/\bb+([^a-z])*o+([^a-z])*l+([^a-z])*o+([^a-z])*t+\b/i',//bolot
'/\bb+([^a-z])*o+([^a-z])*o+([^a-z])*b+\b/i',//boob
'/\bb+([^a-z])*o+([^a-z])*o+([^a-z])*b+([^a-z])*s+\b/i',//boobs
'/\bb+([^a-z])*r+([^a-z])*e+([^a-z])*n+([^a-z])*g+([^a-z])*s+([^a-z])*e+([^a-z])*k+\b/i',//brengsek
'/\bb+([^a-z])*y+([^a-z])*l+([^a-z])*a+([^a-z])*t+\b/i',//bylat
'/\bc+([^a-z])*u+([^a-z])*k+\b/i',//cuk
'/\bc+([^a-z])*u+([^a-z])*n+([^a-z])*t+\b/i',//cunt
'/\bc+([^a-z])*o+([^a-z])*c+([^a-z])*o+([^a-z])*t+([^a-z])*e+\b/i',//cocote
'/\bc+([^a-z])*o+([^a-z])*c+([^a-z])*o+([^a-z])*t+([^a-z])*m+([^a-z])*u+\b/i',//cocotmu
'/\bc+([^a-z])*o+([^a-z])*k+\b/i',//cok
'/\bc+([^a-z])*y+([^a-z])*k+([^a-z])*a+\b/i',//cyka
'/\bd+([^a-z])*e+([^a-z])*m+([^a-z])*a+\b/i',//dema
'/\bd+([^a-z])*i+([^a-z])*c+([^a-z])*k+\b/i',//dick
'/\bd+([^a-z])*i+([^a-z])*c+([^a-z])*k+([^a-z])*h+([^a-z])*e+([^a-z])*a+([^a-z])*d+\b/i',//dickhead
'/\bd+([^a-z])*o+([^a-z])*b+([^a-z])*o+([^a-z])*l+\b/i',//dobol
'/\bf+([^a-z])*a+([^a-z])*g+\b/i',//fag
'/\bf+([^a-z])*a+([^a-z])*g+([^a-z])*g+([^a-z])*o+([^a-z])*t+\b/i',//faggot
'/\bf+([^a-z])*u+([^a-z])*c+([^a-z])*k+\b/i',//fuck
'/\bf+([^a-z])*u+([^a-z])*c+([^a-z])*k+([^a-z])*e+([^a-z])*d+\b/i',//fucked
'/\bf+([^a-z])*u+([^a-z])*c+([^a-z])*k+([^a-z])*e+([^a-z])*r+\b/i',//fucker
'/\bf+([^a-z])*u+([^a-z])*c+([^a-z])*k+([^a-z])*h+([^a-z])*e+([^a-z])*a+([^a-z])*d+\b/i',//fuckhead
'/\bf+([^a-z])*u+([^a-z])*c+([^a-z])*k+([^a-z])*i+([^a-z])*n+([^a-z])*g+\b/i',//fucking
'/\bi+([^a-z])*t+([^a-z])*i+([^a-z])*l+\b/i',//itil
'/\bj+([^a-z])*a+([^a-z])*m+([^a-z])*p+([^a-z])*u+([^a-z])*t+\b/i',//jamput
'/\bj+([^a-z])*a+([^a-z])*n+([^a-z])*c+([^a-z])*o+([^a-z])*k+\b/i',//jancok
'/\bj+([^a-z])*a+([^a-z])*n+([^a-z])*c+([^a-z])*u+([^a-z])*k+\b/i',//jancuk
'/\bj+([^a-z])*e+([^a-z])*m+([^a-z])*b+([^a-z])*u+([^a-z])*t+\b/i',//jembut
'/\bj+([^a-z])*e+([^a-z])*r+([^a-z])*k+\b/i',//jerk
'/\bk+([^a-z])*a+([^a-z])*m+([^a-z])*p+([^a-z])*a+([^a-z])*n+([^a-z])*g+\b/i',//kampang
'/\bk+([^a-z])*a+([^a-z])*m+([^a-z])*p+([^a-z])*r+([^a-z])*e+([^a-z])*t+\b/i',//kampret
'/\bk+([^a-z])*e+([^a-z])*n+([^a-z])*t+([^a-z])*u+\b/i',//kentu
'/\bk+([^a-z])*e+([^a-z])*n+([^a-z])*t+([^a-z])*o+([^a-z])*t+\b/i',//kentot
'/\bk+([^a-z])*i+([^a-z])*m+([^a-z])*c+([^a-z])*i+([^a-z])*l+\b/i',//kimcil
'/\bk+([^a-z])*i+([^a-z])*m+([^a-z])*a+([^a-z])*k+\b/i',//kimak
'/\bk+([^a-z])*o+([^a-z])*n+([^a-z])*t+([^a-z])*o+([^a-z])*l+\b/i',//kontol
'/\bk+([^a-z])*o+([^a-z])*t+([^a-z])*o+([^a-z])*r+\b/i',//kotor
'/\bk+([^a-z])*u+([^a-z])*n+([^a-z])*y+([^a-z])*u+([^a-z])*k+\b/i',//kunyuk
'/\bl+([^a-z])*a+([^a-z])*h+([^a-z])*o+([^a-z])*r+\b/i',//lahor
'/\bm+([^a-z])*e+([^a-z])*k+([^a-z])*i+\b/i',//meki
'/\bm+([^a-z])*e+([^a-z])*m+([^a-z])*e+([^a-z])*k+\b/i',//memek
'/\bm+([^a-z])*o+([^a-z])*t+([^a-z])*h+([^a-z])*e+([^a-z])*r+([^a-z])*f+([^a-z])*u+([^a-z])*c+([^a-z])*k+([^a-z])*e+([^a-z])*r+\b/i',//motherfucker
'/\bm+([^a-z])*o+([^a-z])*t+([^a-z])*h+([^a-z])*e+([^a-z])*r+([^a-z])*f+([^a-z])*u+([^a-z])*c+([^a-z])*k+([^a-z])*e+([^a-z])*r+([^a-z])*s+\b/i',//motherfuckers
'/\bn+([^a-z])*d+([^a-z])*e+([^a-z])*s+\b/i',//ndes
'/\bn+([^a-z])*d+([^a-z])*e+([^a-z])*z+\b/i',//ndez
'/\bn+([^a-z])*g+([^a-z])*e+([^a-z])*h+([^a-z])*e+\b/i',//ngehe
'/\bn+([^a-z])*g+([^a-z])*e+([^a-z])*n+([^a-z])*t+([^a-z])*o+([^a-z])*t+\b/i',//ngentot
'/\bn+([^a-z])*g+([^a-z])*e+([^a-z])*w+([^a-z])*e+\b/i',//ngewe
'/\bn+([^a-z])*j+([^a-z])*i+([^a-z])*n+([^a-z])*g+\b/i',//njing
'/\bn+([^a-z])*y+([^a-z])*i+([^a-z])*n+([^a-z])*g+\b/i',//nying
'/\bp+([^a-z])*a+([^a-z])*n+([^a-z])*t+([^a-z])*e+([^a-z])*k+\b/i',//pantek
'/\bp+([^a-z])*e+([^a-z])*c+([^a-z])*u+([^a-z])*n+\b/i',//pecun
'/\bp+([^a-z])*e+([^a-z])*l+([^a-z])*e+([^a-z])*r+\b/i',//peler
'/\bp+([^a-z])*e+([^a-z])*l+([^a-z])*i+\b/i',//peli
'/\bp+([^a-z])*e+([^a-z])*r+([^a-z])*e+([^a-z])*k+\b/i',//perek
'/\bp+([^a-z])*u+([^a-z])*k+([^a-z])*i+\b/i',//puki
'/\bp+([^a-z])*u+([^a-z])*t+([^a-z])*a+\b/i',//puta
'/\bp+([^a-z])*u+([^a-z])*t+([^a-z])*a+([^a-z])*n+([^a-z])*g+\b/i',//putang
'/\bp+([^a-z])*u+([^a-z])*s+([^a-z])*s+([^a-z])*y+\b/i',//pussy
'/\bs+([^a-z])*h+([^a-z])*i+([^a-z])*t+\b/i',//shit
'/\bs+([^a-z])*h+([^a-z])*i+([^a-z])*t+([^a-z])*h+([^a-z])*e+([^a-z])*a+([^a-z])*d+\b/i',//shithead
'/\bs+([^a-z])*h+([^a-z])*i+([^a-z])*t+([^a-z])*h+([^a-z])*o+([^a-z])*l+([^a-z])*e+\b/i',//shithole
'/\bs+([^a-z])*h+([^a-z])*i+([^a-z])*t+([^a-z])*t+([^a-z])*y+\b/i',//shitty
'/\bt+([^a-z])*a+([^a-z])*e+\b/i',//tae
'/\bt+([^a-z])*a+([^a-z])*e+([^a-z])*k+\b/i',//taek
'/\bt+([^a-z])*a+([^a-z])*i+\b/i',//tai
'/\bt+([^a-z])*a+([^a-z])*i+([^a-z])*k+\b/i',//taik
'/\bt+([^a-z])*a+([^a-z])*h+([^a-z])*i+\b/i',//tahi
'/\bt+([^a-z])*e+([^a-z])*m+([^a-z])*p+([^a-z])*i+([^a-z])*k+\b/i',//tempik
'/\bt+([^a-z])*a+([^a-z])*n+([^a-z])*g+([^a-z])*i+([^a-z])*n+([^a-z])*a+\b/i',//tangina
'/\bt+([^a-z])*i+([^a-z])*t+([^a-z])*s+\b/i',//tits
'/\bt+([^a-z])*o+([^a-z])*k+([^a-z])*e+([^a-z])*t+\b/i',//toket
'/\bt+([^a-z])*o+([^a-z])*l+([^a-z])*o+([^a-z])*l+\b/i',//tolol
'/\bu+([^a-z])*d+([^a-z])*i+([^a-z])*k+\b/i',//udik
);
    
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
                    $result = $bot->replyText($event['replyToken'], "Astaghfirullahaladzim, jangan berkata kotor");
                    $stickerMessageBuilder = new \LINE\LINEbot\MessageBuilder\StickerMessageBuilder("2","152");
                    $rp = $bot->pushMessage($event['source']['userId'], $stickerMessageBuilder);
                }
                
                
            //            if (stripos($event['message']['text'], $dirtyWord ) !== false) {
            //                $result = $bot->replyText($event['replyToken'], "Astaghfirullahaladzim, jangan berkata kotor");
            //            
            //            $stickerMessageBuilder = new \LINE\LINEbot\MessageBuilder\StickerMessageBuilder("2","152");
            //            $rp = $bot->pushMessage($event['source']['userId'], $stickerMessageBuilder);
            //        //    $rp = $bot->pushMessage($event['source']['groupId'], $stickerMessageBuilder);
            //        //    $rp = $bot->pushMessage($event['source']['roomId'], $stickerMessageBuilder);    
            //            }
                    }
                
                        
                
				// send same message as reply to user
				//$result = $bot->replyText($event['replyToken'], $event['message']['text']);

				// or we can use pushMessage() instead to send reply message
                //$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("Tolong jangan gunakan kata kasar");
                //$pm = $bot->pushMessage($event['source']['userId'], $textMessageBuilder);
				
				return $result->getHTTPStatus() . ' ' . $result->getRawBody();
                return $rp->getHTTPStatus() . ' ' . $rp->getRawBody();
                //return $pm->getHTTPStatus() . ' ' . $pm->getRawBody();
			}
		}
	}

});

// $app->get('/push/{to}/{message}', function ($request, $response, $args)
// {
// 	$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($_ENV['CHANNEL_ACCESS_TOKEN']);
// 	$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $_ENV['CHANNEL_SECRET']]);

// 	$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($args['message']);
// 	$result = $bot->pushMessage($args['to'], $textMessageBuilder);

// 	return $result->getHTTPStatus() . ' ' . $result->getRawBody();
// });

/* JUST RUN IT */
$app->run();