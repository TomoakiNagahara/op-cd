<?php
/** git update repository
 *
 * @created   2022-11-13
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//	Return constant value.
$_ = function($constant_name){ return $constant_name; };

//	To clarify current directory.
if(!chdir(_APP_ROOT_) ){
	echo "Change directory failed - {$_(_APP_ROOT_)} \n";
	return false;
}

//	Start
`git stash  save`;

//  Rebase  origin.
`git fetch  origin`;
`git rebase origin/master`;

//	Rebase  upstream.
`git fetch  upstream`;
`git rebase upstream/master`;

//	Finish
`git stash  pop`;

//  Rebase submodules origin.
`git submodule foreach git fetch    origin`;
`git submodule foreach git rebase   origin/2022`;

//  Rebase submodules upstream.
`git submodule foreach git fetch    upstream`;
`git submodule foreach git rebase   upstream/2022`;

//  Successful.
return true;
