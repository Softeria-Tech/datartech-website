  <header>
      <div class="home-one">
          <nav class="navbar-details navbar navbar-expand-lg">
              <div class="container">
                  <div class="brand-logo">
                      <a class="navbar-brand" href="{{ route('index') }}">
                       <img src="{{ asset('assets/frontend/images/homepage/datar-tech.png') }}" class="img-fluid" alt="logo" style="max-height: 50px;">
                      </a>
                  </div>
                  <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse"
                      data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                      aria-expanded="false" aria-label="Toggle navigation">
                      <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="navigation-link collapse navbar-collapse gap-4" id="navbarSupportedContent">
                       <ul class="navbar-nav ms-auto">
                           <li class="nav-item">
                              <a class="nav-link fw-normal" href="{{ route('shop') }}"><span>Shop</span></a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link fw-normal" href="{{ route('library.resources') }}"><span>Resources</span></a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link fw-normal" href="{{ route('about-us') }}"><span>About</span></a>
                          </li>
                         
                          <li class="nav-item">
                              <a class="nav-link fw-normal" href="{{ route('contact-us') }}"><span>Contact</span></a>
                          </li>
                          @auth
                            <li class="nav-item">
                              <a class="nav-link fw-normal" href="{{ route('dashboard') }}"><span> <i class="fa-solid fa-user"></i> Dashboard</span></a>
                          </li>
                          @else
                            <li class="nav-item">
                              <a class="nav-link fw-normal" href="{{ route('login') }}"><span>Login</span></a>
                          </li> 
                          @endauth
                      </ul>
                      <div class="getStart-sideMenu d-flex align-items-center align-content-center justify-content-center justify-content-md-between gap-2 ">
                          <div class="d-flex align-items-center ms-sm-0 ms-lg-auto ms-xl-auto ms-xxl-auto cityWall-btn" role="search">
                              <a href="https://wa.me/254726238623?text=Hello%2C%20I%27m%20interested%20in%20your%20services">
                                <i class="fa-brands fa-whatsapp"></i>
                                </a>
                          </div>
                      </div>
                  </div>
              </div>
          </nav>
      </div>
  </header>
