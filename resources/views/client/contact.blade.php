@extends('client.structure.app')

@section('content')
    @php
        $gs = \App\HelperClass::generalSettings();
        $cs = \App\HelperClass::contactSettings();
    @endphp
    <!-- contact area start -->
    <div class="contact-area mtb-60px">
        <div class="container">
            @if($cs && $cs->map_link)
                <div class="contact-map mb-10">
                    <div id="map">
                        <div class="mapouter">
                            <div class="gmap_canvas">
                                @if(str_contains($cs->map_link, '<iframe'))
                                    {!! $cs->map_link !!}
                                @else
                                    <iframe id="gmap_canvas" src="{{ $cs->map_link }}" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="custom-row-2">
                <div class="col-lg-4 col-md-5">
                    <div class="contact-info-wrap">
                        @if($cs && $cs->phone_number)
                            <div class="single-contact-info">
                                <div class="contact-icon">
                                    <i class="fa fa-phone"></i>
                                </div>
                                <div class="contact-info-dec">
                                    <p>{{ $cs->phone_number }}</p>
                                </div>
                            </div>
                        @endif
                        @if($cs && $cs->company_email)
                            <div class="single-contact-info">
                                <div class="contact-icon">
                                    <i class="fa fa-globe"></i>
                                </div>
                                <div class="contact-info-dec">
                                    <p><a href="mailto:{{ $cs->company_email }}">{{ $cs->company_email }}</a></p>
                                    <p><a href="{{ url('/') }}">{{ str_replace(['http://', 'https://'], '', config('app.url')) }}</a></p>
                                </div>
                            </div>
                        @endif
                        @if($cs && $cs->address)
                            <div class="single-contact-info">
                                <div class="contact-icon">
                                    <i class="fa fa-map-marker"></i>
                                </div>
                                <div class="contact-info-dec">
                                    <p>{!! nl2br(e($cs->address)) !!}</p>
                                </div>
                            </div>
                        @endif
                        <div class="contact-social">
                            <h3>Follow Us</h3>
                            <div class="social-info">
                                <ul>
                                    @if($cs && $cs->facebook_status && $cs->facebook_url)
                                        <li>
                                            <a href="{{ $cs->facebook_url }}" target="_blank"><i class="ion-social-facebook"></i></a>
                                        </li>
                                    @endif
                                    @if($cs && $cs->x_status && $cs->x_url)
                                        <li>
                                            <a href="{{ $cs->x_url }}" target="_blank"><i class="ion-social-twitter"></i></a>
                                        </li>
                                    @endif
                                    @if($cs && $cs->instagram_status && $cs->instagram_url)
                                        <li>
                                            <a href="{{ $cs->instagram_url }}" target="_blank"><i class="ion-social-instagram"></i></a>
                                        </li>
                                    @endif
                                    @if($cs && $cs->youtube_status && $cs->youtube_url)
                                        <li>
                                            <a href="{{ $cs->youtube_url }}" target="_blank"><i class="ion-social-youtube"></i></a>
                                        </li>
                                    @endif
                                    @if($cs && $cs->whatsapp_status && $cs->whatsapp_url)
                                        <li>
                                            <a href="{{ $cs->whatsapp_url }}" target="_blank"><i class="ion-social-whatsapp"></i></a>
                                        </li>
                                    @endif
                                    @if($cs && $cs->tiktok_status && $cs->tiktok_url)
                                        <li>
                                            <a href="{{ $cs->tiktok_url }}" target="_blank"><i class="fa fa-music"></i></a>
                                        </li>
                                    @endif
                                    @if($cs && $cs->linkedin_status && $cs->linkedin_url)
                                        <li>
                                            <a href="{{ $cs->linkedin_url }}" target="_blank"><i class="ion-social-linkedin"></i></a>
                                        </li>
                                    @endif
                                    @if($cs && $cs->thread_status && $cs->thread_url)
                                        <li>
                                            <a href="{{ $cs->thread_url }}" target="_blank"><i class="ion-chatbubble-working"></i></a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-7">
                    <div class="contact-form">
                        <div class="contact-title mb-30">
                            <h2>Get In Touch</h2>
                        </div>
                        <form class="contact-form-style" id="contact-form" action="#" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <input name="name" placeholder="Name*" type="text" required>
                                </div>
                                <div class="col-lg-6">
                                    <input name="email" placeholder="Email*" type="email" required>
                                </div>
                                <div class="col-lg-12">
                                    <input name="subject" placeholder="Subject*" type="text" required>
                                </div>
                                <div class="col-lg-12">
                                    <textarea name="message" placeholder="Your Message*" required></textarea>
                                    <div class="billing-btn">
                                        <button type="submit">SEND</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <p class="form-messege"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- contact area end -->
@endsection

@push('styles')
    <style>
        .gmap_canvas iframe {
            width: 100%;
            height: 450px;
            border: 0;
        }
    </style>
@endpush
