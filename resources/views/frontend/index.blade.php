@extends('layouts.main')
@section('content')
    <!-- Hero Section -->
    <section class="hero">
        <!-- Background Decorations -->
        <div class="bg-decoration decoration-1"></div>
        <div class="bg-decoration decoration-2"></div>

        <div class="hero-content">
            <!-- Announcement Badge -->
            <div class="announcement">
                <span class="announcement-icon">🎉</span>
                <span class="announcement-text">The Daily  Answer </span>
            </div>

            <!-- Main Heading -->
            <h1>Empower your life and loved ones with the Daily Answer</h1>

            <!-- Subtitle -->
            <p class="hero-subtitle">
                A Monthly Guide to Study, Memorize, and Apply the Word of God.
            </p>

            <!-- CTA Button -->
            <a href="#" class="cta-button">Get Started</a>
        </div>
    </section>

     <!-- Why Daily Answer Section -->
    <section class="why-section">
        <div class="container">
            <h2 class="section-title">Why Daily Answer?</h2>
            
            <div class="features-grid">
                <div class="feature-card">
                    <h3 class="feature-title">Scripture Focus</h3>
                    <p class="feature-description">Devotionals built around key Bible verses for each week.</p>
                </div>
                
                <div class="feature-card">
                    <h3 class="feature-title">Spiritual Growth</h3>
                    <p class="feature-description">Consistent encouragement to grow in faith, week by week.</p>
                </div>
                
                <div class="feature-card">
                    <h3 class="feature-title">Accessible Anywhere</h3>
                    <p class="feature-description">Available online, offline, and printable formats every month.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- What's Inside Section -->
    <section class="whats-inside-section">
        <div class="container">
            <h2 class="section-title">What's Inside Each Edition?</h2>
            
            <div class="inside-grid">
                <div class="inside-column">
                    <div class="inside-item">
                        <svg class="check-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 6L9 17L4 12" stroke="#E94B7B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Daily Scripture Devotionals</span>
                    </div>
                    
                    <div class="inside-item">
                        <svg class="check-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 6L9 17L4 12" stroke="#E94B7B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Reflections and Prayer Prompts</span>
                    </div>
                </div>
                
                <div class="inside-column">
                    <div class="inside-item">
                        <svg class="check-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 6L9 17L4 12" stroke="#E94B7B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Weekly Memory Verses</span>
                    </div>
                    
                    <div class="inside-item">
                        <svg class="check-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 6L9 17L4 12" stroke="#E94B7B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Printable Journal Pages</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Start Journey Section -->
    <section class="start-journey-section">
        <div class="container">
            <h2 class="journey-title">Start Your Devotional Journey</h2>
            <p class="journey-description">
                Subscribe now and receive a fresh edition every month with bonus guides and 
                faith-building material delivered right to your inbox.
            </p>
            <a href="#" class="subscribe-button">Subscribe Now</a>
        </div>
    </section>
@endsection