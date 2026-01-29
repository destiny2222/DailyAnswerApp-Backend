@extends('layouts.main')
@section('content')
    <!-- Breadcrumb Hero Section -->
    <div class="breadcrumb-hero">
        <div class="breadcrumb-container">
            <h1 class="breadcrumb-title">Subscribe </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Subscribe</li>
                </ol>
            </nav>
        </div>
    </div>


    <div class="support-content">
        
        <form action="{{ route('subscribe.checkout') }}" method="POST" class="support-form">
            @csrf

            <div class="form-group mt-3">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group mt-3">
                <label for="plan_id">Choose Plan</label>
                <select id="plan_id" name="plan_id" class="form-control" required>
                    @foreach ($subscriptions as $sub)
                        <option value="{{ $sub->id }}">
                            {{ $sub->name }} - ${{ number_format(($sub->price ?? 0) , 2) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Continue to payment</button>
        </form>

    </div>
@endsection
