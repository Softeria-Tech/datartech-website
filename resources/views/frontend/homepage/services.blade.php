<!-- ======Find Government Services Section====== -->
<style>
    section {
        margin: 0 !important;
    }
</style>
<section class="government-service">
    <div class="container">
        <div class="section-title">
            <div class="semi-title" data-aos="fade-up">
                <div class="animated-circles justify-content-center">
                    <div class="small-circle-start"></div>
                    <div class="title">All Services</div>
                    <div class="small-circle-end"></div>
                </div>
            </div>
        </div>
        <div class="title text-center mb-5" data-aos="fade-up">
            <h2 class="cssanimation lePopUp sequence">Find All Services</h2>
        </div>
        <!-- card -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @foreach ($service_categories as $service_category)
                <div class="col" data-aos="fade-up">
                    <div class="card h-100 border-0 rounded-0">
                        <img src="{{ url('storage/' . $service_category->image) }}" class="card-img-top img-fluid"
                            alt="...">
                        <div class="card-body">
                            <a href="{{ route('service-details', ['service_id' => $service_category->id]) }}">
                                <h4 class="card-title text-center">{{ $service_category->category_name }}</h4>
                            </a>
                            <p class="card-text text-center">
                                {{ \Illuminate\Support\Str::limit(strip_tags($service_category->category_desc), 30, '...') }}
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 p-0 text-center">
                            <a href="{{ route('service-details', ['service_id' => $service_category->id]) }}"
                                class="card-btn">More Details <i class="bi bi-arrow-up-right"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
