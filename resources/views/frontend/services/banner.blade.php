<div class="banner">
    <div id="carouselExampleSlidesOnly" class="carousel slide pointer-event" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item">
                <div class="images-optimization position-relative ">
                    <img src="{{ asset('assets/frontend/images/service-details/banner/banner-2.jpg') }}"
                        class="d-block img-fluid " alt="...">
                    <div class="cover-images-overlay">
                        <div class="container">
                            <div class="banner-content ">
                                <h4 class="text-white">Comprehensive Overview of Our
                                    {{ $service_category->category_name }}</h4>
                                <h1 class="text-white text-uppercase  my-3">Service Details</h1>
                            </div>
                            <div class="home-about-link">
                                <ul>
                                    <li><a class="text-white" href="{{ route('index') }}">Home</a></li>
                                    <li class="text-white">&nbsp;>&nbsp;</li>
                                    <li><a class="text-white"
                                            href="{{ route('service-details', ['service_id' => $service_category->id]) }}">{{ $service_category->category_name }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item active">
                <div class="images-optimization position-relative ">
                    <img src="{{ asset('assets/frontend/images/service-details/banner/banner-1.jpg') }}"
                        class="d-block img-fluid " alt="...">
                    <div class="cover-images-overlay">
                        <div class="container">
                            <div class="banner-content ">
                                <h4 class="text-white">Comprehensive Overview of Our
                                    {{ $service_category->category_name }}</h4>
                                <h1 class="text-white text-uppercase  my-3">Service Details</h1>
                            </div>
                            <div class="home-about-link">
                                <ul>
                                    <li><a class="text-white" href="{{ route('index') }}">Home</a></li>
                                    <li class="text-white"> &nbsp;>&nbsp; </li>
                                    <li><a class="text-white"
                                            href="{{ route('service-details', ['service_id' => $service_category->id]) }}">{{ $service_category->category_name }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="images-optimization position-relative ">
                    <img src="{{ asset('assets/frontend/images/service-details/banner/banner-2.jpg') }}"
                        class="d-block img-fluid " alt="...">
                    <div class="cover-images-overlay">
                        <div class="container">
                            <div class="banner-content ">
                                <h4 class="text-white">Comprehensive Overview of Our
                                    {{ $service_category->category_name }}</h4>
                                <h1 class="text-white text-uppercase  my-3">Service Details</h1>
                            </div>
                            <div class="home-about-link">
                                <ul>
                                    <li><a class="text-white" href="{{ route('index') }}">Home</a></li>
                                    <li class="text-white"> &nbsp;>&nbsp; </li>
                                    <li><a class="text-white"
                                            href="{{ route('service-details', ['service_id' => $service_category->id]) }}">{{ $service_category->category_name }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
