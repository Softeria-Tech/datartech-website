@extends('frontend.layouts.app')
@section('content')
    <style>
        .company-list ul li {
            line-height: 30px;
            font-size: 20px
        }

        .company-list ul li .square {
            display: inline-block;
            content: "";
            background-color: #313AE5;
            width: 10px;
            height: 10px;
        }

        @media only screen and (max-width: 768px) {
            .other-services .details-link {
                margin: 0 !important
            }
        }
    </style>
    <main class="service-detail">
        @include('frontend.services.banner')
        <section class="service-details" data-aos="fade-up">
            <div class="container">
                <div class="row mb-3">
                    <div class="col-12 col-md-8">

                        <h2 class="mb-1 text-center">{{ $service_category->category_name }}</h2>
                        <div class="city-visitors-guide" data-aos="fade-left">
                            <img src="{{ url('storage/' . $service_category->image) }}" class="img-fluid guide-photo"
                                alt="">

                        </div>
                        <div class="company-list category-desc-wrapper">
                            <p class="mb-3">{!! $service_category->category_desc !!}</p>
                        </div>
                        <div class="other-services">
                            <div class="details-link mt-3" style="margin-right:8rem;margin-top:3rem !important;"
                                data-aos="fade-right">
                                <ul>
                                    @foreach ($services as $service)
                                        <li>
                                            <a href="{{ $service->serviceCategory->service_url }}">
                                                <h4>{{ $service->service_name }}</h4>
                                                <span class="d-flex "><i class="fa-solid fa-angles-right"></i></span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="download-files text-center py-2 d-flex align-content-center align-items-center justify-content-center cityWall-btn"
                                    role="search">
                                    <a href="{{ $service_category->service_url }}" target="_blank"
                                        style="font-size: 18px;padding:10px 100px;">Request for Service <i
                                            class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="semi-title mb-3">
                            <div class="animated-circles" style="margin-left: 7rem">
                                <div class="small-circle-start"></div>
                                <span class="title" style="font-size: 18px">All Services</span>
                            </div>
                        </div>
                        <div class="details-link mt-2" data-aos="fade-right">
                            <ul>
                                @foreach ($service_categories as $service_category)
                                    <li>
                                        <a href="{{ route('service-details', ['service_id' => $service_category->id]) }}">
                                            <h4>{{ $service_category->category_name }}</h4>
                                            <span class="d-flex "><i class="fa-solid fa-angles-right"></i></span>
                                        </a>
                                    </li>
                                @endforeach

                            </ul>
                        </div>
                        <div class="Contact-advice" data-aos="fade-right">
                            <div class="position-relative ">
                                <img src="{{ asset('assets/frontend/images/service-details/support.gif') }}"
                                    class="d-block img-fluid img-bg " alt="...">
                                <div class="cover-images-overlay">
                                    <h2 class="text-white ">Contact Us for <br> any Enquiries</h2>
                                    <img src="{{ asset('assets/frontend/images/service-details/support.gif') }}"
                                        class="img-fluid support" alt="">
                                    <div class="phone-coll">
                                        <p class="text-white mb-2">Need help? Talk to an expert</p>
                                        <a href="tel:+254726238623 ">
                                            +254 726 238 623
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const container = document.querySelector('.category-desc-wrapper');
        if (!container) return;

        const uls = container.querySelectorAll('ul');

        uls.forEach(ul => {
            const lis = ul.querySelectorAll('li');

            lis.forEach(li => {
                const text = li.textContent.trim();
                console.log(text)
                li.innerHTML = `<span class="square"></span> <span>${text}</span>`;
            });
        });
    });
</script>
