
// Mobile menu toggle
function toggleMobileMenu() {
  const navbar = document.getElementById('navbar');
  const navList = navbar.querySelector('ul');
  navList.classList.toggle('show');
}

// Blood compatibility data
const compatibilityMap = {
  'A+': { 
    canDonateTo: ['A+', 'AB+'], 
    canReceiveFrom: ['A+', 'A-', 'O+', 'O-'] 
  },
  'A-': { 
    canDonateTo: ['A+', 'A-', 'AB+', 'AB-'], 
    canReceiveFrom: ['A-', 'O-'] 
  },
  'B+': { 
    canDonateTo: ['B+', 'AB+'], 
    canReceiveFrom: ['B+', 'B-', 'O+', 'O-'] 
  },
  'B-': { 
    canDonateTo: ['B+', 'B-', 'AB+', 'AB-'], 
    canReceiveFrom: ['B-', 'O-'] 
  },
  'AB+': { 
    canDonateTo: ['AB+'], 
    canReceiveFrom: ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] 
  },
  'AB-': { 
    canDonateTo: ['AB+', 'AB-'], 
    canReceiveFrom: ['A-', 'B-', 'AB-', 'O-'] 
  },
  'O+': { 
    canDonateTo: ['A+', 'B+', 'AB+', 'O+'], 
    canReceiveFrom: ['O+', 'O-'] 
  },
  'O-': { 
    canDonateTo: ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'], 
    canReceiveFrom: ['O-'] 
  }
};

const bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

// API base URL
const API_BASE_URL = 'https://api.jeevanseva.vnest.tech/api';

// Initialize the blood compatibility chart
document.addEventListener('DOMContentLoaded', function() {
  // Set up the compatibility table
  const compatibilityData = document.getElementById('compatibility-data');
  if (compatibilityData) {
    populateCompatibilityTable();
    
    // Add event listeners to blood group buttons
    const bloodGroupButtons = document.querySelectorAll('.blood-group-btn');
    bloodGroupButtons.forEach(button => {
      button.addEventListener('click', function() {
        const selectedGroup = this.getAttribute('data-group');
        
        // Remove active class from all buttons
        bloodGroupButtons.forEach(btn => btn.classList.remove('active'));
        
        // Add active class to clicked button
        this.classList.add('active');
        
        // Update compatibility highlighting
        updateCompatibilityHighlighting(selectedGroup);
        
        // Show compatibility summary
        updateCompatibilitySummary(selectedGroup);
      });
    });
  }
  
  // Initialize form submission handlers
  setupFormHandlers();
});

// Populate the compatibility table with all blood groups
function populateCompatibilityTable() {
  const tableBody = document.getElementById('compatibility-data');
  if (!tableBody) return;
  
  tableBody.innerHTML = '';
  
  bloodGroups.forEach(group => {
    const row = document.createElement('tr');
    row.setAttribute('data-group', group);
    
    const groupCell = document.createElement('td');
    groupCell.textContent = group;
    groupCell.classList.add('group-cell');
    groupCell.style.fontWeight = 'bold';
    
    const donateCell = document.createElement('td');
    donateCell.textContent = compatibilityMap[group].canDonateTo.join(', ');
    donateCell.classList.add('donate-cell');
    
    const receiveCell = document.createElement('td');
    receiveCell.textContent = compatibilityMap[group].canReceiveFrom.join(', ');
    receiveCell.classList.add('receive-cell');
    
    row.appendChild(groupCell);
    row.appendChild(donateCell);
    row.appendChild(receiveCell);
    tableBody.appendChild(row);
  });
}

// Update compatibility highlighting based on selected blood group
function updateCompatibilityHighlighting(selectedGroup) {
  if (!selectedGroup) return;
  
  const rows = document.querySelectorAll('#compatibility-data tr');
  
  rows.forEach(row => {
    const rowGroup = row.getAttribute('data-group');
    const donateCell = row.querySelector('.donate-cell');
    const receiveCell = row.querySelector('.receive-cell');
    
    // Highlight the selected group's row
    if (rowGroup === selectedGroup) {
      row.classList.add('highlighted');
    } else {
      row.classList.remove('highlighted');
    }
    
    // Check if the row group can donate to the selected group
    if (compatibilityMap[rowGroup].canDonateTo.includes(selectedGroup)) {
      donateCell.classList.add('compatible');
      donateCell.classList.remove('incompatible');
    } else {
      donateCell.classList.add('incompatible');
      donateCell.classList.remove('compatible');
    }
    
    // Check if the row group can receive from the selected group
    if (compatibilityMap[rowGroup].canReceiveFrom.includes(selectedGroup)) {
      receiveCell.classList.add('compatible');
      receiveCell.classList.remove('incompatible');
    } else {
      receiveCell.classList.add('incompatible');
      receiveCell.classList.remove('compatible');
    }
  });
}

// Update the compatibility summary
function updateCompatibilitySummary(selectedGroup) {
  const summaryDiv = document.getElementById('compatibility-summary');
  if (!summaryDiv) return;
  
  if (!selectedGroup) {
    summaryDiv.classList.add('hidden');
    return;
  }
  
  summaryDiv.classList.remove('hidden');
  
  const donateList = compatibilityMap[selectedGroup].canDonateTo.join(', ');
  const receiveList = compatibilityMap[selectedGroup].canReceiveFrom.join(', ');
  
  summaryDiv.innerHTML = `
    <h4>Blood Group ${selectedGroup} Compatibility:</h4>
    <p><strong>Can donate to: </strong><span class="compatibility-tag">${donateList}</span></p>
    <p><strong>Can receive from: </strong><span class="compatibility-tag">${receiveList}</span></p>
  `;
}

// Set up form handlers
function setupFormHandlers() {
  // Donor registration form
  const donorForm = document.getElementById('donor-registration-form');
  if (donorForm) {
    donorForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      if (!validateForm(donorForm)) {
        return;
      }
      
      const formData = {
        name: donorForm.name.value,
        age: parseInt(donorForm.age.value),
        bloodGroup: donorForm.bloodGroup.value,
        email: donorForm.email.value,
        phone: donorForm.phone.value,
        location: donorForm.location.value,
        lastDonation: donorForm.lastDonation ? donorForm.lastDonation.value : null,
        medicalConditions: donorForm.medicalConditions ? donorForm.medicalConditions.value : null
      };
      
      try {
        showLoadingIndicator();
        const result = await addDonor(formData);
        hideLoadingIndicator();
        
        if (result && result.id) {
          showToast('Donor registration successful!', 'success');
          donorForm.reset();
        } else {
          throw new Error('Failed to register');
        }
      } catch (error) {
        hideLoadingIndicator();
        showToast('Registration failed: ' + error.message, 'error');
      }
    });
  }
  
  // Find donor form
  const searchForm = document.getElementById('donor-search-form');
  if (searchForm) {
    searchForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const location = searchForm.location.value;
      const bloodGroup = searchForm.bloodGroup.value;
      
      if (!location && !bloodGroup) {
        showToast('Please enter a location or select a blood group', 'error');
        return;
      }
      
      try {
        showLoadingIndicator();
        const donors = await searchDonors(location, bloodGroup);
        hideLoadingIndicator();
        
        displaySearchResults(donors);
      } catch (error) {
        hideLoadingIndicator();
        showToast('Search failed: ' + error.message, 'error');
      }
    });
  }
}

// API functions
async function getDonors() {
  const response = await fetch(`${API_BASE_URL}/donors`);
  
  if (!response.ok) {
    throw new Error('Failed to fetch donors');
  }
  
  return await response.json();
}

async function addDonor(donor) {
  const response = await fetch(`${API_BASE_URL}/donors`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(donor),
  });
  
  if (!response.ok) {
    const errorData = await response.json();
    throw new Error(errorData.message || 'Failed to add donor');
  }
  
  return await response.json();
}

async function searchDonors(location, bloodGroup) {
  const queryParams = new URLSearchParams();
  if (location) queryParams.append('location', location);
  if (bloodGroup) queryParams.append('bloodGroup', bloodGroup);
  
  const response = await fetch(`${API_BASE_URL}/donors/search?${queryParams.toString()}`);
  
  if (!response.ok) {
    throw new Error('Failed to search donors');
  }
  
  return await response.json();
}

// Display search results
function displaySearchResults(donors) {
  const resultsContainer = document.getElementById('search-results');
  if (!resultsContainer) return;
  
  resultsContainer.innerHTML = '';
  
  if (donors.length === 0) {
    resultsContainer.innerHTML = `
      <div class="no-results">
        <p>No donors found matching your criteria.</p>
        <p>Please try broadening your search or check back later.</p>
      </div>
    `;
    return;
  }
  
  const resultsGrid = document.createElement('div');
  resultsGrid.className = 'results-grid';
  
  donors.forEach(donor => {
    const donorCard = document.createElement('div');
    donorCard.className = 'donor-card';
    
    // Calculate months since last donation
    const lastDonationText = donor.lastDonation 
      ? calculateTimeSince(donor.lastDonation) 
      : 'Not specified';
    
    donorCard.innerHTML = `
      <div class="donor-blood-group">${donor.bloodGroup}</div>
      <h3>${donor.name}, ${donor.age}</h3>
      <div class="donor-location">
        <i class="fas fa-map-marker-alt"></i> ${donor.location}
      </div>
      <div class="donor-details">
        <p><strong>Last Donation:</strong> ${lastDonationText}</p>
        <a href="mailto:${donor.email}" class="donor-contact">
          <i class="fas fa-envelope"></i> Contact via Email
        </a>
        <a href="tel:${donor.phone}" class="donor-contact">
          <i class="fas fa-phone"></i> ${donor.phone}
        </a>
      </div>
    `;
    
    resultsGrid.appendChild(donorCard);
  });
  
  resultsContainer.appendChild(resultsGrid);
}

// Calculate time since last donation
function calculateTimeSince(dateString) {
  const lastDonation = new Date(dateString);
  const now = new Date();
  
  const diffTime = Math.abs(now - lastDonation);
  const diffMonths = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 30));
  
  if (diffMonths < 1) {
    return 'Less than a month ago';
  } else if (diffMonths === 1) {
    return '1 month ago';
  } else {
    return `${diffMonths} months ago`;
  }
}

// Show loading indicator
function showLoadingIndicator() {
  let loader = document.querySelector('.loader-overlay');
  
  if (!loader) {
    loader = document.createElement('div');
    loader.className = 'loader-overlay';
    loader.innerHTML = '<div class="loader"></div>';
    document.body.appendChild(loader);
  }
  
  loader.style.display = 'flex';
}

// Hide loading indicator
function hideLoadingIndicator() {
  const loader = document.querySelector('.loader-overlay');
  if (loader) {
    loader.style.display = 'none';
  }
}

// Toast notifications
function showToast(message, type = 'success') {
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.textContent = message;
  
  document.body.appendChild(toast);
  
  // Show the toast
  setTimeout(() => {
    toast.classList.add('show');
  }, 10);
  
  // Hide the toast after 3 seconds
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => {
      document.body.removeChild(toast);
    }, 300);
  }, 3000);
}

// Form validation helper
function validateForm(form) {
  const inputs = form.querySelectorAll('[required]');
  let isValid = true;
  
  inputs.forEach(input => {
    if (!input.value.trim()) {
      isValid = false;
      showInputError(input, 'This field is required');
    } else {
      clearInputError(input);
      
      // Additional validation for specific fields
      if (input.type === 'email' && !isValidEmail(input.value)) {
        isValid = false;
        showInputError(input, 'Please enter a valid email address');
      }
      
      if (input.id === 'phone' && !isValidPhone(input.value)) {
        isValid = false;
        showInputError(input, 'Please enter a valid 10-digit phone number');
      }
      
      if (input.id === 'age') {
        const age = parseInt(input.value);
        if (isNaN(age) || age < 18 || age > 65) {
          isValid = false;
          showInputError(input, 'Age must be between 18 and 65');
        }
      }
    }
  });
  
  return isValid;
}

function showInputError(input, message) {
  const formGroup = input.closest('.form-group');
  
  // Remove any existing error message
  clearInputError(input);
  
  const errorElement = document.createElement('div');
  errorElement.className = 'error-message';
  errorElement.textContent = message;
  
  formGroup.appendChild(errorElement);
  formGroup.classList.add('error');
}

function clearInputError(input) {
  const formGroup = input.closest('.form-group');
  const existingError = formGroup.querySelector('.error-message');
  
  if (existingError) {
    formGroup.removeChild(existingError);
    formGroup.classList.remove('error');
  }
}

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function isValidPhone(phone) {
  const phoneRegex = /^\d{10}$/;
  return phoneRegex.test(phone);
}
