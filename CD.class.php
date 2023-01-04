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
		self::Update();
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
				throw new Exception("This arguments is not set ({$key}). Please read README.md.");
			}
		}

		/* @var $workspace string */
		/* @var $branch    string */
		self::$_git_root  = rtrim($workspace,'/').'/'.$branch.'/';
	}

	/** Clone
	 *
	 * @created    2023-01-02
	 */
	static function Clone()
	{
		//	Check if already cloned.
		if( file_exists(self::$_git_root) ){
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

		//	...
		Git::Clone();
		Git::SetOrigin();
		Git::SetUpstream();
	}

	/** Fetch
	 *
	 * @created    2023-01-02
	 */
	static function Update()
	{
		//	Change git root directory.
		if(!chdir(self::$_git_root) ){
			throw new Exception('Change directory was failed.('.self::$_git_root.')');
		}

		/* @var $output string  */
		/* @var $status integer */
		exec("sh asset/git/submodule/update.sh", $output, $status);
		foreach( $output as $line ){
			echo $line;
		}
		echo "\n";
	}

	/** CI
	 *
	 * @created    2023-01-02
	 */
	static function CI()
	{
		//	Change git root directory.
		if(!chdir(self::$_git_root) ){
			throw new Exception('Change directory was failed.('.self::$_git_root.')');
		}

		//	...
		$display   = Request('display');
		$debug     = Request('debug');
		$args[]    = "display={$display}";
		$args[]    = "debug={$debug}";
		$args      = join(' ', $args);

		/* @var $output string  */
		/* @var $status integer */
		exec("php ci.php $args", $output, $status);
		foreach( $output as $line ){
			echo $line;
		}
		echo "\n";

		//	...
		if( $status ){
			throw new Exception("ci.php is failed.");
		}
	}

	/** Push
	 *
	 * @created    2023-01-02
	 */
	static function Push()
	{
		//	Change git root directory.
		if(!chdir(self::$_git_root) ){
			throw new Exception('Change directory was failed.('.self::$_git_root.')');
		}

		/* @var $output string  */
		/* @var $status integer */
		$branch    = Request('branch');

		//	Main
		Display(' * git push upstream master');
		exec("git push upstream master", $output, $status);
		foreach( $output as $line ){
			echo $line;
		}
		echo "\n";

		//	Submodules.
		Display(" * sh asset/git/submodule/push.sh upstream {$branch}");
		exec("sh asset/git/submodule/push.sh upstream {$branch}", $output, $status);
		foreach( $output as $line ){
			echo $line;
		}
		echo "\n";
	}
}
