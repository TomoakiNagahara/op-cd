<?php
/** op-cd:/0_ci.php
 *
 * @created   2022-12-06
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//	Return constant value.
$_ = function($constant_name){ return $constant_name; };

//	To clarify current directory.
if(!chdir(_APP_ROOT_) ){
	echo "Change directory is failed. ({$_(_APP_ROOT_)})\n";
	return false;
}

//	Specify the PHP version.
if( $php_version = Request('version') ){
	//	Check positive integer. A string is also possible.
	if( is_int($php_version) or ctype_digit($php_version) ){
		$version_list[] = $php_version;
	}else{
		echo "This value is not positive integer. ($php_version)\n";
		return false;
	}
}else{
	//	...
	$version_list   = ['', 81]; // 70, 71, 72, 73, 74, 80, 81, 82
}

//	Execute each PHP version.
foreach( $version_list as $php_version){
	//	...
	if( ExecuteCI($php_version) ){
		//	Push git repository to upstream.
		include('4_push.php');
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
	//	Check if php installed.
	if(!`command -v php{$php_version}` ){
		echo "This version of PHP is not installed. ($php_version)\n";
		return false;
	}

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
	if(!GitBranch($branch) ){
		return false;
	}

	//	If that commit has already been tested.
	if( CheckCommitID($php_version) ){
		return false;
	}

	//  Execute ci.php
	if( $result = `php{$php_version} ci.php display=1` ){
		//	If result is error code.
		if( strpos($result, "0\n") === 0 ){
			ExecuteCode( explode("\n", $result) );
		}else{
			echo $result;
		}
	}

	//	Save evaluated commit id.
	if(!SaveCommitID($php_version) ){
		return false;
	}

	//	Return result.
	return true;
}

/** Execute code.
 *
 * @created    2022-11-11
 * @param      array       $codes
 */
function ExecuteCode(array $codes) : void {
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

/** Check if already tested by saved commit id.
 *
 * @created    2022-12-06
 * @param      string      $php_version
 * @return     boolean
 */
function CheckCommitID(string $php_version) : bool {
	//	Get commit id file name.
	$commit_id_file = GetCommitIdFileName($php_version);

	//  Get commit id. <-- app-skeleton only
	$commit_id = `git show --format='%h' --no-patch`;

	//	Check if commit id file exists.
	if(!file_exists($commit_id_file) ){
		//  Create commit id file.
		if(!touch($commit_id_file) ){
			echo "Failed touch({$commit_id_file}) command #".__LINE__;
		}
		return false;
	}

	//  Checking last commit id.
	if( $commit_id !== file_get_contents($commit_id_file) ){
		//  Not tested.
		return false;
	}

	//	...
	if( Request('display') ?? true ){
		$branch = $php_version ? 'PHP'.$php_version: 'master';
		echo "This branch is Already tested. ({$branch})\n\n";
	}

	//	...
	return true;
}

/**
 *
 * @created    2022-12-06
 * @param      string      $php_version
 * @return     boolean
 */
function SaveCommitID(string $php_version) : bool {
	//	...
	$commit_id_file = GetCommitIdFileName($php_version);

	//  Get commit id. <-- app-skeleton only
	$commit_id = `git show --format='%h' --no-patch`;

	//	...
	if(!file_put_contents($commit_id_file, $commit_id, LOCK_EX) ){
		echo "Failed file_put_contents({$commit_id_file}) command #".__LINE__;
		return false;
	}

	//	...
	return true;
}

/** Generate a file name of save the commit ID.
 *
 * @created    2022-12-06
 * @param      string      $php_version
 * @return     string
 */
function GetCommitIdFileName(string $php_version) : string {
	return '.op-cd_commit-id.php'.$php_version;
}
