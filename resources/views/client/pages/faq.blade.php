@extends('client.structure.app')
@section('content')

<div class="faq-area mt-60px mb-60px">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title text-center mb-40px">
                    <h2 class="title">{{ $title }}</h2>
                    <p>Find answers to the most common questions about our services.</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion" id="faqAccordion">
                    @forelse($faqs as $faq)
                        <div class="accordion-item border-0 mb-3 shadow-sm rounded">
                            <h2 class="accordion-header" id="heading{{ $faq->id }}">
                                <button class="accordion-button {{ !$loop->first ? 'collapsed' : '' }} fw-bold text-dark rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $faq->id }}">
                                    {{ $faq->question }}
                                </button>
                            </h2>
                            <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ $faq->id }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted fs-15">
                                    {{ $faq->answer }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <p class="text-muted">No FAQs available at the moment.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: var(--bs-primary);
        box-shadow: none;
    }
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,0,0,.125);
    }
    .accordion-item {
        overflow: hidden;
    }
</style>
@endsection
