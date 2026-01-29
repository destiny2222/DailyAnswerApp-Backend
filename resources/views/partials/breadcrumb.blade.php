<!--=============================
        BREADCRUMB START
    ==============================-->
    <section class="wsus__breadcrumb" style="background: url({{ asset('images/breadcrumb_image.jpg') }});">
        <div class="wsus__breadcrumb_overlay">
            <div class="container">
                <div class="wsus__breadcrumb_text">
                    <h1>@yield('title', '')</h1>
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="#">@yield('title', '')</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--=============================
        BREADCRUMB END
    ==============================-->