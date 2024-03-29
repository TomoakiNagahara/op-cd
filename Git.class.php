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
			$directory = Request('config');
			$directory = basename($directory);
			$directory = substr($directory , 0, -4);
			$_git_root = rtrim($workspace,'/').'/'.$directory.'/';
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
		$directory = Request('config');
		$directory = basename($directory);
		$directory = substr($directory, 0, -4);
		$git_root  = self::Root();
		$redirect  = '2>&1';

		//	Change directory to workspace.
		if(!chdir($workspace) ){
			throw new Exception("`chdir` was failed. ($workspace)");
		}

		//	...
		if(!file_exists($git_root) ){
			$command = "git clone {$origin} {$directory}";
			Display(" * $command");
			if( $result  = `{$command} {$redirect}` ){
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
							Display($line);
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
		Display("\n * `$do`");
		echo `$do 2>&1`;

		//	Init submodules.
		Display("\n * `git submodule update --init --recursive`");
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
					Display($line);
				break;
			}
		}

		//	Checkout submodules
		Display("\n * Do submodule configuration.");
		foreach( self::SubmoduleConfig(true) as $key => $config ){
			//	...
			Display(" - {$key} : {$config['path']}");

			//	...
			if(!chdir( $git_root . $config['path'] ) ){
				throw new Exception("Change directory was failed. ({$git_root}{$config['path']})");
			}

			//	Checkout
			Display("  `git checkout {$branch}");
			$result  = `git checkout {$branch} $redirect`;
			foreach( explode("\n", $result) as $line ){
				switch( $line = trim($line) ){
					//	Discard
					case '':
					case "Switched to a new branch '{$branch}'":
					case "Branch '{$branch}' set up to track remote branch '{$branch}' from 'origin'.":
						Display('   '.$line);
						break;
					//	Display
					default:
						Display($line);
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
		Display(' * Set upstream URL to main repository.');
		if( $result = `git config --get remote.upstream.url $redirect` ){
			Display(" - Already set upstream URL: $result");
		}else{
			Display("  `git remote add upstream {$upstream}`");
			echo `git remote add upstream {$upstream}  $redirect`;
		}
		Display(' ');

		//	submodules
		Display(' * Set upstream URL to submodules.');
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
			Display(" - {$key}: {$config['path']}");
			if( $result = `git config --get remote.upstream.url` ){
				Display(" - Already set upstream URL: $result");
			}else{
				Display("  `git remote add upstream {$upstream}`");
				echo `git remote add upstream {$upstream}`;
			}
		}
		Display(' ');
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

	/** Fetch
	 *
	 * @created    2023-01-02
	 * @param      string     $remote
	 */
	static function Fetch(string $remote)
	{
		Debug(__METHOD__, false);
		$output = null;
		$status = null;
		exec("git fetch $remote 2>&1", $output, $status);
		foreach( $output as $line ){
			switch( $line ){
				case '':
				continue 2;
			}
			Display($line);
		}
	}

	/** Rebase
	 *
	 * @created    2023-01-02
	 * @param      string     $target
	 */
	static function Rebase(string $target)
	{
		//	...
		$branch = Request('branch');

		//	...
		Debug(__METHOD__, false);
		$output = null;
		$status = null;
		exec("git rebase $target 2>&1", $output, $status);
		foreach( $output as $line ){
			switch( trim($line) ){
				case 'Current branch master is up to date.':
				case "Current branch {$branch} is up to date.":
					continue 2;
				default:
			}
			Display($line);
		}
	}

	/** Stash Save
	 *
	 * @created    2023-01-02
	 */
	static function Save()
	{
		//	...
		$branch = Request('branch');

		//	...
		Debug(__METHOD__, false);
		$output = null;
		$status = null;
		exec("git stash save 2>&1", $output, $status);
		foreach( $output as $line ){
			switch( trim($line) ){
				case 'No local changes to save':
				case strpos(' '.$line, 'Saved working directory and index state WIP on master:')    ? true: false;
				case strpos(' '.$line, "Saved working directory and index state WIP on {$branch}:") ? true: false;
				continue 2;
			}
			Display($line);
		}
	}

	/** Stash Pop
	 *
	 * @created    2023-01-02
	 */
	static function Pop()
	{
		Debug(__METHOD__, false);
		$output = null;
		$status = null;
		exec("git stash pop 2>&1", $output, $status);
		foreach( $output as $line ){
			switch( trim($line) ){
				case 'No stash entries found.':
				continue 2;
			}
		//	Display($line);
		}
	}

	/** Push to repository.
	 *
	 * @created    2023-01-02
	 * @param      string      $remote
	 * @param      string      $branch
	 */
	static function Push(string $remote, string $branch)
	{
		//	...
		Debug(__METHOD__."($remote, $branch)", false);

		//	...
		$commit_id_1 = null;
		$commit_id_2 = null;
		foreach(explode("\n",`git branch -a 2>&1`) as $line){
			//	...
			if( strpos($line, "remotes/{$remote}/{$branch}") ){
				//	remote
				if( strpos($line, $branch) ){
					$commit_id_2 = trim(`git rev-parse {$remote}/{$branch}`);
				}
			}else{
				//	branch
				if( strpos($line, $branch) ){
					$commit_id_1 = trim(`git rev-parse {$branch}`);
				}
			}
		}

		//	Check if not push.
		if( $commit_id_1 === $commit_id_2 ){
			//	Already pushed.
			Debug("Already pushed.\n", false);
			return;
		}

		//	...
		$output = null;
		$status = null;
		exec("git push {$remote} {$branch} 2>&1", $output, $status);

		//	...
		foreach( $output as $line ){
			switch( trim($line) ){
				case '':
					continue 2;
			}
			Display($line);
		}
	}

	/**　Switch branch
	 *
	 * @created    2023-01-02
	 * @param      string      $branch
	 */
	static function Switch($branch)
	{
		Debug(__METHOD__."($branch)", false);

		//	...
		$list = self::BranchList();

		//	...
		if(!isset($list[$branch]) ){
			Debug("This branch does not exists. ({$branch})", false);
			return false;
		}

		//	...
		if( $list[$branch] ){
			//	Already on that branch.
			return true;
		}

		//	...
		$fail   = null;
		$output = null;
		$status = null;
		exec("git switch $branch 2>&1", $output, $status);
		foreach( $output as $line ){
			switch( trim($line) ){
			//	case "Already on '{$branch}'":
				case "Your branch is up to date with 'origin/{$branch}'.":
					continue 2;
				case "Switched to branch '{$branch}'":
					return true;
				default:
					$fail = true;
			}
			Display(__METHOD__.' - '.$line);
		}

		//	...
		return $fail ? false: true;
	}

	static function Checkout($branch) : bool
	{
		Debug(__METHOD__."($branch)", false);

		//	Checkout exists branch.
		if( self::Switch($branch) ){
			return true;
		}

		//	Checkout new branch.
		foreach( explode("\n", trim(`git branch -a 2>&1`,"\n")) as $line ){
			//	'  remotes/origin/master' --> 'remotes/origin/master'
			if( $line === "  remotes/origin/{$branch}" ){
				Debug(`git checkout origin/{$branch} -b {$branch} 2>&1`);
				return true;
			}
		}

		//	...
		return false;
	}

	static function BranchList() : array
	{
		$result = [];
		foreach( explode("\n", trim(`git branch`, "\n")) as $line ){
			$selected = $line[0] === '*' ? true: false;
			$branch   = substr($line, 2);
			$result[$branch] = $selected;
		}
		return $result;
	}
}
