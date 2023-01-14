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
function Debug($args, $trace=true)
{
	static $_debug = null;
	if( $_debug ===  null ){
		$_debug = Request('debug') ?? 0;
	}

	//	...
	if(!$_debug ){
		return;
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
	if( $trace ){
		DebugTrace(debug_backtrace());
	}
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
		$file   = $trace['file']     ?? null;
		$line   = $trace['line']     ?? null;
		$func   = $trace['function'] ?? null;
		$class  = $trace['class']    ?? null;
		$type   = $trace['type']     ?? null;
		//	$object = $trace['object'];
		$args   = $trace['args']     ?? [];

		//	...
		if( $file ){
			$file = str_replace($_root, 'CD:', $file);
			$file = str_pad($file, 20, ' ', STR_PAD_RIGHT);
		}

		//	...
		if( $line ){
			$line = (string)$line;
			$line = str_pad($line, 3, ' ', STR_PAD_LEFT);
		}

		//	...
		$args = DebugTraceArgs($args);

		//	...
		if( $type ){
			$function = "{$class}{$type}{$func}";
		}else{
			$function = $func;
		}

		//	...
		$head = $file ? "{$file} {$line} - ": null;

		//	...
		echo "{$head}{$function}({$args})\n";
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
				$arg = str_replace(["\n","\r","\t"], ['\n','\r','\t'], $arg);
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
