<?php

require_once __DIR__ . "/youtube-php-api-v3/Google_Client.php";
require_once __DIR__ . "/youtube-php-api-v3/contrib/Google_YouTubeService.php";

class Youtube{
  	public $_youtube;
	
    public function __construct(){
		$DEVELOPER_KEY = 'AIzaSyAjtcg0Ls3HYTuJhVWmqfJ94a6CX_05x8c';
		$client = new Google_Client();
		$client->setDeveloperKey($DEVELOPER_KEY);
		$this->_youtube = new Google_YoutubeService($client);	
    }
	
    public function get_videoList(){
		$videoList = $this->_youtube->search->listSearch('snippet', array('q' => 'grafikarttv'));
		return $videoList;
	}
	
	public function get_channelList(){
		$videoList = $this->_youtube->search->listSearch('snippet', array('q' => 'grafikart'));
		return $videoList;
    }
	
	/*public function get_videoList(){
		$videoList = $this->query_youtube('https://www.googleapis.com/youtube/v3/search?part=id%2Csnippet&q=grafikarttv&key=AIzaSyAjtcg0Ls3HYTuJhVWmqfJ94a6CX_05x8c');
		return $videoList;
    }
	
	public function get_channelList(){
		$videoList = $this->query_youtube('https://www.googleapis.com/youtube/v3/subscriptions?part=id%2Csnippet&channelId=UCbcMVpCRxwcqaT1wiWM_ocg&key=AIzaSyAjtcg0Ls3HYTuJhVWmqfJ94a6CX_05x8c');
		return $channelList;
    }
	
	public function query_youtube($url){
		$request = file_get_contents($url);
		$query = json_decode($request);
        return $query;
    }*/
	
}