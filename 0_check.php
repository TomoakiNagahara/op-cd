<?php
/** Arguments error check.
 *
 * @created   2022-12-05
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/* @var $branch            string */
/* @var $base_file_name    string */
/* @var $github_account    string */
/* @var $working_directory string */

//	Empty branch name.
if(!$branch ){
	$cmd = "php {$base_file_name} [2022]";
	echo "Empty branch name: {$cmd}\n";
	return false;
}

//	Empty github account.
if(!$github_account ){
	$cmd = "php {$base_file_name} {$branch} [GITHUB PUSH ACCOUNT]";
	echo "Empty GitHub account name: {$cmd}\n";
	return false;
}

//	Check working directory exists.
if(!file_exists($working_directory) ){
	//	Create working directory.
	if(!mkdir($working_directory, 0744, true) ){
		echo "Create working directory is failed. ({$working_directory})\n";
		return false;
	}
}

//	...
return true;
