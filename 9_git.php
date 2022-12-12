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
	if(!$source = explode("\n", file_get_contents($file_name)) ){
		echo "Could not read .gitmodules. #".__LINE__;
		return false;
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
			$configs[$name]['url'] = 'repo:~'.substr($configs[$name]['url'], _HOME_POSITION_);
		}else{
			$configs[$name]['url'] = str_replace('/onepiece-framework/', $github_account, $configs[$name]['url']);
		}
	}

	//	...
	return $configs;
}

