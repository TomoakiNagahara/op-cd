Continuous Delivery dedicated to onepiece-framework
===

# Usage

```sh
php ./0_action.php [branch name] [GitHub account name]
```

# Files

README.md      - This one.
0_action.php   - Always call this file.
1_clone.php    - Clone git repository.
2_upstream.php - Setup upstream.
2_update.php   - PULL of origin and upstream.
3_push.php     - PUSH to upstream.
ci.sh          - Required for git pre-push. This will eventually become unnecessary.
