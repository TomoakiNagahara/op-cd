<?php
/** Upstream repository is private remote.
 *
 * @created   2022-12-02
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/* @var $app_root       string */
//	...
$github_account = Request('username');

//	Change directory.
if(!chdir($app_root) ){
	return false;
}
//	Skeleton.
if(!AddUpstream() ){
	return false;
}

//	Submodule.
if(!AddUpstreamSubmodule($app_root, $github_account) ){
	return false;
}

//	Successful.
return true;

/**
 *
 */
function AddUpstream(){
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

/**
 *
 */
function AddUpstreamSubmodule(string $app_root, string $github_account){
	//	Get submodule settings.
	if(!$configs = GetConfigs($github_account) ){
		return false;
	}

	//	Add submodule upstream.
	foreach( $configs as $config ){
		//	Change to the submodule directory.
		chdir($app_root . $config['path']);

		//	...
		`git remote add upstream {$config['url']}`;
	}
}

/**
 *
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

/**
 *
 */
function GetConfigs(string $github_account){
	//	Switch file name by GitHub account.
	$file_name = ($github_account === 'private') ? '.gitmodules': '.gitmodules_original';

	//	Get submodule settings.
	if(!$source = explode("\n", file_get_contents($file_name)) ){
		echo "Could not read .gitmodules. #".__LINE__;
		return false;
	}

	//	Get home position.
	if(!$pos = GetHomePosition('') ){
		return false;
	}

	//	Parse the submodule settings.
	$configs = [];
	while( $line = array_shift($source) ){
		//	[submodule "asset/core"]
		$name = substr($line, 12, -2);
		$name = str_replace('/', '-', $name);

		//	path, url, branch
		for($i=0; $i<3; $i++){
			list($key, $var) = explode("=", array_shift($source));
			$configs[$name][ trim($key) ] = trim($var);
		}

		//	...
		if( $github_account === 'private' ){
			$configs[$name]['url'] = 'repo:~'.substr($configs[$name]['url'], $pos);
		}else{
			$configs[$name]['url'] = str_replace('/onepiece-framework/', $github_account, $configs[$name]['url']);
		}
	}

	//	...
	return $configs;
}
