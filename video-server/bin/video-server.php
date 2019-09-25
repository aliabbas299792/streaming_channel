<?php

include "../../include/db.info.php";

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use MyApp\Chat;

require dirname(__DIR__) . '/vendor/autoload.php';

$chat = new Chat();
$server = IoServer::factory(
            new HttpServer(
              new WsServer(
                $chat
              )
            ), 888
          );


$pdo = new PDO("mysql:host=localhost;port=3306;dbname=erewhon", "root", "");

$jsonObj = new StdClass();
$videos = new StdClass();
$activeID = 0;

$server->loop->addPeriodicTimer(1, function () use ($pdo, $chat, $jsonObj, $videos, $activeID) //pass these into the loop
{   
	$getStreamVars = $pdo->prepare("SELECT UNIX_TIMESTAMP(time), list_vids, id FROM stream_schedule WHERE UNIX_TIMESTAMP(time) <= UNIX_TIMESTAMP(NOW())"); //select the most recent stream (probably will be redundant in the future)
	$getStreamVars->execute();
	$row2 = $getStreamVars->fetch(PDO::FETCH_ASSOC);
	
	if($row2['list_vids'] != ""){
		$streamCurrentTime = time() - $row2['UNIX_TIMESTAMP(time)']; //gets current time of stream, relative to since it started
		
		$ids = explode(" ", $row2['list_vids']); //gets the videos in the stream
		
		$totalLengthStream = 0; //sets the total length of the stream since it started to 0, to overried previous loop
		$totalLengthStream -= $streamCurrentTime; //subtracts the stream time from the stream length
		
		$totalVidLengthSinceStreamStart = 0; //this is used to hold up how long the stream is
		
		foreach( $ids as $id){
			
			$getVids = $pdo->prepare("SELECT * FROM stream_videos WHERE id=:id");
			$getVids->bindParam(':id', $id);
			$getVids->execute();
			$row = $getVids->fetch(PDO::FETCH_ASSOC);
			
			$videos->$id->length = $row['length'];
			$videos->$id->playTime = $totalLengthStream; //this sets the object to hold the time which can be used for what's described on line 58
			$videos->$id->name = $row['name'];
			$videos->$id->url = $row['url'];
			$videos->$id->id = $row['id']; 
			
			$totalLengthStream += $row['length']; //this allows it to determine how long ago the current and previous videos started and how long until the next (combined with line 41
			
			if($videos->$id->playTime <= 0){ //this checks if the current video is playing or has been played
				$videos->$id->length -= ($streamCurrentTime - $totalVidLengthSinceStreamStart); //sets the length of playing/played videos to how much of it is left relative to its own start
				if($videos->$id->length >= 0){ //at this point we know that the video is playing or played, but if the length is greater than 0, it is still playing, we discern the active video using this
					$activeID = $id; //this gives us the member id of the active video
				}
			}
			
			$totalVidLengthSinceStreamStart += $row['length']; //this allows for us to see the length of the stream till this iteration of the foreach loop
		}
		
		print_r($videos);
		
		//below puts it into json object
		$jsonObj->currentTime = $videos->$activeID->length;
		$jsonObj->id = $videos->$activeID->id;
		$jsonObj->name = $videos->$activeID->name;
		$jsonObj->url = $videos->$activeID->url;
			
		$jsonObjEncoded = json_encode($jsonObj); //encodes it
		 
		foreach ($chat->clients as $client) {                  
				$client->send($jsonObjEncoded); //sends to all connected users
		}
	}
});

$server->run();
