   <!-- ======Best City Government & Municipal Service Based On USA====== -->
   <section class="best-city-government remove-div one-second" data-aos="fade-up">
       <div class="images-and-overlay">
           <img class="img-fluid" src="{{ asset('assets/frontend/images/homepage/services/services.jpg') }}"
               alt="best-city">
           <div class="overlay"></div>
       </div>
       <div class="container">
           <div class="city-content">
               <div class="city-title" data-aos="fade-up">
                   <h2 class="text-white text-center "> <span class="cssanimation lePopUp sequence">Best
                           Online Cyber </span> <br>
                       <span class="cssanimation lePopUp sequence">All in One Services
                           </span>
                   </h2>
               </div>
               <div class="city-service-list">
                   <div class="row city-footer-content">
                       <div class="col-12 col-md-6 col-lg-4" data-aos="fade-right">
                           <ul>

                               @foreach ($chunk1 as $service)
                                   <li class="hove">
                                       <a href="{{ url('service-details/' . $service->service_category_id) }}"
                                           target="_blank" rel="noopener noreferrer">
                                           <span>{{ $service->service_name }}</span>
                                           <i class="bi bi-chevron-right float-end me-2"></i></a>
                                       <div class="divide-row"></div>
                                   </li>
                               @endforeach

                           </ul>
                       </div>
                       <div class="col-12 col-md-6 col-lg-4" data-aos="fade-down">

                           <ul>

                               @foreach ($chunk2 as $service)
                                   <li class="hove">
                                       <a href="{{ url('service-details/' . $service->service_category_id) }}"
                                           target="_blank" rel="noopener noreferrer">
                                           <span>{{ $service->service_name }}</span>
                                           <i class="bi bi-chevron-right float-end me-2"></i></a>
                                       <div class="divide-row"></div>
                                   </li>
                               @endforeach


                           </ul>
                       </div>
                       <div class="col-12 col-md-6 col-lg-4" data-aos="fade-left">
                           <ul>

                               @foreach ($chunk3 as $service)
                                   <li class="hove">
                                       <a href="{{ url('service-details/' . $service->service_category_id) }}"
                                           target="_blank" rel="noopener noreferrer">
                                           <span>{{ $service->service_name }}</span>
                                           <i class="bi bi-chevron-right float-end me-2"></i></a>
                                       <div class="divide-row"></div>
                                   </li>
                               @endforeach

                           </ul>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </section>
