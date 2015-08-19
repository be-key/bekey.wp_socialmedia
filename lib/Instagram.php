<?php

class Instagram{

    /*
     * Attributes
     */
    private $username, //Instagram username
            $access_token, //Your access token
            $userid; //Instagram userid

    /*
     * Constructor
     */
    function __construct($username='',$access_token='') {
            $this->username = $username;
            $this->access_token = $access_token;
    }
	
	public function getUserIDFromUserName($user = null){
			if($user){
				$user = $user;
			}else{
				$user = $this->username;
			}
            //Search for the username
            $useridquery = $this->queryInstagram('https://api.instagram.com/v1/users/search?q='.$user.'&client_id='.$this->access_token);
			
            if(!empty($useridquery) && $useridquery->meta->code=='200' && $useridquery->data[0]->id>0){
                //Found
                $this->userid=$useridquery->data[0]->id;
            } else {
                //Not found
                $this->error('getUserIDFromUserName');
            }
        
    }
	
    /*
     * Get the most recent media published by a user.
     * you can use the $args array to pass the attributes that are used by the GET/users/user-id/media/recent method
     */
    public function getUserMedia($args=array(), $user = null){
		
		if($user){
			$this->getUserIDFromUserName($user);
		}else{
			$this->getUserIDFromUserName();
		}
		
        if(strlen($this->access_token)>0){
            $qs='';
            if(!empty($args)){ $qs = '&'.http_build_query($args); } //Adds query string if any args are specified
            $shots = $this->queryInstagram('https://api.instagram.com/v1/users/'.$this->userid.'/media/recent?client_id='.$this->access_token.$qs); //Get shots
            if($shots->meta->code=='200'){
                return $shots;
            } else {
                $this->error('getUserMedia');
            }
        } else {
            $this->error('empty username or access token');
        }
    }
	
	public function getTags($args=array(), $tags){
		if(!empty($tags)){ 
			if(strlen($this->access_token)>0){
				$qs='';
            	if(!empty($args)){ $qs = '&'.http_build_query($args); }
				$instatags = $this->queryInstagram('https://api.instagram.com/v1/tags/'.$tags.'/media/recent?client_id='.$this->access_token.$qs); //Get shots
				if($instatags->meta->code=='200'){
					return $instatags;
				} else {
					$this->error('getUserMedia');
				}
			} else {
				$this->error('empty username or access token');
			}
		}else {
				$this->error('empty tags');
		}
	}
    /*
     * Method that simply displays the shots in a ul.
     * Used for simplicity and demo purposes
     * You should probably move the markup out of this class to use it directly in your page markup
     */
    
	
	public function getUser($id){
		if(!empty($id)){ 
			if(strlen($this->access_token)>0){
				$instauser = $this->queryInstagram('https://api.instagram.com/v1/users/'.$id.'/?access_token='.$this->access_token); //Get shots
				if($instauser->meta->code=='200'){
					return $instauser;
				} else {
					$this->error('getUserMedia');
				}
			} else {
				$this->error('empty username or access token');
			}
		}else {
				$this->error('empty tags');
		}
	}
	
    /*
     * Common mechanism to query the instagram api
     */
    public function queryInstagram($url){
        //prepare caching
       /* $cachefolder = __DIR__.'/instagram_cache/';
        $cachekey = md5($url);
        $cachefile = $cachefolder.$cachekey.'_'.date('i').'.txt'; //cached for one minute
*/
        //If not cached, -> instagram request
        /*if(!file_exists($cachefile)){
            //Request
            $request='error';
            if(!extension_loaded('openssl')){ $request = 'This class requires the php extension open_ssl to work as the instagram api works with httpS.'; }
            else { $request = file_get_contents($url); }

            //remove old caches
            $oldcaches = glob($cachefolder.$cachekey."*.txt");
            if(!empty($oldcaches)){foreach($oldcaches as $todel){
              unlink($todel);
            }}
            
            //Cache result
            $rh = fopen($cachefile,'w+');
            fwrite($rh,$request);
            fclose($rh);
        }*/
		
		$request = file_get_contents($url);
        //Execute and return query
		$query = json_decode($request);
        //$query = json_decode(file_get_contents($cachefile));
        return $query;
    }

    /*
     * Error
     */
    public function error($src=''){
        echo '/!\ error '.$src.'. ';
    }

}

?>
