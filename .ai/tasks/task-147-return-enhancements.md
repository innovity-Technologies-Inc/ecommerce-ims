# Task-147: Return Enhancements (Email Notifications & Receiving Allocation)

## Objective
Implement automated email notifications for return requests and status updates, and move the inventory allocation process from the "Approval" step to the "Receiving" step.

## Implementation Steps

### 1. Email Notifications
- [x] Create `app/Mail/ReturnRequestConfirmationMail.php`
- [x] Create `app/Mail/ReturnStatusUpdateMail.php`
- [x] Create `resources/views/emails/returns/confirmation.blade.php`
- [x] Create `resources/views/emails/returns/status_update.blade.php`
- [x] Update `ReturnService::storeReturnRequest()` to send confirmation email.
- [x] Update `ReturnService::updateStatus()` to send status update email.

### 2. Decouple Allocation from Approval
- [x] Update `ReturnRequestStatusUpdateRequest.php` to remove items/allocation validation.
- [x] Update `ReturnService::updateStatus()` to remove allocation saving logic.

### 3. Move Allocation to Receiving Workflow
- [x] Create `app/Http/Requests/Admin/ReturnReceiveRequest.php` with the allocation validation rules.
- [x] Update `Admin\ReturnController::receive()` to accept `ReturnReceiveRequest`.
- [x] Update `ReturnService::receiveReturn()` to accept allocation data and process it before stock adjustments.
- [x] Update `resources/views/admin/returns/show_request.blade.php` to move the allocation UI to the "Approved" section.

### 4. Verification & Documentation
- [x] Verify emails are sent correctly.
- [x] Verify allocation during receiving updates stock, wastage, and ledgers correctly.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- Customer receives confirmation email upon submission.
- Customer receives status update email upon admin action.
- Approval step does not require inventory allocation.
- Receiving step requires inventory allocation and correctly updates stock/wastage.
