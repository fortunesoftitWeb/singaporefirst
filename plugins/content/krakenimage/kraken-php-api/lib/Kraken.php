<?php

class Kraken {
    protected $auth = array();

    public function __construct($key = '', $secret = '') {
        $this->auth = array(
            "auth" => array(
                "api_key" => $key,
                "api_secret" => $secret
            )
        );
    }

    public function url($opts = array()) {
        $data = json_encode(array_merge($this->auth, $opts));
        $response = self::request($data, 'https://api.kraken.io/v1/url', 'url');
        return $response;
    }

    public function upload($opts = array(),$id=null) {
		
	    if (!isset($opts['file'])) {
            return array(
                "success" => false,
                "error" => "File parameter was not provided"
            );
        }

        if (preg_match("/\/\//i", $opts['file'])) {
            $opts['url'] = $opts['file'];
            unset($opts['file']);
            return $this->url($opts);
        }

        if (!file_exists($opts['file'])) {
            return array(
                "success" => false,
                "error" => 'File `' . $opts['file'] . '` does not exist'
            );
        }

        if (class_exists('CURLFile')) {
			$file = new CURLFile($opts['file']);
		} else {
			$file = '@' . $opts['file'];
		}

        unset($opts['file']);

        $data = array_merge(array(
            "file" => $file,
            "data" => json_encode(array_merge($this->auth, $opts))
        ));
		
		$response = self::request($data, 'https://api.kraken.io/v1/upload', 'upload');

		$db = JFactory::getDbo();
		
		if(!empty($id)){
			$query =  "update `qqclj_medialibrary` set process = process+1 where id = $id ";
			$db->setQuery($query);
			$result = $db->execute();
		}		
		return $response;
    }

    public function status() {
        $data = array('auth' => array(
            'api_key' => $this->auth['auth']['api_key'],
            'api_secret' => $this->auth['auth']['api_secret']
        ));

        $response = self::request(json_encode($data), 'https://api.kraken.io/user_status', 'url');
        return $response;
    }

    private function request($data, $url, $type) {
        $curl = curl_init();

        if ($type === 'url') {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_FAILONERROR, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);

        return $response;
    }
}
