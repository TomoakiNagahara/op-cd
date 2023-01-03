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
 * @see   http://php.net/manual/ja/function.restore-error-handler.php
 * @param integer $errno
 * @param string  $error
 * @param string  $file
 * @param integer $line
 * @param array   $context
 */
set_error_handler( function(...$args)
{
	Debug($args);
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
	echo "\nException: {$file} #{$line} - {$message}\n\n";
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
	Debug($error);
});


/** pcntl_signal
 *
 * @see https://www.php.net/manual/ja/function.pcntl-signal.php
 */
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
	pcntl_signal(SIGTERM, Signal);
	pcntl_signal(SIGHUP,  Signal);
	pcntl_signal(SIGUSR1, Signal);
}
