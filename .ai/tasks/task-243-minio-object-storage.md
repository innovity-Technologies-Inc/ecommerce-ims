# Task 243: MinIO Object Storage Integration

Integrate MinIO as a self-hosted, S3-compatible object storage backend for the smart-ecom project.

## 1. Requirement Details
- **Requirement ID:** REQ-243
- **Focus:** Infrastructure & File Storage.
- **Description:** Add MinIO to the Docker stack, configure Laravel to use the `minio` filesystem disk (via the S3 driver), install `league/flysystem-aws-s3-v3`, update `.env` / `.env.example`, and update `HelperClass::file_upload()` so all file uploads are stored in MinIO instead of the local disk.

## 2. Implementation Steps

1. **Install Composer package:** `league/flysystem-aws-s3-v3 ^3.0`.
2. **docker-compose.yml:** Add `minio` service (image: `minio/minio:latest`, ports 9000 & 9001) and a one-shot `minio-init` service (image: `minio/mc`) that creates the default bucket on startup. Add `minio_data` volume. Wire `minio` into the `app` service `depends_on`.
3. **config/filesystems.php:** Add a named `minio` disk using the `s3` driver. Key setting: `use_path_style_endpoint => true`.
4. **.env / .env.example:** Add `MINIO_*` variables and set `FILESYSTEM_DISK=minio`.
5. **HelperClass:** Update `file_upload()` to store via `Storage::disk('minio')` and return the stored path. Update `file_delete()` to delete from the same disk.
6. **Verification:** Run `php artisan config:clear && php artisan optimize`.
7. **Documentation:** Update `PROJECT_DOCUMENTATION.md` and `USER_GUIDE.md`.
8. **Commit:** Stage all and commit with `feat: integrate MinIO object storage (REQ-243)`.

## 3. Verification Criteria
- [x] `composer require` installs without errors.
- [x] `docker compose up -d` starts `minio` and `minio-init` cleanly (setting up both `smart-ecom-dev` and `smart-ecom-production` buckets).
- [x] MinIO console accessible at `http://localhost:9001`.
- [x] `php artisan optimize` runs without errors.
- [x] File uploads in the admin panel are saved to MinIO bucket.
- [x] Public URLs resolve correctly.

## 4. Documentation Update
- [x] Update `PROJECT_DOCUMENTATION.md` with MinIO section (What, How, Data & Storage).
- [x] Update `USER_GUIDE.md` to mention MinIO console access.
