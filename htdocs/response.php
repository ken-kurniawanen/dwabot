<?php
require_once "database.php";

use \LINE\LINEBot\SignatureValidator as SignatureValidator;
use \LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder as ImagemapMessageBuilder;

class Response
{

	public $bot;
	public $getRequest;
	
	function __construct(){

		$this->getRequest = file_get_contents('php://input');

		/* Get Header Data */
		$signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

		/* Logging to Console*/
		file_put_contents('php://stderr', 'Body: '.$this->getRequest);

		/* Validation */
		if (empty($signature)){
			return $response->withStatus(400, 'Signature not set');
		}
		
		if($_ENV['PASS_SIGNATURE'] == false && ! SignatureValidator::validateSignature($this->getRequest, $_ENV['CHANNEL_SECRET'], $signature)){
			return $response->withStatus(400, 'Invalid signature');
		}

		/* Initialize bot*/
		$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($_ENV['CHANNEL_ACCESS_TOKEN']);
		$this->bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $_ENV['CHANNEL_SECRET']]);
	}

	public function requestHandler($type){

		$requestHandler = json_decode($this->getRequest, true);
		return $requestHandler[$type];
	}

/* Table Getter From Database*/
	public function tableCountGetter($table){

		$table = $dbo->prepare("SELECT * FROM :table");
		$table ->bindParam(":table", $table);
						
		if ($table -> execute()){

			$countTable = $table->count();
			return $countTable;
		} else {

			return "Error Database";
		}
	}

	public function tableDataGetter($table, $index = 0){

		$table = $dbo->prepare("SELECT * FROM :table WHERE id = :id");
		$table ->bindParam(":table", $table);
		$table ->bindParam(":id", $index);
						
		if ($table -> execute()){

			$dataTable = $table->fetchAll();
			return $dataTable;
		} else {

			return "Error Database";
		}
	}

/* Response Getter From Database*/
	public function preferredResponseGetter(){

		$getResponse = $this->tableDataGetter('responses', 1);

		return $getResponse[1]['response'];
	}

	public function randomResponseGetter(){
		
		$countTable = $this->tableCountGetter('responses');
		$randomIndex = mt_rand(0,$countTable);

		$getResponse = $this->tableDataGetter('responses', $randomIndex);

		return $getResponse[0]['response'];
	}

/* Bot Event Request Handler */
	public function botEventsRequestHandler(){
		
		$this->requestHandler('events');
	}

/* Bot Usability | Every method can only be used trough foreach */

	/*==================================Mandatory==================================*/
	
	public function botDisplayName($userId = null){

		$getProfile = $this->bot->getProfile();
		$profile = json_decode($getProfile, true);
		$displayName = $profile['displayName'];
		return $displayName;
	}

	/*General*/

	public function botEventReplyToken($source){

		return $source['replyToken'];
	}

	public function botEventType($source){

		return $source['type'];		
	}

	public function botEventTimestamp($source){

		return $source['timestamp'];		
	}


	/*Source*/

	public function botEventSourceType($source){
		
		return $source['source']['type'];
	}

	public function botEventSourceUserId($source){
	
		return $source['source']['userId'];
	}

	public function botEventSourceRoomId($source){
		
		return $source['source']['roomId'];
	}

	public function botEventSourceGroupId($source){
		
		return $source['source']['groupId'];
	}

	public function botEventSourceIsUser($source){
		
		if ($source['source']['type'] == "user"){
			return true;
		}
	}

	public function botEventSourceIsRoom($source){
		
		if ($source['source']['type'] == "room"){
			return true;
		}
	}

	public function botEventSourceIsGroup($source){
		
		if ($source['source']['type'] == "group"){
			return true;
		}
	}


	/*Message*/

	public function botEventMessageId($source){
		
		// text, image, video, audio, location, sticker
		return $source['message']['id'];
	}

	public function botEventMessageType($source){
		
		// text, image, video, audio, location, sticker
		return $source['message']['type'];
	}

	public function botEventMessageText($source){
		
		// text
		return $source['message']['text'];
	}

	public function botEventMessageTitle($source){
		
		// location
		return $source['message']['title'];
	}

	public function botEventMessageAddress($source){
		
		// location
		return $source['message']['address'];
	}

	public function botEventMessageLatitude($source){
		
		// location
		return $source['message']['latitude'];
	}

	public function botEventMessageLongitude($source){
		
		// location
		return $source['message']['longitude'];
	}

	public function botEventMessagePackadeId($source){
		
		// sticker
		return $source['message']['packageId'];
	}

	public function botEventMessageStickerId($source){
		
		// sticker
		return $source['message']['stickerId'];
	}


	/*Postback*/

	public function botEventPostbackData($source){
		
		return $source['postback']['data'];
	}


	/*Beacon*/

	public function botEventBeaconkHwid($source){
		
		return $source['beacon']['hwid'];
	}

	public function botEventBeaconType($source){
		
		return $source['beacon']['type'];
	}

	/*================================================================*/

	
	/* Bot Action */

	/*Leave*/
	public function botEventLeaveRoom($source){

		return $this->bot->leaveRoom($this->botEventSourceRoomId($source));
	}

	public function botEventLeaveGroup($source){

		return $this->bot->leaveRoom($this->botEventSourceGroupId($source));
	}


	/*Send Content*/
	public function botSendText($source, $text){

		$input = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text);
		$response = $bot->replyMessage($this->botEventReplyToken($source), $input);
		
		if ($response->isSucceeded()){
			
			return true;
		}
	}

	public function botSendImage($source, $original, $preview){

		$input = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($original, $preview);
		$response = $bot->replyMessage($this->botEventReplyToken($source), $input);
		
		if ($response->isSucceeded()){
			
			return true;
		}
	}

	public function botSendVideo($source, $original, $preview){

		$input = new \LINE\LINEBot\MessageBuilder\VideoMessageBuilder($original, $preview);
		$response = $bot->replyMessage($this->botEventReplyToken($source), $input);
		
		if ($response->isSucceeded()){
			
			return true;
		}
	}

	public function botSendAudio($source, $content, $duration){

		$input = new \LINE\LINEBot\MessageBuilder\AudioMessageBuilder($content, $duration);
		$response = $bot->replyMessage($this->botEventReplyToken($source), $input);
		
		if ($response->isSucceeded()){
			
			return true;
		}
	}

	public function botSendLocation($source, $title, $address, $latitude, $longitude){

		$input = new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder($title, $address, $latitude, $longitude);
		$response = $bot->replyMessage($this->botEventReplyToken($source), $input);
		
		if ($response->isSucceeded()){
			
			return true;
		}
	}

	public function botSendSticker($source, $packageId, $stickerId){

		$input = new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder($packageId, $stickerId);
		$response = $bot->replyMessage($this->botEventReplyToken($source), $input);
		
		if ($response->isSucceeded()){
			
			return true;
		}
	}

	public function botSendImagemap($source, $baseUrl, $altText, $baseSizeBuilder, array $imagemapActionBuilders){

		$input = new ImagemapMessageBuilder($baseUrl, $altText, $baseSizeBuilder, $imagemapActionBuilders);
		$response = $bot->replyMessage($this->botEventReplyToken($source), $input);
		
		if ($response->isSucceeded()){
			
			return true;
		}
	}

	public function botSendTemplate($source, $altText, $templateBuilder){

		$input = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($altText, $templateBuilder);
		$response = $bot->replyMessage($this->botEventReplyToken($source), $input);
		
		if ($response->isSucceeded()){
			
			return true;
		}
	}


	/*Receive Content*/
	public function botReceiveText($source){

		return $this->botEventMessageText($source);
	}

	public function botReceiveImage($source){

		if ($this->botEventMessageType($source) == 'image'){

			$response = $this->bot->getMessageContent($this->botEventMessageId($source));

			if ($response->isSucceeded()) {

				$folder = "image";
				$math = mt_rand(1,10000000000);
				$time = time();
				$extension = ".jpg";
				$file = $folder . '/' . $time . '-' . $math . $extension;
				$fp = fopen($file, 'w');
				fwrite($fp, $response->getRawBody());
				fclose($fp);

				return "https://dl.abror.net/content/$file";
			}
		}
	}

	public function botReceiveAudio($source){

		if ($this->botEventMessageType($source) == 'audio'){

			$response = $this->bot->getMessageContent($this->botEventMessageId($source));

			if ($response->isSucceeded()) {

				$folder = "audio";
				$math = mt_rand(1,10000000000);
				$time = time();
				$extension = ".jpg";
				$file = $folder . '/' . $time . '-' . $math . $extension;
				$fp = fopen($file, 'w');
				fwrite($fp, $response->getRawBody());
				fclose($fp);

				return "https://dl.abror.net/content/$file";
			}
		}
	}

	public function botReceiveVideo($source){

		if ($this->botEventMessageType($source) == 'video'){

			$response = $this->bot->getMessageContent($this->botEventMessageId($source));

			if ($response->isSucceeded()) {

				$folder = "video";
				$math = mt_rand(1,10000000000);
				$time = time();
				$extension = ".mp4";
				$file = $folder . '/' . $time . '-' . $math . $extension;
				$fp = fopen($file, 'w');
				fwrite($fp, $response->getRawBody());
				fclose($fp);

				return "https://dl.abror.net/content/$file";
			}
		}
	}

	public function botReceiveSticker($source){

		if ($this->botEventMessageType($source) == 'sticker'){

			$sticker = array();
			$packageId = array(
				'packageId'
				);
			$stickerId = array(
				'stickerId'
				);

			array_push($packageId['packageId'], $this->botEventMessagePackadeId($source));
			array_push($stickerId['stickerId'], $this->botEventMessageStickerId($source));

			array_push($sticker, $packageId);
			array_push($sticker, $stickerId);

			return $sticker;
		}
	}

	public function botReceiveLocation($source){

		if ($this->botEventMessageType($source) == 'location'){

			$location = array();
			$title = array(
				'title'
				);
			$address = array(
				'address'
				);
			$latitude = array(
				'latitude'
				);
			$longitude = array(
				'longitude'
				);
			
			array_push($title['title'], $this->botEventMessageTitle($source));
			array_push($address['address'], $this->botEventMessageAddress($source));
			array_push($latitude['latitude'], $this->botEventMessageLatitude($source));
			array_push($longitude['longitude'], $this->botEventMessageLongitude($source));

			array_push($location, $title);
			array_push($location, $address);
			array_push($location, $latitude);
			array_push($location, $longitude);

			return $location;
		}
	}


	/*Is Receive Content*/
	public function botIsReceiveText($source){

		if ($this->botEventMessageType($source) == 'text'){

			return true;
		}
	}

	public function botIsReceiveImage($source){

		if ($this->botEventMessageType($source) == 'image'){

			return true;
		}
	}

	public function botIsReceiveAudio($source){

		if ($this->botEventMessageType($source) == 'audio'){

			return true;
		}
	}

	public function botIsReceiveVideo($source){

		if ($this->botEventMessageType($source) == 'video'){

			return true;
		}
	}

	public function botIsReceiveSticker($source){

		if ($this->botEventMessageType($source) == 'sticker'){

			return true;
		}
	}

	public function botIsReceiveLocation($source){

		if ($this->botEventMessageType($source) == 'location'){

			return true;
		}
	}


	/*Main*/
	public function main(){

		foreach ($this->botEventsRequestHandler() as $source) {

			if ($this->botEventSourceIsUser($source)){

				if ($this->botIsReceiveText($source)){

					if ($this->botReceiveText($source) == "halo"){

						$this->botSendText($source, "halo juga");
					}
				}

				if ($this->botIsReceiveImage($source)){


				}

				if ($this->botIsReceiveAudio($source)){


				}

				if ($this->botIsReceiveVideo($source)){


				}

				if ($this->botIsReceiveLocation($source)){


				}

				if ($this->botIsReceiveSticker($source)){


				}
			}

			if ($this->botEventSourceIsRoom($source)){

				if ($this->botIsReceiveText($source)){

					if ($this->botReceiveText($source) == "halo"){

						$this->botSendText($source, "halo juga");
					}
				}

				if ($this->botIsReceiveImage($source)){


				}

				if ($this->botIsReceiveAudio($source)){


				}

				if ($this->botIsReceiveVideo($source)){


				}

				if ($this->botIsReceiveLocation($source)){


				}

				if ($this->botIsReceiveSticker($source)){


				}
			}

			if ($this->botEventSourceIsGroup($source)){

				if ($this->botIsReceiveText($source)){

					if ($this->botReceiveText($source) == "halo"){

						$this->botSendText($source, "halo juga, grup");
						$this->botSendText($source, "groupId ini : " . $this->botEventSourceGroupId($source));
						$this->botSendText($source, "UserId yang bilang 'halo' : " . $this->botEventSourceUserId($source));
					}
				}

				if ($this->botIsReceiveImage($source)){

					$url = $this->botReceiveImage();

					$this->botSendText($source, "makasih gambar nya, " . $url);
				}

				if ($this->botIsReceiveAudio($source)){

					$url = $this->botReceiveAudio();

					$this->botSendText($source, "makasih audio nya, " . $url);
				}

				if ($this->botIsReceiveVideo($source)){

					$url = $this->botReceiveVideo();

					$this->botSendText($source, "makasih video nya, " . $url);					
				}

				if ($this->botIsReceiveLocation($source)){

					$url = $this->botReceiveLocation();

					$this->botSendText($source, "makasih location nya, " . json_encode($url));
				}

				if ($this->botIsReceiveSticker($source)){

					$url = $this->botReceiveSticker();

					$this->botSendText($source, "makasih audio nya, " . json_encode($url));
				}
			}
		}
	}
}
?>