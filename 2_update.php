<?php
/** git update repository
 * 
 * @created   2022-11-13
 * @version   1.0
 * @package   op-ci
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

 //	Change directory.
if(!chdir($working_directory.$branch) ){
	echo "Change directory failed - {$working_directory}{$branch} \n";
	return false;
}

//  Rebase  origin.
`git fetch  origin`;
`git rebase origin/master`;

//	Rebase  upstream.
`git fetch  upstream`;
`git rebase upstream/master`;

//  Rebase submodules origin.
`git submodule foreach git fetch    origin`;
`git submodule foreach git checkout        2022`;
`git submodule foreach git rebase   origin/2022`;

//  Rebase submodules upstream.
`git submodule foreach git fetch    upstream`;
`git submodule foreach git checkout          2022`;
`git submodule foreach git rebase   upstream/2022`;

//  Successful.
return true;
