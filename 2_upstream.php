<?php
/** git upstream repository
 *
 * Add remote upstream repository.
 *
 * @created   2022-11-13
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/* @var  $github_account  string */
/* @var  $app_root        string */
/* @var  $branch          string */

//	To clarify current directory.
if(!chdir($app_root) ){
	echo "Change directory is failed. ($app_root)\n";
	return false;
}

//	Base URL.
$base = 'https://github.com/'.$github_account.'/';

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
	//	Generate path.
	$path = $app_root.'/asset/'.$dir;

	//	Check directory exists.
	if(!file_exists($path) ){
		echo "Does not exists directory. ({$path})\n";
		continue;
	}

	//	Reset directory.
	if( !chdir($path) ){
		echo "Failed change directory. ({$path})\n";
		continue;
	}

	//	Get targets.
	foreach( glob("*", GLOB_ONLYDIR) as $name ){
		//	Change target directory.
		if(!chdir($app_root.'/asset/'.$dir.'/'.$name) ){
			continue;
		}

		//	Add upstream repository.
		$upstream = $base."op-{$dir}-{$name}.git";
		echo "\n";
		echo " Add upstream: \n";
		echo "  ".getcwd() ."\n";
		echo "  ".$upstream."\n";
		`git remote add upstream {$upstream}`;
	}
}
echo "\n";

//	Fetch upstream
chdir($app_root);
`git fetch upstream`;
`git submodule foreach git fetch upstream`;

/*
//	Rebase upstream
`git stash save`;
`git rebase upstream/master`;
`git stash pop`;
*/

return true;
