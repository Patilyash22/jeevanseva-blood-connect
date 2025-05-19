
@extends('layouts.app')

@section('content')
<section class="find-donor-section">
    <div class="container">
        <h1>Find Blood Donors</h1>
        <p class="section-intro">Search for blood donors based on location and blood group.</p>
        
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <div class="search-form-container">
            <form method="GET" action="{{ route('donors.find') }}" class="search-form">
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="{{ $location }}" placeholder="Enter city, district, or area">
                </div>
                
                <div class="form-group">
                    <label for="blood_group">Blood Group</label>
                    <select id="blood_group" name="blood_group">
                        <option value="">Any Blood Group</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                            <option value="{{ $group }}" {{ $blood_group == $group ? 'selected' : '' }}>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
        
        @auth
            <div class="credit-info">
                <p>You have <strong>{{ Auth::user()->credits }} credits</strong>. Viewing a donor's contact information costs <strong>{{ $donor_view_cost }} credits</strong>.</p>
                <a href="{{ route('credits.buy') }}" class="btn btn-sm btn-outline">Buy Credits</a>
            </div>
        @endauth
        
        <div class="search-results">
            <h2>{{ count($donors) }} Donors Found</h2>
            
            @if(count($donors) > 0)
                <div class="donor-grid">
                    @foreach($donors as $donor)
                        <div class="donor-card">
                            <div class="donor-top">
                                <div class="donor-blood-group">{{ $donor->blood_group }}</div>
                                <div class="donor-location">
                                    <i class="fas fa-map-marker-alt"></i> {{ $donor->location }}
                                </div>
                            </div>
                            
                            <div class="donor-details">
                                <h3>{{ substr($donor->name, 0, 1) . '****' }}</h3>
                                <div class="donor-info">
                                    <p><strong>Age:</strong> {{ $donor->age }} years</p>
                                    <p><strong>Last Donation:</strong> 
                                        {{ $donor->last_donation_date ? $donor->last_donation_date->format('M d, Y') : 'Not Available' }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="donor-footer">
                                <form method="GET" action="{{ route('donors.view', $donor->id) }}">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-eye"></i> View Contact ({{ $donor_view_cost }} credits)
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <p>No donors found matching your criteria. Try broadening your search.</p>
                </div>
            @endif
        </div>
        
        <div class="blood-request-cta">
            <h3>Can't find a suitable donor?</h3>
            <p>Post a blood request and let donors contact you.</p>
            <a href="{{ route('blood-requests.create') }}" class="btn btn-primary">Post Blood Request</a>
        </div>
    </div>
</section>
@endsection
