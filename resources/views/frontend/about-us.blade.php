  @extends('frontend.layouts.app')
  @section('content')
      <style>
          img {
              transition: .5s;
          }

          .product-sorting .pagination {
              display: none;
          }
      </style>
      <main class="service-detail">
          <div class="banner">
              <div id="carouselExampleSlidesOnly" class="carousel slide pointer-event" data-bs-ride="carousel">
                  <div class="carousel-inner">

                      <div class="carousel-item active">
                          <div class="images-optimization position-relative ">
                              <img src="{{ asset('assets/frontend/images/service-details/banner/banner-1.jpg') }}"
                                  class="d-block img-fluid " alt="...">
                              <div class="cover-images-overlay">
                                  <div class="container">
                                      <div class="banner-content ">
                                          <h4 class="text-white">About
                                          </h4>
                                          <h1 class="text-white text-uppercase  my-3">Datartech Digital Solutions</h1>
                                      </div>
                                      <div class="home-about-link">
                                          <ul>
                                              <li><a class="text-white" href="{{ route('index') }}">Home</a></li>
                                              <li class="text-white"> &nbsp;>&nbsp; </li>
                                              <li><a class="text-white" href="#">About Us</a>
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
                                              Shop</h4>
                                          <h1 class="text-white text-uppercase  my-3">Service Details</h1>
                                      </div>
                                      <div class="home-about-link">
                                          <ul>
                                              <li><a class="text-white" href="{{ route('index') }}">Home</a></li>
                                              <li class="text-white"> &nbsp;>&nbsp; </li>
                                              <li><a class="text-white" href="#">Shop</a>
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


          <section class="about-company">
              <div class="container">
                  <div class="row align-items-center py-4">
                      <div class="col-12 col-md-12 col-lg-5 col-xl-5" data-aos="fade-up">
                          <div class="images">
                              <img class="img-fluid img-one"
                                  src="{{ asset('assets/frontend/images/01_home/about-company/image-01.png') }}"
                                  alt="">
                              <img class="img-fluid img-tow left-slider"
                                  src="{{ asset('assets/frontend/images/01_home/about-company/image-02.png') }}"
                                  alt="">
                          </div>
                      </div>
                      <div class="remove-div one-first col-12 col-md-12 col-lg-7 col-xl-7" data-aos="fade-up">
                          <div class="company-details">
                              <div class="semi-title">
                                  <div class="animated-circles">
                                      <div class="small-circle-start"></div>
                                      <span class="title">About Datartech Digital Solutions</span>
                                  </div>
                              </div>
                              <h2> <span class="cssanimation lePopUp sequence">Welcome to Datartech Digital Solutions</span> 
                                  <span class="cssanimation lePopUp sequence">Your Trusted Online Cyber Services
                                      Provider.</span>
                              </h2>
                              <p>At Datartech, we are committed to providing fast, secure, and reliable online services to
                                  both
                                  Kenyans and foreigners, wherever you are. Whether you’re working remotely from home, in
                                  the
                                  office, or living abroad — we’re here to serve you with convenience and professionalism.
                              </p>
                              <p>We simplify your access to essential services such as:</p>

                              <div class="company-list row row-cols-1 row-cols-md-2 row-cols-lg-2 row-cols-xl-2">
                                  <div class="col gap-3">
                                      <ul class="gap-3">
                                          <li> <span class="square"></span> <span>KRA Services</span>
                                          </li>
                                          <li> <span class="square"></span> <span>E-Citizen Services</span>
                                          </li>
                                          <li> <span class="square"></span> <span>NSSF/SHA Services </span></li>
                                      </ul>
                                  </div>
                                  <div class="col">
                                      <ul class="gap-3">
                                          <li> <span class="square"></span> <span>HELB/HEF Services </span>
                                          </li>
                                          <li> <span class="square"></span> <span>NTSA Services</span></li>
                                          <li> <span class="square"></span> <span>Other Services</span>
                                          </li>
                                      </ul>
                                  </div>
                              </div>
                            

                              <div class="company-list">

                                  <ul class="gap-3">
                                     
                                      <li> <span class="square"></span> <span>ICT Consultancy & Support Services</span>
                                      </li>
                                      <li> <span class="square"></span> <span>Networking & Structured Cabling</span></li>
                                      <li> <span class="square"></span> <span> Smart Digital Solutions for Modern
                                              Living</span></li>

                                  </ul>


                              </div>


                          </div>
                      </div>
                  </div>
              </div>
          </section>


      </main>
  @endsection
