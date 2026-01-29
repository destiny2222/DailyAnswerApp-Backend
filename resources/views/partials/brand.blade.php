@php
    $brands = App\Models\Partner::where('status', 1)->get();
@endphp

<!--=============================
        BRAND START
    ==============================-->
 <section class="wsus__brand"
     style="background: url(public/uploads/website-images/counter-bg-2023-03-06-09-34-36-3312.jpg);">
     <div class="wsus__brand_overlay">
         <div class="container">
             <div class="row brand_slider wow fadeInUp" data-wow-duration="1s">
                @foreach ($brands as $brand)
                    <div class="col-xl-2">
                        <a class="wsus__single_brand" href="javascript:;">
                         <img src="{{ asset('upload/partner/'.$brand->image) }}" alt="brand" class="img-fluid w-100">
                        </a>
                    </div>
                @endforeach
             </div>
         </div>
     </div>
 </section>
 <!--=============================
        BRAND END
    ==============================-->
