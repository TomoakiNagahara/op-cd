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

//	Include functions.
require_once('9_d.php');
require_once('9_git.php');
require_once('9_debug.php');
require_once('9_display.php');
require_once('9_request.php');

//  Get arguments.
$branch            = Request('branch')   ?? null;
$github_account    = Request('username') ?? null;
$display           = Request('display')  ?? true;

//	Set constant.
define('_WORKING_DIRECTORY_', '/www/workspace/'                  );
define('_APP_ROOT_'         , _WORKING_DIRECTORY_ .$branch . '/' );
define('_REPOSITORY_PATH_'  , "~/repo/op/skeleton/{$branch}.git" );
define('_END_POINT_'        , basename($_SERVER['argv'][0])      );

//	Maintain compatibility.
$working_directory = _WORKING_DIRECTORY_;
$app_root          = _APP_ROOT_;
$repository_path   = _REPOSITORY_PATH_;
$base_file_name    = _END_POINT_;

//	Check arguments error.
if(!include('0_check.php') ){
	exit(__LINE__); // git diff is fool.
};

//  Checking directory exists.
if(!file_exists(_APP_ROOT_) ){
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
D($github_account, $app_root, $repository_path, $base_file_name, $display, $working_directory);
