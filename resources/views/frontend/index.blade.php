  @extends('frontend.layouts.app')

  @section('content')
      {{-- @include('frontend.homepage.banner') --}}

      {{-- @include('frontend.homepage.banner-card') --}}

      @include('frontend.homepage.services')

      @include('frontend.homepage.other-services')
      
      @include('frontend.homepage.about-us')



      {{-- @include('frontend.homepage.testimonials') --}}
  @endsection
