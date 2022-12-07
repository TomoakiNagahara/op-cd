<?php
/** Request
 *
 * Request function is parse the format in which the key and value are connected with equal.
 *
 * @created   2022-12-07
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** Return request value by key.
 *
 * @created    2022-12-07
 * @param      string      $key
 * @return     mixed
 */
function Request(string $key){
	//	...
	static $_argv;

	//	...
	if(!$_argv ){
		$_argv = GetArgv();
	}

	//	...
	return $_argv[$key] ?? null;
}

/** Parse argv.
 *
 * @created    2022-12-07
 * @return     array
 */
function GetArgv() : array {
	//	...
	$argv = [];

	//	...
	foreach($_SERVER['argv'] as $pair ){
		//	...
		if(!strpos($pair, '=') ){
			continue;
		}

		//	...
		list($key, $var) = explode('=', $pair);

		//	...
		$argv[$key] = $var;
	}

	//	...
	return $argv;
}
