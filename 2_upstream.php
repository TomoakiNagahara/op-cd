<?php
/** git upstream repository
 * 
 * @created   2022-11-13
 * @version   1.0
 * @package   op-ci
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//	Base URL.
$base = 'https://github.com/'.$github_account.'/';

//	Change app root directory.
chdir($working_directory.$branch);

//	app skeleton
$upstream = $base.'op-app-skeleton-'.$branch.'-nep.git';
echo "\n Add upstream URL - {$upstream} \n";
`git remote add upstream {$upstream}`;

//	WebPack
chdir('webpack');
$upstream = $base.'op-module-webpack.git';
echo "\n Add upstream URL - {$upstream} \n";
`git remote add upstream {$upstream}`;

//	core
chdir('../asset/core');
$upstream = $base.'op-core.git';
echo "\n Add upstream URL - {$upstream} \n";
`git remote add upstream {$upstream}`;

//	develop, testcase, reference
foreach(['develop', 'testcase', 'reference'] as $name){
	chdir("../{$name}");
	$upstream = $base."op-module-{$name}.git";
	echo "\n Add upstream URL - {$upstream} \n";
	`git remote add upstream {$upstream}`;
	
}

//	Layout, Unit, WebPack
foreach(['layout', 'unit', 'webpack'] as $dir){	
	//	Reset directory.
	if( file_exists($path = $working_directory.$branch.'/asset/'.$dir) and !chdir($path) ){
		echo "Failed change directory. ({$path})\n";
		continue;
	}

	//	Get targets.
	foreach( glob("*", GLOB_ONLYDIR) as $name ){
		//	Change target directory.
		if(!chdir($working_directory.$branch.'/asset/'.$dir.'/'.$name) ){
			continue;
		}
		//	Add upstream repository.
		$upstream = $base."op-{$dir}-{$name}.git";
		echo "\n Add upstream URL - {$upstream} \n";
		`git remote add upstream {$upstream}`;
	}
} 

//	Fetch upstream
chdir($working_directory.$branch);
var_dump(getcwd());
`git fetch upstream`;
`git submodule foreach git fetch upstream`;

/*
//	Rebase upstream
`git stash save`;
`git rebase upstream/master`;
`git stash pop`;
*/

return true;
