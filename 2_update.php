<?php
/** git update repository
 * 
 * @created   2022-11-13
 * @version   1.0
 * @package   op-ci
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//  Rebase
`git fetch`;
`git rebase origin/master`;

//  Fetch submodules.
`git submodule foreach git fetch    origin`;
`git submodule foreach git checkout        2022`;
`git submodule foreach git rebase   origin/2022`;

//  Result.
return true;
