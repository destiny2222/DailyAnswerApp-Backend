@extends('layouts.main')
@section('content')

<!-- Breadcrumb Hero Section -->
<div class="breadcrumb-hero">
    <div class="breadcrumb-container">
        <h1 class="breadcrumb-title">Support</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Support</li>
            </ol>
        </nav>
    </div>
</div>


<div class="support-content">
    <form action="/submit-support" method="POST" class="support-form">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Enter your name" required>
        </div>
        <div class="form-group mt-3">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <div class="form-group mt-3">
            <label for="message">Message</label>
            <textarea id="message" name="message" class="form-control" rows="5" placeholder="Enter your message" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>
</div>


@endsection