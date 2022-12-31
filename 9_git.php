<?php
/** op-cd:/9_git.php
 *
 * @created   2022-12-09
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** Git wrapper functions.
 *
 * @created    2022-12-09
 * @param      string      $command
 * @param      string      $target
 * @return     string
 */
function Git(string $command, string $target='') : void {
	//	...
	static $_configs;
	static $_display;
	static $_debug;

	//	...
	if(!isset($_configs) ){
		$_configs = GitSubmoduleConfig( Request('branch') );
		$_display = Request('display') ?? 1;
		$_debug   = Request('debug')   ?? 0;
	}

	//	...
	if( $_display or $_debug ){ _Git_Do_Label_($command, $target); }

	//	...
	$current_dir = getcwd();
	$redirect    = $_debug ? '': '2>&1';

	//	Submodule first, before main.
	foreach( $_configs as $config ){
		//	...
		$path   = $config['path'];
		chdir( _APP_ROOT_ . $path );

		//	...
		if( $_display or $_debug ){ echo "\n  -- {$path} --\n"; }

		//	If rebase, first checkout.
		if( $command === 'rebase' ){
			$branch  = explode('/', $target)[1];
			_Git_Result_(`git checkout {$branch} {$redirect}` ?? '', 'checkout', $target);
		}
		_Git_Result_(`git {$command} {$target} {$redirect}` ?? '', $command, $target);
	}

	//	...
	if( $_display or $_debug ){ echo "\n  -- Skeleton --\n"; }

	//	Skeleton
	if( $command === 'rebase' ){
		//	If rebase, first checkout.
		$branch  = explode('/', $target)[1];
		_Git_Result_(`git checkout {$branch} {$redirect}` ?? '', 'checkout', $target);
	}
	_Git_Result_(`git {$command} {$target} {$redirect}` ?? '', $command, $target);

	//	Recovery directory.
	chdir($current_dir);
}

/** Switch branch.
 *
 * @created    2022-12-06
 * @param      string      $branch
 * @return     boolean
 */
function GitBranch(string $branch) : bool {
	//	Switch branch.
	$strings = `git switch {$branch} 2>/dev/null`;

	//	...
	$result = [];
	foreach( explode("\n", $strings) as $string ){
		//	...
		switch( $string ){
			case '':
			case 'M	.gitmodules':
			case "Your branch is up to date with 'origin/{$branch}'.":
				continue 2;
		}

		//	...
		$result[] = $string;
	}

	//	...
	if( $result ){
		array_unshift($result, "\nUncommitted files.");
		echo join("\n", $result)."\n\n";
	}

	//	...
	return true;
}

/** Get config by .gitmodules file.
 *
 * @created    2022-12-05
 * @param      string         $github_account
 * @return     boolean|array
 */
function GitSubmoduleConfig(string $github_account){
	//	Switch file name by GitHub account.
	$file_name = ($github_account === 'private') ? '.gitmodules': '.gitmodules_original';

	//	Get submodule settings.
	if(!file_exists($file_name) ){
		echo "This file does not exist. ($file_name)\n";
		exit(1);
	}

	//	Get submodule settings from file.
	if(!$file = file_get_contents($file_name) ){
		echo "Could not read this file. ($file_name)\n";
		exit(1);
	}

	//	Parse submodule settings.
	$source = explode("\n", $file);

	//	If repository is private.
	if( $github_account === 'private' ){
		//	Get position of home path.
		$position_of_home = strlen(`realpath ~/`) -1;
	}

	//	Parse the submodule settings.
	$configs = [];
	while( $line = array_shift($source) ){
		//	[submodule "asset/core"]
		$name = substr($line, 12, -2);
		$name = str_replace('/', '-', $name);

		//	path, url, branch
		for($i=0; $i<3; $i++){
			list($key, $var) = explode("=", array_shift($source));
			$configs[$name][ trim($key) ] = trim($var);
		}

		//	...
		if( $github_account === 'private' ){
			$configs[$name]['url'] = 'repo:~'.substr($configs[$name]['url'], $position_of_home);
		}else{
			$configs[$name]['url'] = str_replace('/onepiece-framework/', "/{$github_account}/", $configs[$name]['url']);
		}
	}

	//	...
	return $configs;
}

/** Get home position by URL.
 *
 * @created    2022-12-05
 * @param      string          $url
 * @return     boolean|number
 */
function GetHomePosition(string $url) : int {
	//	...
	foreach(['/home/', '/Users/'] as $home){
		//	...
		if( 0 === strpos($url, $home) ){
			$pos = strlen($home);
			break;
		}
	}

	//	Was the home position detected?
	if(!($pos ?? null) ){
		echo "Home position could not be detected. ({$url})\n";
		return false;
	}

	//	Detect user name position.
	if(!($pos = strpos($url, '/', $pos)) ){
		echo "User name position could not be detected. ({$url})\n";
		return false;
	}

	//	...
	return $pos;
}

/** Output label for user.
 *
 * @created    2022-12-09
 * @param      string      $command
 */
function _Git_Do_Label_(string $command, string $target='') : void {
	//	...
	switch( trim($command) ){
		case 'stash':
		case 'stash save':
			$message = "  Save to stash.\n";
			break;

		case 'stash pop':
			$message = "  Pop from stash.\n";
			break;

		case 'fetch':
			$message = "  Fetch {$target} repository.\n";
			break;

		case 'rebase':
			$message = "  Rebase {$target} branch.\n";
			break;

		default:
			$message = "This command is not define. ($command)";
	}

	//	...
	echo $message;
}

/** Output result of git for user.
 *
 * @created    2022-12-09
 * @param      string      $result
 * @param      string      $command
 * @return     void
 */
function _Git_Result_(string $result, string $command, string $target){
	//	...
	static $_debug;
	static $_display;

	//	...
	if(!$_debug ){
		$_debug   = Request('debug')   ?? 0;
		$_display = Request('display') ?? 1;
	}

	//	...
	$lines = null;
	foreach( explode("\n", $result) as $line ){
		//	...
		if( empty($_debug) ){
			if( preg_match('|^Entering \'.+\'|', $line) ){ continue; }
			if( preg_match('|^Current branch (.+) is up to date|', $line) ){ continue; }
		}

		//	...
		switch( $command ){
			case 'fetch':
			break;
			case 'stash':
			case 'stash pop':
			case 'stash save':
				switch( $line ){
					case 'No stash entries found.':
					case 'No local changes to save':
					if( empty($_debug) ){ continue 3; }
				}
			break;
			case 'checkout':
				switch( $line ){
					case preg_match('|Switched to branch \'.+\'|', $line) ? true: false:
					if( empty($_debug) ){ continue 3; }
					case preg_match('|Switched to a new sbranch \'.+\'|', $line) ? true: false:
					if( empty($_display) and empty($_debug) ){ continue 3; }
				}
			break;
			case 'rebase':
				switch( $line ){
					case preg_match('|Your branch is up to date with \'.+\'|', $line) ? true: false:
					if( empty($_debug) ){ continue 3; }
				}
			break;
			case 'push':
			break;
			default:
				Debug("This command is not defined. ({$command})");
			break;
		}
	}

	//	...
	if( $lines ){
		echo join("\n", $lines)."\n";
	}
}
