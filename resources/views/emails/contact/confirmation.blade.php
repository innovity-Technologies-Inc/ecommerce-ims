<x-mail::message>
# Hello {{ $contactMessage->name }},

Thank you for reaching out to us! We have received your message regarding **"{{ $contactMessage->subject }}"**.

Our team is currently reviewing your inquiry and we will be reaching out to you soon.

**Your Message:**
{{ $contactMessage->message }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
