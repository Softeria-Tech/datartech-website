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
          @include('frontend.shop.banner')

          <section class="product-grids section" style="margin: 50px 0">
              <div class="container">
                  <div class="row">
                      <div class="col-lg-3 col-12">
                          <!-- Start Product Sidebar -->
                          <div class="product-sidebar">
                              <!-- Start Single Widget -->
                              <div class="single-widget search">
                                  <h3>Search Product</h3>
                                  <form action="#">
                                      <input type="text" placeholder="Search Here...">
                                      <button type="submit"><i class="lni lni-search-alt"></i></button>
                                  </form>
                              </div>
                              <!-- End Single Widget -->
                              <!-- Start Single Widget -->
                              <div class="single-widget">
                                  <h3>All Categories</h3>
                                  <ul class="list">
                                      @foreach ($product_categories as $product_category)
                                          <li>
                                              <a
                                                  href="#">{{ $product_category->category_name }}</a><span>({{ $product_category->products->count() }})</span>
                                          </li>
                                      @endforeach
                                  </ul>
                              </div>
                              <!-- End Single Widget -->

                          </div>
                          <!-- End Product Sidebar -->
                      </div>
                      <div class="col-lg-9 col-12">
                          <div class="product-grids-head">
                              <div class="product-grid-topbar">
                                  <div class="row align-items-center">
                                      <div class="col-lg-7 col-md-8 col-12">
                                          <div class="product-sorting">
                                              <h3 class="total-show-product"> Showing: <span> {{ $products->firstItem() }} -
                                                      {{ $products->lastItem() }} of {{ $products->total() }} items</span>
                                              </h3>
                                          </div>
                                      </div>
                                      <div class="col-lg-5 col-md-4 col-12">
                                          <nav>
                                              <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                  <button class="nav-link active" id="nav-grid-tab" data-bs-toggle="tab"
                                                      data-bs-target="#nav-grid" type="button" role="tab"
                                                      aria-controls="nav-grid" aria-selected="true"><i
                                                          class="lni lni-grid-alt"></i></button>
                                                  <button class="nav-link" id="nav-list-tab" data-bs-toggle="tab"
                                                      data-bs-target="#nav-list" type="button" role="tab"
                                                      aria-controls="nav-list" aria-selected="false"><i
                                                          class="lni lni-list"></i></button>
                                              </div>
                                          </nav>
                                      </div>
                                  </div>
                              </div>
                              <div class="tab-content" id="nav-tabContent">
                                  <div class="tab-pane fade show active" id="nav-grid" role="tabpanel"
                                      aria-labelledby="nav-grid-tab">
                                      <div class="row">
                                          @foreach ($products as $product)
                                              <div class="col-lg-4 col-md-6 col-12">

                                                  <!-- Start Single Product -->
                                                  <div class="single-product">
                                                      <div class="product-image">
                                                          <img src="{{ url('storage/' . $product->product_image) }}"
                                                              alt="#">
                                                          <div class="button">
                                                              <a href="{{ route('product-details', ['product_id' => $product->id]) }}"
                                                                  class="btn"><i class="fa fa-eye"></i> View Product</a>
                                                          </div>
                                                      </div>
                                                      <div class="product-info">
                                                          <span
                                                              class="category">{{ $product->productCategory->category_name ?? 'Uncategorized' }}</span>
                                                          <h4 class="title">
                                                              <a
                                                                  href="{{ route('product-details', ['product_id' => $product->id]) }}">{{ $product->product_name }}</a>
                                                          </h4>

                                                          <div class="price">
                                                              <span>Ksh.
                                                                  {{ number_format($product->product_cost, 2) }}</span>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <!-- End Single Product -->

                                              </div>
                                          @endforeach

                                      </div>
                                      <div class="row">
                                          <div class="col-12 mt-3">
                                              <!-- Pagination -->
                                              {{ $products->links('pagination::bootstrap-5') }}
                                              <!--/ End Pagination -->
                                          </div>
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
