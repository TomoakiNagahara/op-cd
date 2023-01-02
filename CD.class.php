<?php
/** op-cd:/CD.class.php
 *
 * @created    2023-01-02
 * @version    1.0
 * @package    op-cd
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */

/** Continuous Delivery
 *
 * @created    2023-01-02
 * @version    1.0
 * @package    op-cd
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */
class CD
{
	/** Git root.
	 *
	 * .git is located.
	 *
	 * @var string
	 */
	static $_git_root;

	/** Application root.
	 *
	 * app.php is located.
	 *
	 * @var string
	 */
	static $_app_root;

	/** Auto
	 *
	 * @created    2023-01-02
	 */
	static function Auto(){
		//	...
		self::Init();
		self::Clone();
		self::Fetch();
		self::Rebase();
		self::CI();
		self::Push();

		//	...
		Display(" * All inspection is complete.");

		//	...
		return true;
	}

	/** Init
	 *
	 * @created    2023-01-02
	 */
	static function Init()
	{
		//	...
		foreach(['workspace','branch','upstream','origin'] as $key ){
			if(!${$key} = Request($key) ){
				throw new Exception("Arguments error, {$key} is not set. Please read README.md.");
			}
		}

		/* @var $workspace string */
		/* @var $branch    string */
		self::$_app_root  = rtrim($workspace,'/').'/'.$branch.'/';
	}

	/** Clone
	 *
	 * @created    2023-01-02
	 */
	static function Clone()
	{
		//	Check if already cloned.
		if( file_exists(self::$_app_root) ){
			//	Already cloned.
			return;
		}

		//	Check if workspace exists.
		$workspace = Request('workspace');
		if(!file_exists($workspace) ){
			//	...
			if( `mkdir {$workspace}` ){
				throw new Exception("mkdir failed. ($workspace)");
			}
		}

		//	...
		if(!chdir($workspace) ){
			throw new Exception("chdir failed. ($workspace)");
		}

		//	Do clone.
		Git::Clone();
	}

	/** Fetch
	 *
	 * @created    2023-01-02
	 */
	static function Fetch()
	{

	}

	/** Rebase
	 *
	 * @created    2023-01-02
	 */
	static function Rebase()
	{

	}

	/** CI
	 *
	 * @created    2023-01-02
	 */
	static function CI()
	{

	}

	/** Push
	 *
	 * @created    2023-01-02
	 */
	static function Push()
	{

	}
}
