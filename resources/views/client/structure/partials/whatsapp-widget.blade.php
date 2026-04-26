@php
    $contact = \App\HelperClass::contactSettings();
@endphp

@if($contact->whatsapp_status && $contact->whatsapp_url)
    <div id="whatsapp-widget" class="whatsapp-widget">
        <!-- Chat Window -->
        <div id="whatsapp-chat-window" class="whatsapp-chat-window no-print">
            <div class="whatsapp-chat-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="whatsapp-avatar">
                        <iconify-icon icon="logos:whatsapp-icon" class="fs-24"></iconify-icon>
                    </div>
                    <div>
                        <div class="whatsapp-header-title">WhatsApp Chat</div>
                        <div class="whatsapp-header-status">Typically replies in minutes</div>
                    </div>
                </div>
                <button type="button" id="close-whatsapp-chat" class="whatsapp-close-btn">
                    <iconify-icon icon="solar:close-circle-bold-duotone"></iconify-icon>
                </button>
            </div>
            <div class="whatsapp-chat-body">
                <div class="whatsapp-message-bubble">
                    Hi there! 👋<br>How can we help you today?
                </div>
            </div>
            <div class="whatsapp-chat-footer">
                <textarea id="whatsapp-message-input" placeholder="Type your message..." rows="1"></textarea>
                <button type="button" id="send-whatsapp-btn" class="whatsapp-send-btn">
                    <iconify-icon icon="solar:plain-bold-duotone"></iconify-icon>
                </button>
            </div>
        </div>

        <!-- Floating Button -->
        <button type="button" id="toggle-whatsapp-chat" class="whatsapp-floating-btn no-print" title="Chat with us on WhatsApp">
            <iconify-icon icon="logos:whatsapp-icon" class="whatsapp-icon-main"></iconify-icon>
            <span class="whatsapp-btn-text">Chat with us</span>
        </button>
    </div>

    <style>
        .whatsapp-widget {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            font-family: 'Open Sans', sans-serif;
        }

        /* Floating Button */
        .whatsapp-floating-btn {
            background-color: #25d366;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .whatsapp-floating-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4);
            background-color: #20ba5a;
        }

        .whatsapp-icon-main {
            font-size: 28px;
        }

        .whatsapp-btn-text {
            font-weight: 700;
            font-size: 14px;
        }

        /* Chat Window */
        .whatsapp-chat-window {
            position: absolute;
            bottom: 70px;
            right: 0;
            width: 320px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: none;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .whatsapp-chat-header {
            background-color: #075e54;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .whatsapp-header-title {
            font-weight: 700;
            font-size: 15px;
        }

        .whatsapp-header-status {
            font-size: 11px;
            opacity: 0.8;
        }

        .whatsapp-close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s;
            display: flex;
        }

        .whatsapp-close-btn:hover {
            opacity: 1;
        }

        .whatsapp-chat-body {
            padding: 20px;
            background-color: #e5ddd5;
            background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
            height: 150px;
            overflow-y: auto;
        }

        .whatsapp-message-bubble {
            background: white;
            padding: 10px 15px;
            border-radius: 0 10px 10px 10px;
            font-size: 13px;
            max-width: 85%;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            color: #303030;
        }

        .whatsapp-chat-footer {
            padding: 10px 15px;
            background: white;
            display: flex;
            align-items: center;
            gap: 10px;
            border-top: 1px solid #f0f0f0;
        }

        #whatsapp-message-input {
            flex: 1;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 13px;
            outline: none;
            resize: none;
            background-color: #f9f9f9;
        }

        #whatsapp-message-input:focus {
            border-color: #25d366;
            background-color: white;
        }

        .whatsapp-send-btn {
            background-color: #25d366;
            color: white;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .whatsapp-send-btn:hover {
            transform: scale(1.1);
            background-color: #20ba5a;
        }

        /* Mobile Adjustments */
        @media (max-width: 576px) {
            .whatsapp-widget {
                bottom: 20px;
                right: 20px;
            }
            .whatsapp-chat-window {
                width: 280px;
                right: -10px;
            }
            .whatsapp-btn-text {
                display: none;
            }
            .whatsapp-floating-btn {
                padding: 12px;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            const $chatWindow = $('#whatsapp-chat-window');
            const $toggleBtn = $('#toggle-whatsapp-chat');
            const $closeBtn = $('#close-whatsapp-chat');
            const $sendBtn = $('#send-whatsapp-btn');
            const $input = $('#whatsapp-message-input');

            // Toggle window
            $toggleBtn.on('click', function() {
                $chatWindow.fadeToggle(300);
            });

            // Close window
            $closeBtn.on('click', function() {
                $chatWindow.fadeOut(300);
            });

            // Send message
            $sendBtn.on('click', function() {
                const message = $input.val().trim();
                if (message) {
                    const phoneNumber = "{{ $contact->whatsapp_url }}".replace(/[^0-9]/g, '');
                    const encodedMessage = encodeURIComponent(message);
                    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
                    
                    window.open(whatsappUrl, '_blank');
                    $input.val('');
                    $chatWindow.fadeOut(300);
                }
            });

            // Enter key to send
            $input.on('keypress', function(e) {
                if (e.which == 13 && !e.shiftKey) {
                    e.preventDefault();
                    $sendBtn.click();
                }
            });

            // Close when clicking outside
            $(document).on('click', function(event) {
                if (!$(event.target).closest('#whatsapp-widget').length) {
                    $chatWindow.fadeOut(300);
                }
            });
        });
    </script>
@endif
