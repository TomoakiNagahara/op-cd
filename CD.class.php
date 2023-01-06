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

	/** Change directory.
	 *
	 * @created    2023-01-02
	 * @param      string      $path
	 * @throws     Exception
	 */
	static function ChangeDir(string $path='')
	{
	//	Debug(__METHOD__."({$path})", false);

		//	...
		if( empty(self::$_git_root) ){
			self::Init();
		}

		//	...
		$path = self::$_git_root . $path;

		//	...
		if(!chdir($path)){
			throw new Exception("Change directory failed. ($path)");
		}
	}

	/** Auto
	 *
	 * @created    2023-01-02
	 */
	static function Auto(){
		//	...
		self::Init();
		self::Clone();
		self::Update();

		//	...
		foreach(['', 81] as $version){
			//	Switch branches main and submodules.
			if( $version ){
				Debug(" * PHP{$version}");
				$main = "php{$version}";
				$sub  = "php{$version}";
			}else{
				$main = 'master';
				$sub  = Request('branch');
			}

			//	main
			self::ChangeDir();
			Git::Checkout($main);

			//	submodules
			foreach( Git::SubmoduleConfig(true) as $config ){
				self::ChangeDir($config['path']);
				Git::Checkout($sub);
			}

			//	CI
			if( self::CI($version) ){
				self::Push();
			}
		}

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
		self::ChangeDir();

		//	...
		$branch = Request('branch');

		//	Main
		Display(" * Update main repository");
		Git::Fetch('origin');
		Git::Save();
		Git::Switch('master');
		Git::Rebase('origin/master');
		Git::Pop();

		//	Submodule
		Display(" * Update submodule repository");
		foreach( Git::SubmoduleConfig(true) as $key => $config) {
			Display(" - {$key} : {$branch}");

			//	...
			self::ChangeDir($config['path']);

			//	...
			Git::Fetch('origin');
			Git::Save();
			Git::Switch($config['branch']);
			Git::Rebase("origin/{$config['branch']}");
			Git::Pop();
		}

		/*
		//	Change git root directory.
		if(!chdir(self::$_git_root) ){
			throw new Exception('Change directory was failed.('.self::$_git_root.')');
		}

		$output = null;
		$status = null;
		exec("sh asset/git/submodule/update.sh", $output, $status);
		foreach( $output as $line ){
			echo $line;
		}
		echo "\n";
		*/
	}

	/** CI
	 *
	 * @created    2023-01-02
	 */
	static function CI(string $version) : bool
	{
		//	Change git root directory.
		self::ChangeDir();

		//	...
		$display   = Request('display');
		$debug     = Request('debug');
		$args[]    = "display={$display}";
		$args[]    = "debug={$debug}";
		$args      = join(' ', $args);

		$output = null;
		$status = null;
		exec("php{$version} ci.php $args", $output, $status);
		foreach( $output as $line ){
			echo $line;
		}
		echo "\n";

		//	...
		if( $status ){
			Display(" ! ci.php is failed.");
		}

		//	...
		return $status ? false: true;
	}

	/** Push
	 *
	 * @created    2023-01-02
	 */
	static function Push(?string $branch=null)
	{
		//	Change git root directory.
		self::ChangeDir();

		//	Main
		Display(" * Push main repository");
		Git::Push('upstream', $branch ?? 'master');

		//	Submodule
		Display(" * Push submodule repository");
		foreach( Git::SubmoduleConfig(true) as $key => $config) {
			//	...
			Display(" - {$key}, {$config['path']}");

			//	...
			self::ChangeDir($config['path']);

			//	...
			Git::Push('upstream', $branch ?? $config['branch']);
		}

		/*
		//	...
		$output = null;
		$status = null;
		$branch = Request('branch');

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
		*/
	}
}
