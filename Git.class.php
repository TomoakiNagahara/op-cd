<?php
/** op-cd:/Git.class.php
 *
 * @created    2023-01-02
 * @version    1.0
 * @package    op-cd
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */

/** Git
 *
 * @created    2023-01-02
 * @version    1.0
 * @package    op-cd
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */
class Git
{
	/** Return git root.
	 *
	 * @created    2023-01-02
	 * @return     string
	 */
	static function Root() : string
	{
		static $_git_root;
		if(!$_git_root ){
			$workspace = Request('workspace');
			$branch    = Request('branch');
			$_git_root = rtrim($workspace,'/').'/'.$branch.'/';
		}
		return $_git_root;
	}

	/** Get submodule config.
	 *
	 * @created    2023-01-02
	 * @param      bool        $current
	 * @throws     Exception
	 * @return     array
	 */
	static function SubmoduleConfig(bool $current) : array
	{
		//	...
		$file_name = $current ? '.gitmodules': '.gitmodules_original';
		$file_path = self::Root() . $file_name;

		//	Get submodule settings.
		if(!file_exists($file_path) ){
			throw new Exception("This file does not exist. ($file_path)");
		}

		//	Get submodule settings from file.
		if(!$file = file_get_contents($file_path) ){
			throw new Exception("Could not read this file. ($file_path)");
		}

		//	Parse submodule settings.
		$source = explode("\n", $file);

		//	Parse the submodule settings.
		$configs = [];
		while( $line = array_shift($source) ){
			//	[submodule "asset/core"]
			$name = substr($line, 12, -2);
			$name = str_replace('/', '-', $name);

			//	path, url, branch
			for($i=0; $i<3; $i++){
				list($key, $var) = explode("=", array_shift($source));
				$configs[$name][ trim($key) ] = trim($var);
			}
		}

		//	...
		return $configs;
	}

	/** Git clone
	 *
	 * @created    2023-01-02
	 */
	static function Clone()
	{
		//	...
		$workspace = Request('workspace');
		$origin    = Request('origin');
		$branch    = Request('branch');
		$git_root  = self::Root();
		$redirect  = '2>&1';

		//	Change directory to workspace.
		if(!chdir($workspace) ){
			throw new Exception("`chdir` was failed. ($workspace)");
		}

		//	...
		if(!file_exists($git_root) ){
			Display(" * `git clone {$origin} {$branch}`", false);
			if( $result  = `git clone {$origin} {$redirect}` ){
				foreach( explode("\n", $result) as $line ){
					switch( $line = trim($line) ){
						//	Discard
						case '':
							break;
						case "Cloning into '{$branch}'...":
							$stash = $line;
							break;
						case 'done.':
							$line = $stash . $line;
						//	Display
						default:
							Display($line, false);
						break;
					}
				}
			}
		}
	}

	/** Get which repository.
	 *
	 * @created    2023-01-02
	 * @param      string      $repository
	 * @throws     Exception
	 * @return     array
	 */
	static private function _WhichIs(string $repository) : array
	{
		//	...
		$origin_is = $user_name = null;

		/* @var $match array */
		if( preg_match('|^([~/].+)|', $repository, $match) ){
			//	Directory
			$origin_is = 'local';
		}else if( preg_match('|^([-a-z0-9]+:~?/[^/].+)|i', $repository, $match) ){
			//	SSH
			$origin_is = 'remote';
		}else if( preg_match('|^(https://github.com/)(.+)/|i', $repository, $match) ){
			//	http(s)
			$origin_is = 'github';
			$user_name = $match[2];
		}else{
			throw new Exception("Unmatch repository. ($repository)");
		}

		//	...
		return [$origin_is, $user_name];
	}

	/** Set orign repository.
	 *
	 * @created    2023-01-02
	 */
	static function SetOrigin()
	{
		//	Init
		$git_root = self::Root();
		$origin   = Request('origin');
		$branch   = Request('branch');
		$redirect = '2>&1';
		list($origin_is, $user_name) = self::_WhichIs($origin);

		//	Change directory to git root.
		if(!chdir($git_root) ){
			throw new Exception("`chdir` was failed. ($git_root)");
		}

		//	Change .gitmodule file.
		switch( $origin_is ){
			case 'local':
				$do = 'sh ./asset/git/submodule/local.sh';
				break;
			case 'remote':
				$do = 'sh ./asset/git/submodule/repo.sh';
				break;
			case 'github':
				$do = "sh ./asset/git/submodule/github.sh {$user_name}";
				break;
			default:
				throw new Exception("Unmatch origin is. ($origin_is)");
			break;
		}

		//	...
		Display("\n * `$do`", false);
		echo `$do 2>&1`;

		//	Init submodules.
		Display("\n * `git submodule update --init --recursive`", false);
		$result  = `git submodule update --init --recursive $redirect`;
		foreach( explode("\n", $result) as $line ){
			switch( $line = trim($line) ){
				//	Discard
				case strpos(' '.$line, 'Cloning into ') ? true: false:
					$stash = $line;
				case '':
					break;
				case "done.":
					$line = $stash . $line;
				//	Display
				default:
					Display($line, false);
				break;
			}
		}

		//	Checkout submodules
		Display("\n * Do submodule configuration.", false);
		foreach( self::SubmoduleConfig(true) as $key => $config ){
			//	...
			Display(" - {$key} : {$config['path']}", false);

			//	...
			if(!chdir( $git_root . $config['path'] ) ){
				throw new Exception("Change directory was failed. ({$git_root}{$config['path']})");
			}

			//	Checkout
			Display("  `git checkout {$branch}", false);
			$result  = `git checkout {$branch} $redirect`;
			foreach( explode("\n", $result) as $line ){
				switch( $line = trim($line) ){
					//	Discard
					case '':
					case "Switched to a new branch '{$branch}'":
					case "Branch '{$branch}' set up to track remote branch '{$branch}' from 'origin'.":
						Display('   '.$line, false);
						break;
					//	Display
					default:
						Display($line, false);
					break;
				}
			}
		}
	}

	/** Set upstream repository.
	 *
	 * @created    2023-01-02
	 */
	static function SetUpstream()
	{
		//	Init
		$git_root = self::Root();
		$origin   = Request('origin');
		$upstream = Request('upstream');
		$redirect = '2>&1';
		list($orign_is,    $user_name) = self::_WhichIs($origin);
		list($upstream_is, $user_name) = self::_WhichIs($upstream);

		//	Change directory to git root.
		if(!chdir($git_root) ){
			throw new Exception("Change directory was failed. ($git_root)");
		}

		//	main
		Display(' * Set upstream URL to main repository.', false);
		if( $result = `git config --get remote.upstream.url $redirect` ){
			Display(" - Already set upstream URL: $result", false);
		}else{
			Display("  `git remote add upstream {$upstream}`", false);
			echo `git remote add upstream {$upstream}  $redirect`;
		}
		echo "\n";

		//	submodules
		Display(' * Set upstream URL to submodules.', false);
		$configs_current  = self::SubmoduleConfig(true);
		$configs_original = self::SubmoduleConfig(false);
		foreach( $configs_current as $key => $config ){
			//	...
			$current  = $configs_current[$key]['url'];
			$original = $configs_original[$key]['url'];
			$upstream = self::_CalcRepository($current, $original, $orign_is, $upstream_is, $user_name);

			//	Change submodule directory.
			chdir($git_root . $config['path']);

			//	Submodule.
			Display(" - {$key}: {$config['path']}", false);
			if( $result = `git config --get remote.upstream.url` ){
				Display(" - Already set upstream URL: $result", false);
			}else{
				Display("  `git remote add upstream {$upstream}`", false);
				echo `git remote add upstream {$upstream}`;
			}
		}
		echo "\n";
	}

	/** Calc repository path or URL.
	 *
	 * @created    2023-01-02
	 * @param      string      $current
	 * @param      string      $original
	 * @param      string      $orign_is
	 * @param      string      $upstream_is
	 * @param      string      $user_name
	 * @return     string
	 */
	static private function _CalcRepository(string $current, string $original, string $orign_is, string $upstream_is, ?string $user_name) : string
	{
		//	...
		$repository = null;

		//	...
		switch( $upstream_is ){
			case 'local':
				break;
			case 'remote':
				if( $orign_is === 'local' ){
					if( preg_match('|^(/\w+/\w+/)|', $current, $match) ){
						$len = strlen($match[0]);
						$str = substr($current, $len);
						$repository = 'repo:~/'.$str;
					}else{
						Debug("Unmatch current: {$current}");
					}
				}else{
					Debug("Unmatch origin_is: {$orign_is}");
				}
				break;
			case 'github':
				$repository = str_replace('/onepiece-framework/', "/{$user_name}/", $original);
				break;
			default:
		}

		//	...
		return $repository;
	}
}
