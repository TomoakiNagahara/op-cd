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

