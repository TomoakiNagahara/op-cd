Continuous Delivery dedicated to onepiece-framework
===

# Usage

```sh
php 0_action.php branch=2022 workspace=/www/workspace origin={local/GITHUB_USER_NAME} upstream={private/GITHUB_USER_NAME} [display={0/1}] [debug={0/1}] [version={70/80,81}]
```

 * The order of arguments is variable.
 * Enclosed arguments in brackets are optional.

```
branch    : Submodule branch name. Skeleton is always master.
workspace : Cloning directory.
origin    : Origin repository.
upstream  : Upstream repository.
username  : Upstream GitHub user name or "private" flag.
display   : You can hide in progress message.    - default is show
debug     : Display debug information.           - default is hide
version   : You can specify execute PHP version. - default is empty
```

# Files

 * README.md      - This one.
 * 0_action.php   - Always call this file.
 * 1_clone.php    - Clone git repository.
 * 2_upstream.php - Setup upstream.
 * 2_update.php   - PULL of origin and upstream.
 * 3_push.php     - PUSH to upstream.
 * ci.sh          - Required for git pre-push. This will eventually become unnecessary. <-- Why? How?

# Change repository

## Change the remote repository URL to your github account.

https://github.com/onepiece-framework/repo.git is change to your account.

```sh
sh asset/git/submodule/github.sh [YOUR GITHUB ACCOUNT NAME]
```

## Change the URL of the remote repository to your local path.

https://github.com/onepiece-framework/repo.git --> ~/repo/op/repo.git

```sh
sh asset/git/submodule/local.sh
```

## Change the URL of the remote repository to your local server.

https://github.com/onepiece-framework/repo.git --> repo:~/repo/op/repo.git

```sh
sh asset/git/submodule/repo.sh
```

# Howto

https://songmu.jp/riji/entry/2014-08-07-post-receive-branch.html
http://stackoverflow.com/questions/7351551/writing-a-git-post-receive-hook-to-deal-with-a-specific-branch/13057643#13057643

  "oldrev", "newref", "refname" is in the standard input at separated by spaces.
  If multiple branches are pushed at once with `push --all` etc., there are multiple lines of input.
  A master branch is pushed, If the "hoge" directory has been updated, I want to do something work it.

```sh
#!/bin/bash
# Read standard input at line by line
while read oldrev newrev refname; do
    branch=$(git rev-parse --symbolic --abbrev-ref $refname)
    # In the master branch, do some work if the "hoge" directory has been updated.
    if [[ $branch == "master" && $( git diff --name-only $oldrev $newrev -- hoge ) ]]; then
        # Do something
    fi
done
```
