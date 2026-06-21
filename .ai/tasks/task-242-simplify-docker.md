# Task 242: Simplify Docker Setup (Remove npm and Alpine.js Build)

Remove all Node, npm, and Alpine.js assets building processes from the Dockerfile and docker-compose configurations.

## 1. Requirement Details
- **Requirement ID:** REQ-242
- **Focus:** DevOps & Simplification.
- **Description:** Clean up the Dockerfile by removing the Node/npm build stage (`asset-builder`) and remove the related port mappings and volume configurations from docker-compose.

## 2. Implementation Steps
1. **Dockerfile:** Remove the `asset-builder` stage and simplify the Dockerfile to only focus on PHP/Composer environment.
2. **docker-compose.override.yml:** Remove port `5173` mapping (Vite dev server) from the `app` container.
3. **Documentation:** Update references in `PROJECT_DOCUMENTATION.md` and `USER_GUIDE.md` to reflect this simplified setup.

## 3. Verification Criteria
- [x] Verify Dockerfile no longer builds Node assets or uses a Node stage.
- [x] Verify `docker-compose.override.yml` no longer exposes Vite dev server port.
- [x] Verify project documentation is updated.
- [x] Run `php artisan optimize`.

## 4. Documentation Update
- [x] Update `PROJECT_DOCUMENTATION.md` under the Docker section.
- [x] Update `USER_GUIDE.md` under setup instructions.
