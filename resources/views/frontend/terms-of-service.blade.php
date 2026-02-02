@extends('layouts.main')
@section('content')

<!-- Breadcrumb Hero Section -->
<div class="breadcrumb-hero">
    <div class="breadcrumb-container">
        <h1 class="breadcrumb-title">Terms of Service</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Terms of Service</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Terms Content -->
<div class="terms-content">
    <!-- Introduction Box -->
    <div class="terms-intro">
        <p>
            Welcome to The Daily Answer Devotional! By accessing our app, you agree to be bound by these Terms of Service. 
            If you do not agree with any part of these terms, please do not use our app.
        </p>
    </div>

    <!-- Eligibility Section -->
    <div class="terms-section">
        <h2 class="section-title">
            <span class="section-icon">1</span>
            Eligibility
        </h2>
        <div class="section-content">
            <p>
                You must be at least 13 years old to use our app. By agreeing to these Terms, you represent and warrant 
                that you meet the minimum age requirement.
            </p>
        </div>
    </div>

    <!-- Account Registration Section -->
    <div class="terms-section">
        <h2 class="section-title">
            <span class="section-icon">2</span>
            Account Registration
        </h2>
        <div class="section-content">
            <p>
                To access certain features of the app, you may need to create an account. You are responsible for 
                maintaining the confidentiality of your account credentials and for all activities that occur under your account.
            </p>
        </div>
    </div>

    <!-- User Conduct Section -->
    <div class="terms-section">
        <h2 class="section-title">
            <span class="section-icon">3</span>
            User Conduct
        </h2>
        <div class="section-content">
            <p>
                You are solely responsible for your conduct while using our app and for any data, text, information, 
                usernames, graphics, images, photographs, profiles, audio, video, items, and links (collectively, "Content") 
                that you submit, post, and display on our app.
            </p>
        </div>
    </div>

    <!-- Prohibited Activities Section -->
    <div class="terms-section">
        <h2 class="section-title">
            <span class="section-icon">4</span>
            Prohibited Activities
        </h2>
        <div class="section-content">
            <p>You agree not to engage in the following prohibited activities:</p>
            <ul>
                <li>Illegal or unauthorized use of our app</li>
                <li>Collecting usernames and/or email addresses of users by electronic or other means for the purpose of sending unsolicited email</li>
                <li>Divesting user accounts by automated means or under false pretenses</li>
                <li>Transmitting any worms or viruses or any code of a destructive nature</li>
            </ul>
        </div>
    </div>

    <!-- Intellectual Property Section -->
    <div class="terms-section">
        <h2 class="section-title">
            <span class="section-icon">5</span>
            Intellectual Property
        </h2>
        <div class="section-content">
            <p>
                The app and its original content, features, and functionality are owned by Daily Answer Devotional and are 
                protected by international copyright, trademark, patent, trade secret, and other intellectual property or 
                proprietary rights laws.
            </p>
        </div>
    </div>

    <!-- Changes to Terms Section -->
    <div class="terms-section">
        <h2 class="section-title">
            <span class="section-icon">6</span>
            Changes to Terms
        </h2>
        <div class="section-content">
            <p>
                We reserve the right to modify or replace these Terms at any time. If a revision is material, we will 
                provide at least 30 days' notice prior to any new terms taking effect. What constitutes a material change 
                will be determined at our sole discretion.
            </p>
        </div>
    </div>

    <div class="last-updated">
        Last Updated: January 29, 2026
    </div>
</div>

@endsection