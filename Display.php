<?php
/** op-cd:/Display.php
 *
 * @created    2023-01-02
 * @version    1.0
 * @package    op-cd
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */

/** Display message
 *
 * @created    2023-01-02
 */
function Display(string $message)
{
	//	...
	static $_display = null;
	static $_debug   = null;

	//	...
	if( $_display === null ){
		$_display = Request('display') ?? 1;
		$_debug   = Request('debug')   ?? 0;
	}

	//	...
	if( $_display ){
		//	...
		echo $message . "\n";

		//	...
		if( $_debug ){
			DebugTrace(debug_backtrace());
		}
	}
}
