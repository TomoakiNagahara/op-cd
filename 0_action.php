<?php
/** main action
 *
 * # RULES
 *
 * `exit()` is only this file.
 * The reason is that you will not know where `exit()`.
 * Included files always return a boolean value.
 * Please output the error message by include files.
 *
 * @created   2022-11-11
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//  Init
$branch            = $_SERVER['argv'][1] ?? null;
$github_account    = $_SERVER['argv'][2] ?? null;
$working_directory = '/www/workspace/';
$app_root          = $working_directory . $branch . '/';
$repository_path   = "~/repo/op/skeleton/{$branch}.git";
$base_file_name    = basename($_SERVER['argv'][0]);

//	Check arguments error.
if(!include('0_check.php') ){
	exit(__LINE__); // git diff is fool.
};

//  Checking directory exists.
if(!file_exists($app_root) ){
	//	Do clone.
	if(!include('1_clone.php') ){
		exit(__LINE__);
	}

	//	Add upstream repository.
	if(!include('2_upstream.php') ){
		exit(__LINE__);
	}
}

//	Git update.
if(!include('3_update.php') ){
	exit(__LINE__);
}

//	Execute CI.
if(!include('0_ci.php') ){
	exit(__LINE__);
}

//	Finished
exit(0);

//	Eclipse Notice
if( false ){
	D($github_account, $app_root, $repository_path, $base_file_name);
}
