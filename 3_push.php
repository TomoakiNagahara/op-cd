<?php
/** git push repository
 * 
 * @created   2022-11-15
 * @version   1.0
 * @package   op-ci
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//	To clarify current directory.
chdir($working_directory.$branch);

//	Do the push.
`git push upstream master`;
`git submodule foreach git push upstream {$branch}`;
/*
`git push --recurse-submodules=on-demand`
*/

//	Successful.
return true;
