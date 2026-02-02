<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Daily Answer Devotional</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="stylesheet" href="{{ asset('main.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>

<body>
    <!-- Navbar -->
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <!-- Logo -->
            <a href="/" class="logo">
                <div class="logo-icon">
                    <img src="{{ asset('icon.png') }}" class="logo-icon" alt="">
                </div>
                <span class="logo-text">The Daily Answer</span>
            </a>

            <!-- Desktop Navigation Links -->
            <ul class="nav-center">
                <li><a href="/" class="active">Home</a></li>
                <li><a href="/about-us">About</a></li>
                <li><a href="/resources">Resources</a></li>
                <li><a href="/support">Support</a></li>
            </ul>

            <!-- Desktop Action Buttons -->
            <div class="nav-actions">
                <a href="/subscribe" class="btn btn-signup">Get Started</a>
            </div>

            <!-- Mobile Menu Toggle -->
            <div class="mobile-menu-toggle" id="mobileMenuToggle" >
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <ul class="mobile-nav-links">
                <li><a href="/" class="active">Home</a></li>
                <li><a href="/about-us">About</a></li>
                <li><a href="/resources">Resources</a></li>
                <li><a href="/support">Support</a></li>
            </ul>

            <div class="mobile-actions">
                <a href="/subscribe" class="btn btn-signup">Get Started</a>
            </div>
        </div>
    </nav>


    <div class="main-content">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-links">
                <a href="/about-us">About</a>
                <a href="/resources">Resources</a>
                <a href="/support">Support</a>
                <a href="/privacy-policy">Privacy Policy</a>
                <a href="/terms-of-service">Terms of Service</a>
            </div>
            <div class="footer-divider"></div>
            <p class="footer-copyright">© 2025 Daily Answer. All rights reserved.</p>
        </div>
    </footer>

    <script>
        const toggleBtn = document.getElementById("mobileMenuToggle");
        const mobileMenu = document.getElementById("mobileMenu");

        toggleBtn.addEventListener("click", () => {
            const isOpen = mobileMenu.classList.toggle("open");
            toggleBtn.setAttribute("aria-expanded", isOpen);

            // Switch icon
            toggleBtn.textContent = isOpen ? "✕" : "☰";
        });

        // Optional: close menu when clicking a link
        document.querySelectorAll(".mobile-nav-links a").forEach(link => {
            link.addEventListener("click", () => {
                mobileMenu.classList.remove("open");
                toggleBtn.setAttribute("aria-expanded", "false");
                toggleBtn.textContent = "☰";
            });
        });
    </script>
    @include('sweetalert::alert')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js"></script>
</body>
</html>
