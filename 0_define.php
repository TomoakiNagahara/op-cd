<?php
/** op-cd:/0_define.php
 *h
 * @created   2022-12-13
 * @version   1.0
 * @package   op-cd
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

//	...
(function(){
	//  Get arguments.
	$branch    = Request('branch')    ?? null;
	$workspace = Request('workspace') ?? null;

	//	...
	if( $workspace ){
		$workspace = rtrim($workspace, '/').'/';
	}

	//	Set constant.
	define('_WORKING_DIRECTORY_', $workspace                         );
	define('_APP_ROOT_'         , _WORKING_DIRECTORY_ .$branch . '/' );
	define('_REPOSITORY_PATH_'  , "~/repo/op/skeleton/{$branch}.git" );
	define('_END_POINT_'        , basename($_SERVER['argv'][0])      );
	define('_HOME_POSITION_'    , strlen(`realpath ~/`) -1           );
})();
