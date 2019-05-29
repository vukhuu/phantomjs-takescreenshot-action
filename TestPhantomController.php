<?php
class TestPhantomController extends CController {
	public function actions(){
		return array(
			"takeScreenshot" => array(
				"class" => "ext.phantomjs.EWebScreenshotAction",
				"username" => "username", // username to use this web service
				"password" => "password", // password to use this web service
				"allowedIps" => array(), // restricted to some IPs only, leave blank for any ip
			)
		);
	}
	
	public function actionTestScreenshot() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->createAbsoluteUrl("/test/takeScreenshot"));
		$fields = array(
			"username" => "username",
			"password" => "password",
			"webUrl" => "http://www.google.com",
		);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		
		$result = curl_exec($ch);
		$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($httpStatus == 200 && ! empty($result)) {
			file_put_contents("C:/test.jpg", $result);
		} else { // Error occurred
			echo $result;
		}
	}
}
?>