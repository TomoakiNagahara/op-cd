<?php
/** git push repository
 *
 * @created   2022-11-15
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//	Return constant value.
$_ = function($constant_name){ return $constant_name; };

//	...
if(!$branch = Request('branch') ){
	echo "Empty branch name\n";
	return false;
}

//	To clarify current directory.
if(!chdir(_APP_ROOT_) ){
	echo "Change directory is failed. ($_(_APP_ROOT_))\n";
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
