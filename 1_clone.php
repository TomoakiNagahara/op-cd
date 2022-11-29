<?php
/** git clone repository
 * 
 * @created   2022-11-13
 * @version   1.0
 * @package   op-ci
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//	Check working directory exists.
if(!file_exists($working_directory) ){
	//	Create working directory.
	echo "\n Create working directory - {$working_directory} \n";
	if(!mkdir($working_directory, recursive: true) ){
		return false;
	}
}

//	Change directory.
if(!chdir($working_directory) ){
	exit(__LINE__);
}

//	Check repository exists.
if(!file_exists($branch) ){
	//  Clone repository.
	echo "\n Clone git repository - {$repository_path} \n\n";
	`git clone -b master {$repository_path}`;
}

//	Check git clone success.
if(!file_exists($branch . '/.git') ){
	return false;
}

//	Change directory to git repository.
echo "\n Change directory - {$working_directory}{$branch} \n";
if(!chdir($working_directory.$branch) ){
	echo "Change directory failed - {$working_directory}{$branch} \n";
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
