<?php
/** Execute CI
 *
 * @created   2022-12-06
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/* @var $app_root string */

//	To clarify current directory.
if(!chdir($app_root) ){
	echo "Change directory is failed. ($app_root)\n";
	return false;
}

//	Execute each PHP version.
foreach(['', 70 /*, 71, 72, 73, 74, 80, 81*/] as $php_version){ // Strict types are inconvenient.
	//	...
	if( ExecuteCI($php_version) ){
		//	Push git repository to upstream.
		include('3_push.php');
	};
};

//	Successful.
return true;

/** Execute CI.
 *
 * @created    2022-12-01
 * @param      string     $php_version
 * @return     boolean
 */
function ExecuteCI(string $php_version) : bool {
	//	If php version is specified.
	$branch = ($php_version === '') ? 'master': 'php'.$php_version;

	//	Get branch list.
	$branch_list = `git branch`;

	//	Check if branch exists.
	if( strpos($branch_list, $branch) === false ){
		echo "This branch is not exists. ($branch)\n";
		return false;
	}

	//	Switch branch.
	$result = `git switch {$branch} 2>/dev/null`;

	//	Saved commit id file name.
	$commit_id_file = '.op-cd_commit-id.php'.$php_version;

	//  Get commit id. <-- app-skeleton only
	$commit_id = `git show --format='%h' --no-patch`;

	//	Check commit id file exists.
	if( file_exists($commit_id_file) ){
		//  Checking last commit id.
		if( $commit_id === file_get_contents($commit_id_file) ){
			//  Not fixed.
			return true;
		}
	}else{
		//  Create commit id file.
		if(!touch($commit_id_file) ){
			echo "Failed touch({$commit_id_file}) command #".__LINE__;
			return false;
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
		echo "Failed file_put_contents({$commit_id_file}) command #".__LINE__;
		return false;
	}

	//	Return result.
	return true;
}

/** Execute code.
 *
 * @created    2022-11-11
 */
function ExecuteCode($codes) : void {
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

