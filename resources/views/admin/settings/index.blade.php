
@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Site Settings</h2>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="tabs">
            <div class="tab-header">
                <button class="tab-btn active" onclick="openTab(event, 'general')">General Settings</button>
                <button class="tab-btn" onclick="openTab(event, 'display')">Display Settings</button>
                <button class="tab-btn" onclick="openTab(event, 'contact')">Contact Information</button>
                <button class="tab-btn" onclick="openTab(event, 'credits')">Credit System</button>
            </div>
            
            <!-- General Settings Tab -->
            <div id="general" class="tab-content" style="display: block;">
                <form class="admin-form" method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <input type="hidden" name="setting_type" value="general">
                    
                    <div class="form-group">
                        <label for="site_title">Site Title</label>
                        <input type="text" id="site_title" name="site_title" value="{{ $settings['site_title'] ?? '' }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_tagline">Site Tagline</label>
                        <input type="text" id="site_tagline" name="site_tagline" value="{{ $settings['site_tagline'] ?? '' }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="hero_description">Hero Description</label>
                        <textarea id="hero_description" name="hero_description">{{ $settings['hero_description'] ?? '' }}</textarea>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
            
            <!-- Display Settings Tab -->
            <div id="display" class="tab-content">
                <form class="admin-form" method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <input type="hidden" name="setting_type" value="display">
                    
                    <div class="form-group checkbox-group">
                        <label class="toggle-switch">
                            <input type="checkbox" name="show_donor_count" {{ ($settings['show_donor_count'] ?? '0') === '1' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <label>Show Donor Count Section on Homepage</label>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label class="toggle-switch">
                            <input type="checkbox" name="show_testimonials" {{ ($settings['show_testimonials'] ?? '0') === '1' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <label>Show Testimonials Section</label>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label class="toggle-switch">
                            <input type="checkbox" name="show_compatibility_matrix" {{ ($settings['show_compatibility_matrix'] ?? '0') === '1' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <label>Show Blood Compatibility Matrix</label>
                    </div>
                    
                    <div class="form-group">
                        <label for="current_theme">Current Theme</label>
                        <select id="current_theme" name="current_theme">
                            <option value="light" {{ ($settings['current_theme'] ?? '') === 'light' ? 'selected' : '' }}>Light Theme</option>
                            <option value="dark" {{ ($settings['current_theme'] ?? '') === 'dark' ? 'selected' : '' }}>Dark Theme</option>
                            <option value="grey" {{ ($settings['current_theme'] ?? '') === 'grey' ? 'selected' : '' }}>Grey Flat Theme</option>
                        </select>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
            
            <!-- Contact Information Tab -->
            <div id="contact" class="tab-content">
                <form class="admin-form" method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <input type="hidden" name="setting_type" value="contact">
                    
                    <div class="form-group">
                        <label for="contact_phone">Contact Phone</label>
                        <input type="text" id="contact_phone" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_email">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_address">Contact Address</label>
                        <textarea id="contact_address" name="contact_address">{{ $settings['contact_address'] ?? '' }}</textarea>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
            
            <!-- Credit System Tab -->
            <div id="credits" class="tab-content">
                <form class="admin-form" method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <input type="hidden" name="setting_type" value="credits">
                    
                    <div class="form-group">
                        <label for="donor_view_cost">Cost to View Donor Contact (Credits)</label>
                        <input type="number" min="1" step="1" id="donor_view_cost" name="donor_view_cost" value="{{ $settings['donor_view_cost'] ?? '2' }}">
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openTab(evt, tabId) {
    // Hide all tab contents
    var tabcontents = document.getElementsByClassName("tab-content");
    for (var i = 0; i < tabcontents.length; i++) {
        tabcontents[i].style.display = "none";
    }
    
    // Remove active class from all tab buttons
    var tablinks = document.getElementsByClassName("tab-btn");
    for (var i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    
    // Show the current tab and add an "active" class to the button
    document.getElementById(tabId).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>
@endpush

@push('styles')
<style>
.tabs {
    border: 1px solid var(--admin-border);
    border-radius: 4px;
    overflow: hidden;
}

.tab-header {
    display: flex;
    background-color: #f9fafb;
    border-bottom: 1px solid var(--admin-border);
    overflow-x: auto;
}

.tab-btn {
    padding: 12px 15px;
    border: none;
    background-color: transparent;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
    white-space: nowrap;
}

.tab-btn:hover {
    background-color: #edf2f7;
}

.tab-btn.active {
    border-bottom: 2px solid var(--admin-primary);
    color: var(--admin-primary);
}

.tab-content {
    display: none;
    padding: 20px;
}

.tab-content.active {
    display: block;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: var(--admin-primary);
}

input:focus + .toggle-slider {
    box-shadow: 0 0 1px var(--admin-primary);
}

input:checked + .toggle-slider:before {
    transform: translateX(26px);
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
}
</style>
@endpush
