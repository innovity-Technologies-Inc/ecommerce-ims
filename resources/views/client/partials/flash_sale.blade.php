@if(isset($flashSale) && $flashSale->isActive() && $flashSale->items->isNotEmpty())
<section class="flash-sale-area mt-60px">
    <div class="container">
        <div class="row align-items-center mb-30px">
            <div class="col-md-6">
                <div class="section-title mb-0">
                    <h2 class="mb-0">{{ $flashSale->name }}</h2>
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="flash-sale-timer d-inline-flex align-items-center">
                    <span class="me-3 fw-bold text-uppercase" style="letter-spacing: 1px; color: #777;">Ends In:</span>
                    <div id="flash-sale-countdown" class="d-flex" data-end-date="{{ $flashSale->end_date->toIso8601String() }}">
                        <div class="timer-unit mx-1 text-center">
                            <span class="days d-block fw-bold fs-4" style="color: #ff4545; min-width: 40px;">00</span>
                            <small class="text-uppercase" style="font-size: 10px; color: #999;">Days</small>
                        </div>
                        <span class="fs-4 fw-bold" style="color: #ff4545;">:</span>
                        <div class="timer-unit mx-1 text-center">
                            <span class="hours d-block fw-bold fs-4" style="color: #ff4545; min-width: 40px;">00</span>
                            <small class="text-uppercase" style="font-size: 10px; color: #999;">Hrs</small>
                        </div>
                        <span class="fs-4 fw-bold" style="color: #ff4545;">:</span>
                        <div class="timer-unit mx-1 text-center">
                            <span class="minutes d-block fw-bold fs-4" style="color: #ff4545; min-width: 40px;">00</span>
                            <small class="text-uppercase" style="font-size: 10px; color: #999;">Mins</small>
                        </div>
                        <span class="fs-4 fw-bold" style="color: #ff4545;">:</span>
                        <div class="timer-unit mx-1 text-center">
                            <span class="seconds d-block fw-bold fs-4" style="color: #ff4545; min-width: 40px;">00</span>
                            <small class="text-uppercase" style="font-size: 10px; color: #999;">Secs</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flash-sale-slider owl-carousel owl-nav-style">
            @foreach($flashSale->items as $item)
                @include('client.partials.product_card', ['product' => $item->product])
            @endforeach
        </div>

        <div class="row mt-40px">
            <div class="col-12 text-center">
                <a href="{{ route('client.products.index', ['flash_sale' => [$flashSale->id]]) }}" class="btn btn-dark px-5 py-3 text-uppercase fw-bold" style="border-radius: 0; background: #333; letter-spacing: 1px;">View All Flash Deals</a>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countdownElement = document.getElementById('flash-sale-countdown');
        if (!countdownElement) return;

        const endDate = new Date(countdownElement.getAttribute('data-end-date')).getTime();
        
        const daysSpan = countdownElement.querySelector('.days');
        const hoursSpan = countdownElement.querySelector('.hours');
        const minutesSpan = countdownElement.querySelector('.minutes');
        const secondsSpan = countdownElement.querySelector('.seconds');

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endDate - now;

            if (distance < 0) {
                clearInterval(timerInterval);
                document.querySelector('.flash-sale-area').style.display = 'none';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            daysSpan.textContent = days.toString().padStart(2, '0');
            hoursSpan.textContent = hours.toString().padStart(2, '0');
            minutesSpan.textContent = minutes.toString().padStart(2, '0');
            secondsSpan.textContent = seconds.toString().padStart(2, '0');
        }

        const timerInterval = setInterval(updateCountdown, 1000);
        updateCountdown();

        // Initialize Owl Carousel for Flash Sale if not already handled by a global script
        if (jQuery().owlCarousel) {
            $('.flash-sale-slider').owlCarousel({
                loop: false,
                margin: 30,
                nav: true,
                dots: false,
                navText: ['<i class="ion-ios-arrow-left"></i>', '<i class="ion-ios-arrow-right"></i>'],
                responsive: {
                    0: { items: 1 },
                    576: { items: 2 },
                    768: { items: 2 },
                    992: { items: 3 },
                    1200: { items: 4 }
                }
            });
        }
    });
</script>
@endif
