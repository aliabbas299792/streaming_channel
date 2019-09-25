var websocket2;
var video = document.getElementById("video");
var parsedData;
var vidCurrentURL;
var loading = document.getElementById('loadingScreen');
var startStream = document.getElementById('startStream');

var streamStartedOrNot = false;

startStream.onclick = function(){
	startStream.style.display = "none";
	loadingScreen.style.display = "";
}

var shouldBeTime = 0;

function parseVideoObjs(data){
	startStream.style.display = "none";
	loadingScreen.style.display = "";
	
	parsedData = JSON.parse(data);
	
	if(parsedData['currentTime'] != null){
		vidCurrentID = video.getElementsByTagName("source")[0].id;
		
		shouldBeTime = video.duration - parsedData['currentTime']; 
		
		if(vidCurrentID != parsedData['id']){
			video.getElementsByTagName("source")[0].id = parsedData['id'];
			video.getElementsByTagName("source")[0].src = parsedData['url']+"#t="+shouldBeTime;
			video.load();
		}else{
			if(video.currentTime != shouldBeTime){
				if(video.currentTime > shouldBeTime+2 || video.currentTime < shouldBeTime-2){
					video.getElementsByTagName("source")[0].src = parsedData['url']+"#t="+shouldBeTime;
					video.load();
					video.play();
				}
			}
		}
	}else{
		video.getElementsByTagName("source")[0].src = "";
		startStream.style.display = "";
		loadingScreen.style.display = "none";
	}
}

document.getElementsByTagName('video')[0].oncanplaythrough  = function(){
	video.play();
	loadingScreen.style.display = "none";
}

window.onload = function WebSocketSupport()
{
	video.autoplay = true;
	video.controls = false;
	
    if (browserSupportsWebSockets() === false) {
        document.write = "<h2>Sorry! Your web browser does not supports web sockets</h2>";

        return;
    }
	
	websocket2 = new WebSocket('ws:127.0.0.1:888');
	
	websocket2.onmessage = function(e) {
		parseVideoObjs(e.data);
    };
	
	websocket2.onerror = function(e) {
        document.write("<h1>Reload the page, if this doesn't go away by a few loads, the page is broken :/");
    };
}

function browserSupportsWebSockets() {
    if ("WebSocket" in window)
    {
        return true;
    }
    else
    {
        return false;
    }
}