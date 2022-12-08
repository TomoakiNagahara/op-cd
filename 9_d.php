<?php
/** D
 *
 * @created   2022-12-08
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** D
 *
 * @created    2022-12-08
 * @param      mixed       $args
 */
function D($args) : void {
	//	...
	$temp = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];

	//	...
//	$func = $temp['function'];
	$file = $temp['file'];
	$line = $temp['line'];

	//	...
	echo "\n{$file} #{$line}\n";
	var_dump($args);
}
