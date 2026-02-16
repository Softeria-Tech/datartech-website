  @extends('frontend.layouts.app')
  @section('content')
      <style>
          img {
              transition: .5s;
          }

          .product-sorting .pagination {
              display: none;
          }

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
      </style>
      <main class="service-detail">
          @include('frontend.shop.banner')

          <section class="item-details section">
              <div class="container">
                  <div class="top-area">
                      <a href="{{ route('shop') }}" class="btn btn-secondary float-end" style="margin-top: -1rem"><i
                              class="fa fa-angle-left"></i>Back</a>
                      <div class="row align-items-center mt-3">
                          <div class="col-lg-6 col-md-12 col-12">
                              <div class="product-images">
                                  <main id="gallery">
                                      <div class="main-img">
                                          <img src="{{ url('storage/' . $product_details->product_image) }}" id="current"
                                              alt="#">
                                      </div>
                                  </main>
                              </div>
                          </div>
                          <div class="col-lg-6 col-md-12 col-12">
                              <div class="product-info">
                                  <h2 class="title">{{ $product_details->product_name }}</h2>
                                  <p class="category"><i class="lni lni-tag"></i><a
                                          href="{{ route('product-categories', ['category_id' => $product_details->product_category_id]) }}">{{ $product_details->productCategory->category_name }}
                                      </a></p>
                                  <h3 class="price">{{ number_format($product_details->product_cost, 2) }}</span></h3>

                                  <p class="info-text">
                                      {{ \Illuminate\Support\Str::limit(strip_tags($product_details->product_desc), 120, '...') }}
                                  </p>
                                  <div class="bottom-content">
                                      <div class="row align-items-end">
                                          <div class="col-lg-4 col-md-4 col-12">
                                              @php
                                                  $whatsappNumber = '254726238623';
                                                  $productName = $product_details->product_name;
                                                  $message = urlencode(
                                                      "Hello, I am interested in purchasing: {$productName} as listed on your Website...",
                                                  );
                                                  $whatsappLink = "https://wa.me/{$whatsappNumber}?text={$message}";
                                              @endphp
                                              <div class="button cart-button">
                                                  <a href="{{ $whatsappLink }}" target="_blank">
                                                      <button class="btn" style="width: 100%;">Buy</button>
                                                  </a>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="product-details-info">
                      <div class="single-block">
                          <div class="row">
                              <div class="col-lg-6 col-12">
                                  <div class="info-body custom-responsive-margin">
                                      <div class="company-list category-desc-wrapper">
                                          {!! $product_details->product_desc !!}
                                      </div>
                                  </div>
                              </div>
                              <div class="col-lg-6 col-12">
                                  <div class="info-body">
                                      <h4>Specifications</h4>
                                      <div class="company-list category-desc-wrapper">
                                          {!! $product_details->product_specs !!}
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
