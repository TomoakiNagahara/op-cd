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
$commit_id_file    = '_commit_id';
$base_file_name    = basename($_SERVER['argv'][0]);

//	Check arguments error.
if(!include('0_check.php') ){
	exit(__LINE__); // git diff is fool.
};

//  Checking directory exists.
if(!$exists = file_exists($app_root) ){
	//	Execute clone.
	if(!include('1_clone.php') ){
		exit(__LINE__);
	}
}

//	If already cloned.
if( $exists ){
	//  Git update.
	if(!include('2_update.php') ){
		exit(__LINE__);
	}
}else{
	//	Add upstream repository.
	if(!include('2_upstream.php') ){
		return false;
	}
}

//  Change directory.
if(!chdir($app_root) ){
	exit(__LINE__);
}

//  Get commit id. <-- app-skeleton only
$commit_id = `git show --format='%h' --no-patch`;

//	Check commit id file exists.
if( file_exists($commit_id_file) ){
	//  Checking last commit id.
	if( $commit_id === file_get_contents($commit_id_file) ){
		//  Not fixed.
		exit(0);
	}
}else{
	//  Create commit id file.
	if(!touch($commit_id_file) ){
		exit(__LINE__);
	}
}

//  Execute ci.php
if( $result = `php ci.php display=1` ){
	//	If result is error code.
	if( strpos($result, "0\n") === 0 ){
		ExecuteCode( explode("\n", $result) );
	}else{
		echo $result;
	}
}

//	Save evaluated commit id.
if(!file_put_contents($commit_id_file, $commit_id, LOCK_EX) ){
	exit(__LINE__);
}

//	Push git repository to upstream.
include('3_push.php');

//	Finished
exit(0);

/** Execute code.
 * @created    2022-11-11
 */
function ExecuteCode($codes){
	//	...
	foreach( $codes as $code ){
		//	...
		if(!$code ){
			continue;
		}
		//	...
		echo $code.PHP_EOL;
		echo `$code`;
	}
}
