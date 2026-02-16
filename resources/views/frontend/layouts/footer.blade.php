  <!-- =====Footer section===== -->
  @php
      $service_categories = App\Models\ServiceCategory::with('services')->get();
  @endphp
  <footer>
      <div class="container">
          <div
              class="row row-cols-1 row-cols-md-1 row-cols-lg-2 row-cols-xl-2 row-cols-xxl-2 subscribe-field g-4 d-none">
              <div class="col ">
                  <div class="subscribe-content position-relative ">
                      <div class="divide-column position-absolute "></div>
                      <h3 class="tow ms-3 mb-1 text-white">Subscribe To Newsletter</h3>
                      <p class=" ms-3 text-white-50">Stay updated with our latest news and offers.</p>
                  </div>
              </div>
              <div class="col">
                  <form action="#" class="d-flex ">
                      <input type="email" name="email" id="footerEmail" placeholder="Enter Your Email Address"
                          required>
                      <button class="subscript" type="submit">Subscript</button>
                  </form>
              </div>
          </div>
          <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 row-cols-xl-4 g-4 our-details mt-3">
              <div class="col">
                  <div class="card h-100 bg-transparent border-0  ">
                      <div class="card-body px-0 py-3 ">
                          <p class="card-text text-white ">Datartech Digital Solutions provides fast and secure online cyber services in Kenya, including eCitizen help, KRA online assistance, NTSA TIMS account support, and other services such as NHIF, NSSF, HELB, TSC, and passport applications. As a trusted virtual cyber café, we serve Kenyans in the diaspora and foreigners worldwide with reliable digital solutions accessible anytime, anywhere.</p>
                      </div>
                      <div class="social-info">
                          <div class="position-relative">
                              <div class="divide-column position-absolute "></div>
                              <h4 class="ms-3 text-white ">Social Info</h4>
                          </div>
                          <ul class="d-flex mt-2 gap-3 ms-3  ">
                              <li><a class="text-white " href="#"><i
                                          class="link-icons fa-brands fa-facebook-f"></i></a></li>

                              <li><a class="text-white " href="#"><i
                                          class="link-icons fa-brands fa-instagram"></i></a></li>
                          </ul>
                      </div>
                  </div>
              </div>
              <div class="col">
                  <div class="card h-100 bg-transparent border-0">
                      <div class="industry-info">
                          <div class="position-relative">
                              <div class="divide-column position-absolute "></div>
                              <h4 class="ms-3 text-white ">Quick Links</h4>
                          </div>
                          <ul class="d-flex flex-column  mt-3 row-gap-2  ms-3  ">
                              @foreach ($service_categories as $service_category)
                                  <li><a class="text-white "
                                          href="{{ route('service-details', ['service_id' => $service_category->id]) }}">{{ $service_category->category_name }}</a>
                                  </li>
                              @endforeach
                              <li><a class="text-white " href="{{ route('shop') }}">Datartech Shop</a></li>
                          </ul>
                      </div>
                  </div>

              </div>
              <div class="col">
                  <div class="card h-100 bg-transparent border-0">
                      <div class="touch-info">
                          <div class="position-relative">
                              <div class="divide-column position-absolute "></div>
                              <h4 class="ms-3 text-white ">Get In Touch</h4>
                          </div>
                          <ul class="d-flex flex-column  mt-3 row-gap-2  ms-3  ">
                              <li class="text-white mb-2">Nairobi-Kenya</li>
                              <li>
                                  <a class="text-white" href="tel:+254711209948">
                                      <i class="bi bi-telephone phone"></i>
                                      +254 726 238 623
                                  </a>
                              </li>
                              <li> <a class="text-white" href="mailto:info@datartech.co.ke">
                                      <i class="bi bi-envelope email"></i>
                                      info@datartech.co.ke
                                  </a>
                              </li>
                          </ul>
                      </div>
                  </div>
              </div>
              <div class="col">
                  <div class="card h-100 bg-transparent border-0">
                      <div class="working-info">
                          <div class="position-relative">
                              <div class="divide-column position-absolute "></div>
                              <h4 class="ms-3 text-white ">Working Hours</h4>
                          </div>
                          <ul class="mt-3 ms-3 text-white   ">
                              <li>
                                  <div class="d-flex justify-content-between ">
                                      <div>Monday</div>
                                      <div>09.00hrs - 20.00hrs</div>
                                  </div>
                                  <hr>
                              </li>
                              <li>
                                  <div class="d-flex justify-content-between ">
                                      <div>Tuesday</div>
                                      <div>09.00hrs - 20.00hrs</div>
                                  </div>
                                  <hr>
                              </li>
                              <li>
                                  <div class="d-flex justify-content-between ">
                                      <div>Wednesday</div>
                                      <div>09.00hrs - 20.00hrs</div>
                                  </div>
                                  <hr>
                              </li>
                              <li>
                                  <div class="d-flex justify-content-between ">
                                      <div>Thursday</div>
                                      <div>09.00hrs - 20.00hrs</div>
                                  </div>
                                  <hr>
                              </li>
                              <li>
                                  <div class="d-flex justify-content-between ">
                                      <div>Friday</div>
                                     <div>09.00hrs - 20.00hrs</div>
                                  </div>
                                  <hr>
                              </li>
                              <li>
                                  <div class="d-flex justify-content-between ">
                                      <div>Saturday</div>
                                      <div>10.00hrs - 18.00hrs</div>
                                  </div>
                              </li>
                          </ul>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="term-and-condition">
          <div class="container">
              <div class="d-flex justify-content-between align-items-center copy-right-content ">
                  <div class="text-white copy-right"> Alrights reserved by <span><a
                              href="https://datartech.co.ke/">Datartech Digital Solutions</a></span> 

                      © 2025
                  </div>

                  <div class="button-div">
                      <a href="#" class="up-to-down-btn to-top"><i class="bi bi-chevron-double-up"></i></a>
                  </div>
                  <div class="text-white float-sm-end"> Designed by <a href="https://techsynclabs.co.ke/">Techsynclabs
                          Solutions</a> </div>
              </div>

          </div>
      </div>
  </footer>
