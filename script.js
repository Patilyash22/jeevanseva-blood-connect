
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

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
  // Set up the compatibility table
  const compatibilityData = document.getElementById('compatibility-data');
  if (compatibilityData) {
    initializeBloodCompatibility();
  }
  
  // Initialize form submission handlers
  initializeForms();

  // Initialize FAQs if present
  initializeAccordions();

  // Check if we need to scroll to results (after page reload)
  if (sessionStorage.getItem('scrollToResults')) {
    const resultsElement = document.getElementById('search-results');
    if (resultsElement) {
      resultsElement.scrollIntoView({ behavior: 'smooth' });
    }
    sessionStorage.removeItem('scrollToResults');
  }
  
  // Check if there's a hash in URL for FAQ
  checkUrlHashForSection();
});

// Check if URL has hash to scroll to a specific section
function checkUrlHashForSection() {
  if (window.location.hash) {
    const targetElement = document.getElementById(window.location.hash.substring(1));
    if (targetElement) {
      setTimeout(() => {
        targetElement.scrollIntoView({ behavior: 'smooth' });
        
        // If it's an accordion item, open it
        if (targetElement.closest('.accordion-item')) {
          targetElement.closest('.accordion-item').classList.add('active');
        }
      }, 300);
    }
  }
}

// Initialize blood compatibility chart
function initializeBloodCompatibility() {
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

  // Select O- by default (universal donor)
  const defaultButton = document.querySelector('.blood-group-btn[data-group="O-"]');
  if (defaultButton) {
    defaultButton.click();
  }
}

// Initialize accordion functionality
function initializeAccordions() {
  const accordionItems = document.querySelectorAll('.accordion-item');
  
  accordionItems.forEach(item => {
    const header = item.querySelector('.accordion-header');
    if (header) {
      header.addEventListener('click', () => {
        // Toggle current item
        item.classList.toggle('active');
        
        // Close other items (optional)
        accordionItems.forEach(otherItem => {
          if (otherItem !== item && otherItem.classList.contains('active')) {
            otherItem.classList.remove('active');
          }
        });
      });
    }
  });
}

// Initialize form handlers
function initializeForms() {
  // Donor registration form
  const donorForm = document.getElementById('donor-registration-form');
  if (donorForm) {
    donorForm.addEventListener('submit', handleDonorRegistration);
  }
  
  // Find donor form
  const searchForm = document.getElementById('donor-search-form');
  if (searchForm) {
    searchForm.addEventListener('submit', handleDonorSearch);
  }

  // Contact form
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', handleContactForm);
  }
}

// Handle donor registration form submission
async function handleDonorRegistration(e) {
  e.preventDefault();
  
  if (!validateForm(this)) {
    return;
  }
  
  const formData = {
    name: this.name.value.trim(),
    age: parseInt(this.age.value),
    gender: this.gender.value,
    bloodGroup: this.bloodGroup.value,
    location: this.location.value.trim(),
    phone: this.phone.value.trim(),
    email: this.email.value.trim(),
    lastDonation: this.lastDonation ? this.lastDonation.value : null,
    medicalConditions: this.medicalConditions ? this.medicalConditions.value : null
  };
  
  try {
    showLoadingIndicator();
    // Use our database module
    const result = await window.jeevanSevaDB.addDonor(formData);
    hideLoadingIndicator();
    
    if (result && result.id) {
      showToast('Thank you! Your registration was successful.', 'success');
      this.reset();
      
      // Redirect after a delay
      setTimeout(() => {
        window.location.href = 'index.html';
      }, 2000);
    } else {
      throw new Error('Failed to register');
    }
  } catch (error) {
    hideLoadingIndicator();
    showToast('Registration failed: ' + error.message, 'error');
  }
}

// Handle donor search form submission
async function handleDonorSearch(e) {
  e.preventDefault();
  
  const location = this.location.value.trim();
  const bloodGroup = this.bloodGroup.value;
  
  if (!location && !bloodGroup) {
    showToast('Please enter a location or select a blood group', 'error');
    return;
  }
  
  try {
    showLoadingIndicator();
    // Use our database module
    const donors = await window.jeevanSevaDB.searchDonors(location, bloodGroup);
    hideLoadingIndicator();
    
    displaySearchResults(donors);
    
    // Store a flag to scroll to results if page reloads
    sessionStorage.setItem('scrollToResults', 'true');
    
    // Scroll to results
    const resultsElement = document.getElementById('search-results');
    if (resultsElement) {
      resultsElement.scrollIntoView({ behavior: 'smooth' });
    }
  } catch (error) {
    hideLoadingIndicator();
    showToast('Search failed: ' + error.message, 'error');
  }
}

// Handle contact form submission
function handleContactForm(e) {
  e.preventDefault();
  
  if (!validateForm(this)) {
    return;
  }
  
  // Get form data
  const formData = {
    name: document.getElementById('name').value.trim(),
    email: document.getElementById('email').value.trim(),
    subject: document.getElementById('subject').value,
    message: document.getElementById('message').value.trim(),
    timestamp: new Date().toISOString()
  };
  
  // Disable submit button and show loading state
  const submitBtn = document.getElementById('submitBtn');
  const btnText = submitBtn.querySelector('.btn-text');
  const btnIcon = submitBtn.querySelector('.btn-icon');
  const originalText = btnText.textContent;
  const originalIcon = btnIcon.innerHTML;
  
  submitBtn.disabled = true;
  btnText.textContent = 'Sending...';
  btnIcon.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  
  // Simulate sending the form data to a server
  setTimeout(() => {
    // In a real application, you would send this data to your server
    console.log('Form data:', formData);
    
    // Store in local storage for demo purposes
    const messages = JSON.parse(localStorage.getItem('jeevanseva-messages') || '[]');
    messages.push(formData);
    localStorage.setItem('jeevanseva-messages', JSON.stringify(messages));
    
    // Show success message
    showToast('Your message has been sent! We will get back to you soon.', 'success');
    
    // Reset form
    this.reset();
    
    // Re-enable submit button
    submitBtn.disabled = false;
    btnText.textContent = originalText;
    btnIcon.innerHTML = originalIcon;
  }, 1500);
}

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
    
    <div class="compatibility-note">
      <p><small>
        <i class="fas fa-info-circle"></i> 
        For emergency transfusions, please always consult healthcare professionals.
      </small></p>
    </div>
  `;
}

// Display search results
function displaySearchResults(donors) {
  const resultsContainer = document.getElementById('search-results');
  if (!resultsContainer) return;
  
  resultsContainer.innerHTML = '';
  
  if (donors.length === 0) {
    resultsContainer.innerHTML = `
      <div class="no-results">
        <div style="font-size: 3rem; margin-bottom: 20px;">😕</div>
        <h3 style="font-size: 1.3rem; margin-bottom: 15px;">No donors found</h3>
        <p class="mb-4">We couldn't find any donors matching your search criteria.</p>
        <div class="tips">
          <p>Try:</p>
          <ul style="list-style-type: disc; padding-left: 20px; text-align: left;">
            <li>Searching for a different location</li>
            <li>Searching for any blood group</li>
            <li>Using a broader location (e.g., city name instead of specific area)</li>
          </ul>
        </div>
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
      <h3 style="margin: 5px 0 10px;">${donor.name}, ${donor.age}</h3>
      <div class="donor-location">
        <i class="fas fa-map-marker-alt"></i> ${donor.location}
      </div>
      <div class="donor-details">
        ${donor.lastDonation ? `<p><strong>Last Donation:</strong> ${lastDonationText}</p>` : ''}
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
  const toastId = 'toast-' + Date.now();
  const toast = document.createElement('div');
  toast.id = toastId;
  toast.className = `toast toast-${type}`;
  
  // Set icon based on toast type
  let icon = '';
  if (type === 'success') {
    icon = '<span class="toast-icon"><i class="fas fa-check-circle"></i></span>';
  } else if (type === 'error') {
    icon = '<span class="toast-icon"><i class="fas fa-exclamation-circle"></i></span>';
  }
  
  toast.innerHTML = `${icon}<span>${message}</span>`;
  
  document.body.appendChild(toast);
  
  // Show the toast
  setTimeout(() => {
    toast.classList.add('show');
  }, 10);
  
  // Hide the toast after 4 seconds
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => {
      const toastElement = document.getElementById(toastId);
      if (toastElement) {
        document.body.removeChild(toastElement);
      }
    }, 300);
  }, 4000);
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
  errorElement.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
  
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
