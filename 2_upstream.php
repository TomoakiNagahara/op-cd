<?php
/** Upstream repository is private remote.
 *
 * @created   2022-12-02
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//	...
$github_account = Request('username');

//	Change directory.
if(!chdir(_APP_ROOT_) ){
	return false;
}
//	Skeleton.
if(!AddUpstream() ){
	return false;
}

//	Submodule.
if(!AddUpstreamSubmodule(_APP_ROOT_, $github_account) ){
	return false;
}

//	Successful.
return true;

/** Add upstream to the Skeleton.
 *
 * @created    2022-12-05
 * @return     boolean
 */
function AddUpstream() : bool {
	//	Get origin URL by skeleton.
	if( $origin = `git config --get remote.origin.url` ){
		$origin = trim($origin);
	}else{
		echo "Get remote origin URL is failed.\n";
		return false;
	}

	//	Get home position.
	if(!$pos = GetHomePosition($origin) ){
		return false;
	}

	//	Add skeleton upstream.
	$url    = 'repo:~'.substr($origin, $pos);
	$result = `git remote add upstream $url`;
	if( $result ){
		echo $result;
	}

	//	...
	return true;
}

/** Add upstream to each the Submodules.
 *
 * @created    2022-12-05
 * @param      string      $app_root
 * @param      string      $github_account
 * @return     boolean
 */
function AddUpstreamSubmodule(string $app_root, string $github_account) : bool {
	//	Get submodule settings.
	if(!$configs = GitSubmoduleConfig($github_account) ){
		return false;
	}

	//	Add submodule upstream.
	foreach( $configs as $config ){
		//	Change to the submodule directory.
		chdir($app_root . $config['path']);

		//	...
		`git remote add upstream {$config['url']}`;
	}

	//	...
	return true;
}

/** Get home position by URL.
 *
 * @created    2022-12-05
 * @param      string          $url
 * @return     boolean|number
 */
function GetHomePosition(string $url){
	//	...
	static $pos;

	//	...
	if( $pos ){
		return $pos;
	}

	//	Detect home position.
	if( 0 === strpos($url, '/home/') ){
		$pos = 6;
	}else if( 0 === strpos($url, '/Users/') ){
		$pos = 7;
	}else{
		echo "Home position could not be detected. {$url}\n";
		return false;
	}

	//	Detect user name position.
	if(!($pos = strpos($url, '/', $pos)) ){
		echo "User name position could not be detected. {$url}\n";
		return false;
	}

	//	...
	return $pos;
}
