# Task: Resolve WSL2 Docker 504 Gateway Timeout

## Objective
Fix the Nginx 504 Gateway Timeout in the local WSL2 Docker environment caused by slow file I/O during view compilation.

## Implementation Steps
1. Increase fastcgi_read_timeout to 300 in docker/nginx/default.conf.
2. Update docker/entrypoint.sh to run specific cache clears instead of optimize:clear to preserve views.
3. Add php artisan view:cache to entrypoint.sh for first-boot optimization.
4. Fix null pointer exception in header.blade.php when menu_banner is null.
5. Add optional chaining to banner_1.blade.php and banner_2.blade.php.

## Verification
- Ensure Nginx configuration is updated.
- Ensure entrypoint.sh executes successfully without destroying view cache.
- Ensure homepage loads without 500 or 504 errors.

## Documentation
- Update PROJECT_DOCUMENTATION.md.
