<?php
/** git push repository
 *
 * @created   2022-11-15
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//	...
$branch = Request('branch');

/* @var $app_root string */

//	To clarify current directory.
if(!chdir($app_root) ){
	echo "Change directory is failed. ($app_root)\n";
	return false;
}

//	Do the push.
`git push upstream master`;
`git submodule foreach git push upstream {$branch}`;
/*
`git push --recurse-submodules=on-demand`
*/

//	Successful.
return true;
