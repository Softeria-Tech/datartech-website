  <!-- =====banner card===== -->
  <div class="banner-card" data-aos="fade-up">
      <div class="container">
          <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 row-cols-xl-4 row-cols-xxl-4 g-3">

              @foreach ($random_categories as $random_category)
                  <div class="col">
                      <div class="card h-100">
                          <img src="{{ asset('assets/frontend/images/multiple-use/banner-card/planning-animation.gif') }}"
                              class="card-img-top img-fluid" alt="...">
                          <div class="card-body">
                              <a href="service-detail.html">
                                  <h4 class="card-title">{{ $random_category->category_name }}</h4>
                              </a>
                              <p class="card-text">{{ $random_category->category_desc }}.</p>
                          </div>
                      </div>
                  </div>
              @endforeach

          </div>
          <form action="#" class="mt-5 m-auto ">
              <div class="d-flex banner-input-field input-field">
                  <input type="text" name="solution" id="solution"
                      placeholder="The official guide to living, working, visiting, and investing in Texas">
                  <a href="service-detail.html">Find Your Solution</a>
              </div>
          </form>
      </div>
  </div>
