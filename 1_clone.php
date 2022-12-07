<?php
/** git clone repository
 *
 * Clone repository from local repository.
 * And, Submodule too.
 *
 * @created   2022-11-13
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//	Return constant value.
$_ = function($constant_name){ return $constant_name; };

//	...
$branch = Request('branch');

/* @var $working_directory string */
/* @var $repository_path   string */

//	To clarify current directory.
if(!chdir(_WORKING_DIRECTORY_) ){
	echo "Change directory is failed. ({$_(_WORKING_DIRECTORY_)})\n";
	return false;
}

//	Check repository exists.
if(!file_exists($branch) ){
	//  Clone repository.
	echo "\n Clone git repository - {$_(_REPOSITORY_PATH_)} \n\n";
	`git clone -b master {$_(_REPOSITORY_PATH_)}`;
}

//	Check git clone success.
if(!file_exists($branch . '/.git') ){
	return false;
}

//	Change directory to git repository.
echo "\n Change directory - {$_(_APP_ROOT_)} \n";
if(!chdir(_APP_ROOT_) ){
	echo "Change directory failed - {$_(_APP_ROOT_)} \n";
	return false;
}

//	Change .gitmodules. https://github.com/onepiece-framework/op-core.git --> ~/repo/op/core.git
echo "\n Overwrite .gitmodules \n";
`sh ./asset/git/submodule/local.sh`;

//	Checkout submodules.
echo "\n git checkout submodules \n\n";
`git submodule update --init --recursive`;
`git submodule foreach git checkout {$branch}`;

//	Successful.
return true;
