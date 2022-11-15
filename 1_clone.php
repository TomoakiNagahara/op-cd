<?php
/** git clone repository
 * 
 * @created   2022-11-13
 * @version   1.0
 * @package   op-ci
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//	
if(!file_exists($working_directory) ){
	//	Create working directory.
	if(!mkdir($working_directory, recursive: true) ){
		return false;
	}
}

//	Change directory.
if(!chdir($working_directory) ){
	exit(__LINE__);
}

//	Checking repository exists.
if(!file_exists($branch) ){
	//  Clone repository.
	`git clone {$repository_path}`;
}

//	Checking git clone success.
if(!file_exists($branch . '/.git') ){
	return false;
}

//	Added upstream repository.
`git remote add upstream https://github.com/TomoakiNagahara/op-app-skeleton-2022-nep.git`;

//	Successful.
return true;
