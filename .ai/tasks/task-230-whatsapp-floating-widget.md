# Task-230: WhatsApp Floating Chat Widget

## Requirement
Add a floating WhatsApp icon on the client-side frontend.
- When clicked, it opens a small chat window.
- Users can type a message.
- "Send" button opens WhatsApp (app or web) with the message pre-filled.
- WhatsApp number and status should be fetched from `ContactSetting`.

## Implementation Steps

### 1. Model & Helper
- [ ] Verify `HelperClass::contactSettings()` exists to fetch settings in Blade.

### 2. Frontend Layout (`resources/views/client/structure/master.blade.php`)
- [ ] Add CSS for the floating widget:
    - Position: bottom-right.
    - Animation for the icon.
    - Chat window styles (emerald/white theme).
- [ ] Add HTML structure:
    - Floating button.
    - Chat window (initially hidden).
    - Textarea for message.
    - Send button.
- [ ] Add JavaScript:
    - Toggle chat window.
    - Construct WhatsApp URL: `https://wa.me/NUMBER?text=ENCODED_MESSAGE`.
    - Open URL on send.

### 3. Verification
- [ ] Enable WhatsApp in Admin Settings.
- [ ] Visit client frontend.
- [ ] Test toggling the window.
- [ ] Test sending a message (verify redirection).

## Verification Criteria
- [ ] Widget is only visible if `whatsapp_status` is true.
- [ ] Chat window opens/closes correctly.
- [ ] Message is correctly passed to WhatsApp.
