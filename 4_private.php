<?php
/** Upstream repository is private remote.
 *
 * @created   2022-12-02
 * @version   1.0
 * @package   op-ci
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/* @var $app_root string */

//	Change directory.
if(!chdir($app_root) ){
	exit(__LINE__);
}

/* @var $github_account string */
$file_name = ($github_account === 'private') ? '.gitmodules': '.gitmodules_original';

//	Get submodule settings.
if(!$source = explode("\n", file_get_contents($file_name)) ){
	echo "Could not read .gitmodules. #".__LINE__;
	return false;
}

//	Init.
$configs = [];

//	Parse the submodule settings.
while( $line = array_shift($source) ){
	//	[submodule "asset/core"]
	$name = substr($line, 12, -2);
	$name = str_replace('/', '-', $name);
	//	path, url, branch
	for($i=0; $i<3; $i++){
		list($key, $var) = explode("=", array_shift($source));
		$configs[$name][ trim($key) ] = trim($var);
	}
}

//	Detect home position.
if( 0 === strpos($configs[$name]['url'], '/home/') ){
	$pos = 6;
}else if( 0 === strpos($configs[$name]['url'], '/Users/') ){
	$pos = 7;
}else{
	echo "Home position could not be detected. {$configs[$name]['url']}";
	return false;
}

//	Detect user name position.
if(!($pos = strpos($configs[$name]['url'], '/', $pos)) ){
	echo "User name position could not be detected. {$configs[$name]['url']}";
	return false;
}

//	Add upstream remote.
foreach( $configs as $config ){
	//	Change to the submodule directory.
	chdir($app_root . $config['path']);

	//	Calc upstream URL.
	$url = 'repo:~'.substr($config['url'], $pos);

	//	...
	`git remote add upstream $url`;
}

//	Successful.
return true;
