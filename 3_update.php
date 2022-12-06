<?php
/** git update repository
 *
 * @created   2022-11-13
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/* @var $app_root string */

//	To clarify current directory.
if(!chdir($app_root) ){
	echo "Change directory failed - {$app_root} \n";
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
