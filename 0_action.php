<?php
/** main action
 * 
 * @created   2022-11-11
 * @version   1.0
 * @package   op-ci
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//  Init
$branch            = $_SERVER['argv'][1] ?? null;
$github_account    = $_SERVER['argv'][2] ?? null;
$working_directory = '/www/workspace/';
$repository_path   = "~/repo/op/skeleton/{$branch}.git";
$commit_id_file    = '_commit_id';
$base_file_name    = basename($_SERVER['argv'][0]);

//	Empty branch name.
if(!$branch ){
	$cmd = 'php '.$base_file_name.' 2022';
	echo "Empty branch name. Example: {$cmd}".PHP_EOL;
	exit(__LINE__);
}//	Empty branch name.

//	Empty github account.
if(!$github_account ){
	$cmd = 'php '.$base_file_name.' 2022 your_github_account';
	echo "Empty branch name. Example: {$cmd}".PHP_EOL;
	exit(__LINE__);
}//	Empty github account.

//  Checking directory exists.
if(!$cloned = file_exists($working_directory.$branch) ){
	//	Execute clone.
	if(!include('1_clone.php') ){
		exit(__LINE__);
	}
}

//	If already cloned.
if( $cloned ){
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
if(!chdir($working_directory.$branch) ){
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
