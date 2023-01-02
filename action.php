<?php
/** op-cd:/action.php
 *
 * @created    2023-01-02
 * @version    1.0
 * @package    op-cd
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */

//	...
error_reporting(E_ALL);
ini_set('short_open_tag', 1);
ini_set('display_errors', 1);
ini_set('log_errors'    , 0);

//	...
chdir(__DIR__);

//	...
require('Error.php');
require('Debug.php');
require('Display.php');
require('9_request.php');
require('CD.class.php');
require('Git.class.php');

//	...
$exit = CD::Auto() ? 0: 1;

//	...
exit($exit);
