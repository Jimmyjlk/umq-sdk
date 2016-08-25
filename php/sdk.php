<?php

class SDK {
	private $_config;

	function __construct() {
		require_once './config.php';
		$this->_config = $config;
	}

	public function get_organization_id() {
		$result = $this->_api_request(array(
			'UserEmail' => $this->_config['email'],
			'OrganizationAlias' => $this->_config['org_id'],
			'Action' => 'GetOrganizationId'
		));

		return $result['Data'];
	}

	public function publish_msg($content) {
		$result = $this->_api_request(array(
			'QueueId' => $this->_config['queue_id'],
			'Action' => 'PublishMsg',
			'PublisherId' => $this->_config['publisher_id'],
			'PublisherToken' => $this->_config['publisher_token'],
			'Content' => $content
		));

		return $result['RetCode'] === 0;
	}

	public function get_msg($num) {
		$result = $this->_api_request(array(
			'Action' => 'GetMsg',
			'QueueId' => $this->_config['queue_id'],
			'ConsumerId' => $this->_config['consumer_id'],
			'ConsumerToken' => $this->_config['consumer_token'],
			'Num' => $num
		));

		if ($result['RetCode'] === 0) {
			return $result['Data'];
		} else {
			return $result['Message'];
		}
	}
	
	public function ack_msg($message_id) {
		$result = $this->_api_request(array(
			'Action' => 'AckMsg',
			'QueueId' => $this->_config['queue_id'],
			'ConsumerId' => $this->_config['consumer_id'],
			'MsgId' => $message_id
		));

		if ($result['RetCode'] === 0) {
			return $result['Data'];
		} else {
			return $result['Message'];
		}
	}

	private function _api_request($data) {
		$post_data = '';
		foreach ($data as $i => $d) {
			$post_data .= $i . '=' . $d . '&';
		}

		$post_data = substr($post_data, 0, -1);		

		$c = curl_init();
		
		curl_setopt($c, CURLOPT_POST, 1);  
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 3);  
		curl_setopt($c, CURLOPT_POSTFIELDS, $post_data);  
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $this->_config['url']);  

		$result = curl_exec($c);  	 
		
		if (curl_errno($c)) {
			$result = curl_error($c);
		}

		curl_close($c);
		$result = json_decode($result, true);
		
		if ($result === null) {
			throw new Exception('Server error, nothing returned.');
		}

		return $result;
	}
}

?>
