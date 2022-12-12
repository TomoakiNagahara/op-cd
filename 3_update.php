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

//	...
Display('Start repository update.');

//	...
Git('stash save');
Git('fetch',  'origin'  );
Git('fetch',  'upstream');
Git('rebase', 'upstream/master');
Git('rebase', 'origin/master'  );
Git('rebase', 'upstream/2022'  );
Git('rebase', 'origin/2022'    );
Git('stash pop');

//  Successful.
return true;
