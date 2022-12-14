<?php
/** op-cd:/9_display.php
 *
 * @created   2022-12-11
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** Display
 *
 * @created    2022-12-11
 * @param      string      $string
 * @return     void
 */
function Display(string $string) : void {
	//	...
	if( Request('display') ?? true ){
		echo $string . PHP_EOL;
	}
}
