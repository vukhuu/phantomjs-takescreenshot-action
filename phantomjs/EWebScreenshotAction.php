<?php
/**
 * 
 * A web service which is implemented as an action to take screenshot of a website & return the file content to web service consumer
 * The returned file content should be saved to file with .jpg extension
 * @author vu.khuu@glandoresystems.com
 *
 */
class EWebScreenshotAction extends CAction {
	public $allowedIps = array();
	// Username & password to be used to call this service
	public $username = "username";
	public $password = "password";
	// The command to run phantomjs
	public $phantomjsCommand = "phantomjs";
	
	public function run() {
		$request = Yii::app()->request;
		if ($request->isPostRequest) {
			// Get username & password to authenticate
			$username = $request->getParam("username", "");
			$password = $request->getParam("password", "");
			
			// Get web url to take screenshot
			$webUrl = $request->getParam("webUrl", "");
			
			// Get IP of requester
			$ip = $request->getUserHostAddress();
			if ((! empty($this->allowedIps) && in_array($ip, $this->allowedIps) || empty($this->allowedIps)) && $username == $this->username && $password == $this->password) {
				$tempFolder = Yii::getPathOfAlias("application"). "/runtime";
				
				$screenshotFileName = $tempFolder . "/" . uniqid() . ".jpg";
				$phantomjsCommand = $this->phantomjsCommand . " " . dirname(__FILE__) . "/js/rasterize.js " . $webUrl . " " . $screenshotFileName;
				
				exec($phantomjsCommand);
				
				$status = false;
				$fileContent = "";
				if (file_exists($screenshotFileName)) {
					$status = true;
					$fileContent = file_get_contents($screenshotFileName);
					
					// Remove file
					unlink($screenshotFileName);
				}
				echo $fileContent;
			} else {
				throw new CHttpException(403, "Authentication failed.");
			}
		} else {
			throw new CHttpException(400, "This page serves POST requests only.");
		}
	}
}
?>