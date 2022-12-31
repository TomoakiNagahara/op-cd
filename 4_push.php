<?php
/** op-cd:/4_push.php
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
Git('push', 'origin/master'     );
Git('push', "origin/{$branch}"  );
Git('push', 'upstream/master'   );
Git('push', "upstream/{$branch}");

/* @var $php_version integer */
if( $php_version ?? null ){
	Git('push', "origin/php{$php_version}"  );
	Git('push', "upstream/php{$php_version}");
}

/*
`git push --recurse-submodules=on-demand`
*/

//	Successful.
return true;
