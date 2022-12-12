<?php
/** op-cd:/0_check.php
 *
 * @created   2022-12-05
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//	Return constant value.
$_ = function($constant_name){ return $constant_name; };

//	...
$branch         = Request('branch');
$github_account = Request('username');

//	Generate help.
if( empty($branch) or empty($github_account) or empty(_WORKING_DIRECTORY_) ){
	$cmd = "php {$_(_END_POINT_)} branch=2022 workspace=/www/workspace username=YOUR_GITHUB_USER_NAME display=1 debug=0 version=82";
}

//	Empty branch name.
if(!$branch ){
	echo "Empty branch name: {$cmd}\n";
	return false;
}

//	Empty github account.
if(!$github_account ){
	echo "Empty GitHub account name: {$cmd}\n";
	return false;
}

//	Empty workspace.
if(!_WORKING_DIRECTORY_ ){
	echo "Empty workspace path: {$cmd}\n";
	return false;
}

//	Check working directory exists.
if(!file_exists(_WORKING_DIRECTORY_) ){
	//	Create working directory.
	if(!mkdir(_WORKING_DIRECTORY_, 0744, true) ){
		echo "Create working directory is failed. ({$_(_WORKING_DIRECTORY_)})\n";
		return false;
	}
}

//	...
return true;
