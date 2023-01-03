<?php
/** op-cd:/Error.php
 *
 * @created    2023-01-02
 * @version    1.0
 * @package    op-cd
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */

/** Catch standard error.
 *
 * @created    2023-01-02
 * @see        https://www.php.net/manual/ja/function.set-error-handler.php
 * @see        https://www.php.net/manual/ja/function.restore-error-handler.php
 * @param      integer   $errno
 * @param      string    $error
 * @param      string    $file
 * @param      integer   $line
 * @param      array     $context is removed PHP8.0. You can receive the variables in the scope where the error occurred.
 * @return     boolean
 */
set_error_handler( function(int $no, string $message, string $file, int $line /* , array $context */ )
{
	//	...
	echo "{$file} #{$line} - [{$no}]{$message}\n";
	DebugTrace(debug_backtrace());

	/* Can restore PHP standard error handler.
	restore_error_handler();
	*/

	/* Can clear last error.
	 * https://www.php.net/manual/ja/function.error-clear-last.php
	error_clear_last();
	*/

	// If you return false, pass to PHP standard error handler.
	return true;
}, E_ALL);

/** Catch of uncaught error.
 *
 * @param \Throwable $e
 */
set_exception_handler(function( \Throwable $e)
{
	$file = $e->getFile();
	$line = $e->getLine();
	$message = $e->getMessage();
	echo "\nException: {$file} #{$line} - {$message}\n";
	DebugTrace($e->getTrace());
});

/** Called back on shutdown.
 *
 * @see http://www.php.net/manual/ja/function.pcntl-signal.php
 */
register_shutdown_function(function()
{
	//	...
	if(!$error = error_get_last() ){
		return;
	}

	//	...
	$type    = $error['type'];
	$file    = $error['file'];
	$line    = $error['line'];
	$message = $error['message'];

	//	...
	echo "{$file} #{$line} - [{$type}]{$message}\n";
	DebugTrace(debug_backtrace());
});

/** pcntl_signal
 *
 * @see https://www.php.net/manual/ja/function.pcntl-signal.php
 */
/*
if( function_exists('pcntl_signal') )
{
	// `pcntl_signal` is needs `ticks`
	declare(ticks=1);

	//	...
	function Signal($signal){
		switch($signal){
			case SIGTERM:
				//	Shutdown
				break;
			case SIGHUP:
				//	Restart
				break;
			case SIGUSR1:
				break;
			case SIGUSR2:
				break;
			default:
				//	Other
		}
	}

	//	...
	pcntl_signal(SIGTERM, "Signal");
	pcntl_signal(SIGHUP,  "Signal");
	pcntl_signal(SIGUSR1, "Signal");
}
*/
