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

/* @var $working_directory string */
/* @var $repository_path   string */
/* @var $branch            string */

//	Change directory.
if(!chdir($working_directory) ){
	echo "Change directory is failed. ($working_directory)\n";
	return false;
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
