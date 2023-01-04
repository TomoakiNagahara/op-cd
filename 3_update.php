<?php
/** op-cd:/3_update.php
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

//	...
Display('Start repository update.');

//	Save
Git('stash save');
//	Fetch
Git('fetch',    'origin'  );
Git('fetch',    'upstream');
//	Rebase upstream to origin
Git('checkout', 'upstream/master');
Git('branch',   'upstream');
Git('switch',   'upstream');
Git('rebase',   'master'  );
//	Rebase origin include upstream
Git('switch',   'master');
Git('rebase',   'upstream');
//	Pop
Git('stash pop');

//  Successful.
return true;
