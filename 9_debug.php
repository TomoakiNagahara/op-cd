<?php
/** op-cd:/9_debug.php
 *
 * @created   2022-12-11
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** Debug
 *
 * @created    2022-12-11
 * @param      string      $string
 * @return     void
 */
function Debug(string $string) : void {
	//	...
	if( Request('debug') ?? true ){
		echo $string . PHP_EOL;
	}
}
