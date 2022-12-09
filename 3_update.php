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
$display = Request('display') ?? true;
if( $display ){ echo "\nStart repository update.\n"; }

//	Start
if( $display ){ echo "  Stash is save.\n"; }
$result  = GitSaveResult(  `git stash  save             2>/dev/null` ?? '');

//	Rebase  upstream - Upstream first.
if( $display ){ echo "  Fetch upstream repository and rebase.\n"; }
$result .= GitFetchResult( `git fetch  upstream         2>/dev/null` ?? '');
$result .= GitRebaseResult(`git rebase upstream/master  2>/dev/null` ?? '');

//  Rebase  origin.
if( $display ){ echo "  Fetch origin repository and rebase.\n"; }
$result .= GitFetchResult( `git fetch  origin           2>/dev/null` ?? '');
$result .= GitRebaseResult(`git rebase origin/master    2>/dev/null` ?? '');

//  Rebase submodules upstream - Upstream first.
if( $display ){ echo "  Fetch submodule upstream repository and rebase.\n"; }
$result .= GitFetchResult( `git submodule foreach git fetch    upstream      2>/dev/null` ?? '');
$result .= GitRebaseResult(`git submodule foreach git rebase   upstream/2022 2>/dev/null` ?? '');

//  Rebase submodules origin.
if( $display ){ echo "  Fetch submodule origin repository and rebase.\n"; }
$result .= GitFetchResult( `git submodule foreach git fetch    origin      2>/dev/null` ?? '');
$result .= GitRebaseResult(`git submodule foreach git rebase   origin/2022 2>/dev/null` ?? '');

//	Finish
if( $display ){ echo "  Stash is pop.\n"; }
$result .= GitPopResult(   `git stash  pop              2>/dev/null` ?? '');

//	...
echo $result;

//  Successful.
return true;

/** Git submodule.
 *
 * @created    2022-12-08
 * @param      string      $result
 * @return     string
 */
function GitSubmodule(string $result) : string {
	//	...
	$separated = explode("\n", $result);

	//	...
	$lines = [];
	while( $line = array_shift($separated) ){
		//	...
		switch( $line ){
			case '':
			case strpos($line, 'Entering') === 0:
				continue 2;
		}

		//	...
		$lines[] = $line;
	}

	//	...
	return join("\n", $lines);
}

/** Git stash save result.
 *
 * @created    2022-12-08
 * @param      string      $result
 */
function GitSaveResult(string $result) : void {
	//	...
	foreach(explode("\n", $result) as $line){
		//	...
		switch( trim($line) ){
			case '':
			case 'No local changes to save':
			case strpos($line, 'Saved working directory and index state WIP on master:') !== false:
				continue 2;
		}

		//	...
		D($line);
	}
}

/** Git stash pop result.
 *
 * @created    2022-12-08
 * @param      string      $result
 */
function GitPopResult(string $result) : void {
	//	...
	if( strpos($result,'no changes added to commit (use "git add" and/or "git commit -a")') ){
		return;
	}

	//	...
	foreach(explode("\n", $result) as $line){
		//	...
		switch( trim($line) ){
			case '':
				continue 2;
		}

		//	...
		D($line);
	}
}

/** Git fetch result.
 *
 * @created    2022-12-08
 * @param      string      $result
 */
function GitFetchResult(string $result) : void {
	//	...
	$result = GitSubmodule($result);

	//	...
	foreach(explode("\n", $result) as $line){
		//	...
		switch( trim($line) ){
			case '':
				continue 2;
		}

		//	...
		D($line);
	}
}

/** Git rebase result.
 *
 * @created    2022-12-08
 * @param      string      $result
 */
function GitRebaseResult(string $result) : void {
	//	...
	$result = GitSubmodule($result);

	//	Current branch.
	$current_branch = `git branch --contains`;
	$current_branch = trim($current_branch);
	$current_branch = substr($current_branch , 2);

	//	Specify by cli.
	$specify_branch = Request('branch');

	//	...
	foreach(explode("\n", $result) as $line){
		//	...
		switch( trim($line) ){
			case '':
			case "Current branch {$current_branch} is up to date.":
			case "Current branch {$specify_branch} is up to date.":
				continue 2;
		}

		//	...
		D($line);
	}
}
