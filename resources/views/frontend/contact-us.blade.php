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
      <main class="contact">
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
                                          <h4 class="text-white">Get in touch
                                          </h4>
                                          <h1 class="text-white text-uppercase  my-3">Contact Us</h1>
                                      </div>
                                      <div class="home-about-link">
                                          <ul>
                                              <li><a class="text-white" href="{{ route('index') }}">Home</a></li>
                                              <li class="text-white"> &nbsp;>&nbsp; </li>
                                              <li><a class="text-white" href="#">Contact Us</a>
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

   <!-- =====Contact section===== -->
        <section data-aos="fade-up">
            <div class="container">
                <div class="contact-information">
                    <div class="row align-items-center g-5">
                        <div class="col-md-12 col-lg-4 col-xl-4 contact-info py-5 px-4" data-aos="fade-right">
                            <h2>Contact Information</h2>
                            <div class="info-item d-flex align-items-center">
                                <div class="contact-icon"><i class="bi bi-geo-alt"></i></div>
                                <div>
                                    <p>Address</p>
                                    <span>Nairobi-Kenya</span>
                                </div>
                            </div>
                            <div class="info-item d-flex align-items-center">
                                <div class="contact-icon">
                                    <i class="bi bi-telephone phone"></i>
                                </div>
                                <div>
                                    <p>Contact Number</p>
                                    <span><a href="tel:+254711209948">                                  
                                  +254 726 238 623
                              </a></span>
                                </div>
                            </div>
                            <div class="info-item d-flex align-items-center">
                                <div class="contact-icon">
                                    <i class="bi bi-envelope email"></i>
                                </div>
                                <div>
                                    <p>Email Us</p>
                                    <span><a href="mailto:info@datartech.co.ke">
                                            info@datartech.co.ke
                                        </a></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-8 col-xl-8 get-quote" data-aos="fade-left">
                            <h2>Get A Quote</h2>
                            <form id="contactForm">
                                <div class="two-group d-flex gap-3 my-3">
                                    <div class="form-group">
                                        <label for="name">Your Name</label>
                                        <input type="text" class="form-control" id="name" placeholder="Your Name">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" class="form-control" id="email" placeholder="Email Address">
                                    </div>
                                </div>
                                <div class="two-group d-flex gap-3 my-3">
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" placeholder="Phone Number">
                                    </div>
                                    <div class="form-group">
                                        <label for="subject">Subject</label>
                                        <input type="text" class="form-control" id="subject" placeholder="Subject">
                                    </div>
                                </div>
                                <div class="form-group my-3">
                                    <label for="company">Select Service</label>
                                    <select name="service" id="" class="form-control">
                                        <option value="kra_services">KRA Services</option>
                                        <option value="ecitizen_services">E-Citizen Services</option>
                                        <option value="sha_services">SHA/SHIF Serrvices</option>
                                        <option value="helb_services">HELB Services</option>
                                        <option value="ntsa_services">NTSA Services</option>
                                        <option value="tsc_services">TSC Services</option>
                                        <option value="design_services">Graphic Design Services</option>
                                        <option value="other_services">Other Services</option>
                                    </select>

                                </div>
                                <div class="form-group my-3">
                                    <label for="message">Write your question here</label>
                                    <textarea class="form-control" id="message" rows="4"
                                        placeholder="Write your question here"></textarea>
                                </div>
                                <div class="cityWall-btn mt-4">
                                    <button type="submit" class="text-white">Get Started</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- =====Google maps===== -->
        <section class="google-map">
            <h2 class="d-none">Location</h2>
            <div class="container-fluid p-0">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.0751972162348!2d36.99087137600418!3d-1.1058550354576802!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f4789a2c2c5c9%3A0x41dad592893903a1!2sSummertime%20krd!5e0!3m2!1sen!2ske!4v1754387953856!5m2!1sen!2ske" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </section>


      </main>
  @endsection
