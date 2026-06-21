# Task 241: Dockerize the Laravel Application

Fully dockerize the Laravel application with a production-ready and local-development structure using PHP 8.3, Nginx, MySQL 8, Redis, scheduler, and queue worker.

## 1. Requirement Details
- **Requirement ID:** REQ-241
- **Focus:** DevOps & Architecture.
- **Description:** Provide a complete Docker setup for local development and production. The setup should include PHP 8.3 (with required extensions), Nginx, MySQL 8, Redis, queue worker, Laravel scheduler setup, dynamic environment variable configuration (no secrets hardcoded), proper storage/cache permissions, and step-by-step commands to spin up and seed the environment.

## 2. Implementation Steps
1. **Dockerfile:** Create a multi-stage production-ready `Dockerfile` containing:
   - PHP 8.3-fpm as base.
   - Core extensions needed for Laravel (pdo_mysql, redis, bcmath, gd, opcache, zip).
   - Composer setup.
   - Dynamic stage for asset building (Node.js for Vite) if necessary, or simple multi-stage asset generation.
2. **Nginx configuration:** Create `docker/nginx/default.conf` config.
3. **docker-compose.yml & overrides:**
   - Create `docker-compose.yml` for multi-service setup (app, web server, db, redis, queue-worker, scheduler).
   - Create `docker-compose.override.yml` for local development setup (mounting volumes, npm run dev, etc.).
4. **Entrypoint Script:** Create `docker/entrypoint.sh` to run composer commands, handle caching, database migrations, and set correct directory permissions.
5. **.dockerignore:** Create `.dockerignore` to exclude node_modules, vendor, git, and local env files.
6. **Documentation:** Update `PROJECT_DOCUMENTATION.md` and `USER_GUIDE.md` to document the Docker environment.

## 3. Verification Criteria
- [ ] Verify Docker build succeeds.
- [ ] Spin up containers using `docker compose up -d`.
- [ ] Verify app connects to DB and Redis.
- [ ] Verify Laravel queue worker and scheduler containers start up.
- [ ] Run pint to ensure file format/cleanliness.
- [ ] Run `php artisan optimize`.
- [ ] Document setup instructions clearly.

## 4. Documentation Update
- [ ] Update `PROJECT_DOCUMENTATION.md` under a new deployment/Docker section.
- [ ] Update `USER_GUIDE.md` under setup instructions.
