<?php
/** op-cd:/Debug.php
 *
 * @created    2023-01-02
 * @version    1.0
 * @package    op-cd
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */

/** Debug
 *
 * @created    2023-01-02
 * @version    1.0
 * @package    op-cd
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */
function Debug($args)
{
	static $_debug = null;
	if( $_debug ===  null ){
		$_debug = Request('debug') ? 1: 0;
	}

	//	...
	if( isset($args) ){
		echo "\n";
		if( is_string($args) ){
			echo "{$args}\n";
		}else{
			var_dump($args);
		}
	}

	//	...
	DebugTrace(debug_backtrace());
}

/** Debug trace
 *
 * @created    2023-01-02
 * @param      array       $traces
 */
function DebugTrace($traces)
{
	//	...
	static $_root;
	if(!$_root ){
		$_root = __DIR__;
	}

	//	Display the message for the CD root, only once.
	static $_cd;
	if(!$_cd ){
		$_cd = true;
		echo "CD is {$_root}\n";
	}else{
		echo "\n";
	}

	//	...
	foreach( $traces as $trace){
		$file   = $trace['file'];
		$line   = $trace['line'];
		$func   = $trace['function'] ?? null;
		$class  = $trace['class']    ?? null;
		$type   = $trace['type']     ?? null;
		//	$object = $trace['object'];
		$args   = $trace['args']     ?? [];

		//	...
		$file = str_replace($_root, 'CD:', $file);

		//	...
		$args = DebugTraceArgs($args);

		//	...
		if( $type ){
			$function = "{$class}{$type}{$func}";
		}else{
			$function = $func;
		}

		//	...
		echo "{$file} #{$line} - {$function}({$args})\n";
	}
}

/** Debug args
 *
 * @created    2023-01-02
 * @param      array       $args
 * @return     string
 */
function DebugTraceArgs(array $args) : string
{
	//	...
	$results = [];

	//	...
	foreach($args as $arg){
		switch( $type = gettype($arg) ){
			case 'null':
				$result = 'NULL';
				break;
			case 'boolean':
				$result = $arg ? 'true':'false';
				break;
			case 'integer':
				$result = $arg;
				break;
			case 'string':
				$result = "'{$arg}'";
				break;
			default:
				$result = $type;
			break;
		}
		$results[] = $result;
	}

	//	...
	return join(', ', $results);
}
