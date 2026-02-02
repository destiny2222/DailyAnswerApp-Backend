@extends('layouts.main')
@section('content')

<!-- Breadcrumb Hero Section -->
<div class="breadcrumb-hero">
    <div class="breadcrumb-container">
        <h1 class="breadcrumb-title">Privacy Policy</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Privacy Policy</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Privacy Content -->
<div class="privacy-content">
    <!-- Introduction Box -->
    <div class="privacy-intro">
        <p>
            Welcome to The Daily Answer Devotional! Your privacy is important to us. This Policy explains how we collect, 
            use, disclose, and safeguard your information when you visit our mobile application. Please read this privacy 
            policy carefully. If you do not agree with the terms of this privacy policy, please do not access the app.
        </p>
    </div>

    <!-- Information Collection Section -->
    <div class="privacy-section">
        <h2 class="section-title">
            <span class="section-icon">1</span>
            Information Collection and Use
        </h2>
        <div class="section-content">
            <div class="subsection">
                <p class="subsection-title">Personal Data</p>
                <p>
                    We collect personal data that you voluntarily provide to us when registering at the app, expressing an 
                    interest in obtaining information about us or our products and services, when participating in activities 
                    on the app or otherwise contacting us. The personal data we collect may include your name, email address, 
                    phone number, and other contact details.
                </p>
            </div>

            <div class="subsection">
                <p class="subsection-title">Usage Data</p>
                <p>
                    When you access the app, we may also collect certain information automatically, including, but not limited 
                    to, the type of mobile device you use, your mobile device's unique ID, the IP address of your mobile device, 
                    your mobile operating system, the type of mobile Internet browser you use, unique device identifiers and 
                    other diagnostic data.
                </p>
            </div>

            <div class="subsection">
                <p class="subsection-title">Location Information</p>
                <p>
                    We may request access or permission to and track location-based information from your mobile device, either 
                    continuously or while you are using our mobile application, to provide location-based services.
                </p>
            </div>
        </div>
    </div>

    <!-- Cookies and Tracking Section -->
    <div class="privacy-section">
        <h2 class="section-title">
            <span class="section-icon">2</span>
            Cookies and Tracking Technologies
        </h2>
        <div class="section-content">
            <p>
                We use cookies and similar tracking technologies to track the activity on our app and hold certain information. 
                Tracking technologies used are beacons, tags, and scripts to collect and track information and to improve and 
                analyze our app.
            </p>
        </div>
    </div>

    <!-- Third-Party Services Section -->
    <div class="privacy-section">
        <h2 class="section-title">
            <span class="section-icon">3</span>
            Third-Party Service Providers
        </h2>
        <div class="section-content">
            <p>
                We may employ third-party companies and individuals to facilitate our app ("Service Providers"), to provide 
                the app on our behalf, to perform app-related services or to assist us in analyzing how our app is used.
            </p>
            <p>
                These third parties have access to your Personal Data only to perform these tasks on our behalf and are 
                obligated not to disclose or use it for any other purpose.
            </p>
        </div>
    </div>

    <!-- Data Security Section -->
    <div class="privacy-section">
        <h2 class="section-title">
            <span class="section-icon">4</span>
            Data Security
        </h2>
        <div class="section-content">
            <p>
                The security of your Personal Data is important to us, but remember that no method of transmission over the 
                Internet, or method of electronic storage is 100% secure. While we strive to use commercially acceptable means 
                to protect your Personal Data, we cannot guarantee its absolute security.
            </p>
        </div>
    </div>

    <!-- Your Rights Section -->
    <div class="privacy-section">
        <h2 class="section-title">
            <span class="section-icon">5</span>
            Your Privacy Rights
        </h2>
        <div class="section-content">
            <p>
                You have the right to access, update, or delete the information we have on you. You can contact us to request 
                access to, correct, or delete any personal information that you have provided to us.
            </p>
            <p>
                Please note that we may ask you to verify your identity before responding to such requests.
            </p>
        </div>
    </div>

    <!-- Changes to Policy Section -->
    <div class="privacy-section">
        <h2 class="section-title">
            <span class="section-icon">6</span>
            Changes to This Privacy Policy
        </h2>
        <div class="section-content">
            <p>
                We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new 
                Privacy Policy on this page and updating the "Last Updated" date.
            </p>
            <p>
                You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy 
                are effective when they are posted on this page.
            </p>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="privacy-section">
        <h2 class="section-title">
            <span class="section-icon">7</span>
            Contact Us
        </h2>
        <div class="section-content">
            <p>
                If you have any questions about this Privacy Policy, please contact us at 
                <a href="mailto:support@dailyanswer.com" style="color: #E94B7B; text-decoration: none;">support@dailyanswer.com</a>
            </p>
        </div>
    </div>

    <div class="last-updated">
        Last Updated: January 29, 2026
    </div>
</div>

@endsection