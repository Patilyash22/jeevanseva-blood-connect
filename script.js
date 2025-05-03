
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
});

// Populate the compatibility table with all blood groups
function populateCompatibilityTable() {
  const tableBody = document.getElementById('compatibility-data');
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

// API functions
async function getDonors() {
  try {
    const response = await fetch(`${API_BASE_URL}/donors`);
    if (!response.ok) {
      throw new Error('Failed to fetch donors');
    }
    return await response.json();
  } catch (error) {
    console.error('Error fetching donors:', error);
    return [];
  }
}

async function addDonor(donor) {
  try {
    const response = await fetch(`${API_BASE_URL}/donors`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(donor),
    });
    
    if (!response.ok) {
      throw new Error('Failed to add donor');
    }
    
    return await response.json();
  } catch (error) {
    console.error('Error adding donor:', error);
    // For now, fall back to local storage if API fails
    const newDonor = {
      ...donor,
      id: generateUUID(),
      createdAt: new Date().toISOString(),
    };
    
    // Store in local storage as fallback
    const donors = JSON.parse(localStorage.getItem('jeevanseva-donors') || '[]');
    localStorage.setItem('jeevanseva-donors', JSON.stringify([...donors, newDonor]));
    
    return newDonor;
  }
}

async function searchDonors(location, bloodGroup) {
  try {
    const queryParams = new URLSearchParams();
    if (location) queryParams.append('location', location);
    if (bloodGroup) queryParams.append('bloodGroup', bloodGroup);
    
    const response = await fetch(`${API_BASE_URL}/donors/search?${queryParams.toString()}`);
    
    if (!response.ok) {
      throw new Error('Failed to search donors');
    }
    
    return await response.json();
  } catch (error) {
    console.error('Error searching donors:', error);
    // Fall back to local storage if API fails
    const donors = JSON.parse(localStorage.getItem('jeevanseva-donors') || '[]');
    
    return donors.filter(donor => {
      const matchesLocation = donor.location.toLowerCase().includes(location.toLowerCase());
      const matchesBloodGroup = bloodGroup ? donor.bloodGroup === bloodGroup : true;
      
      return matchesLocation && matchesBloodGroup;
    });
  }
}

// Helper function to generate UUID
function generateUUID() {
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
    const r = (Math.random() * 16) | 0,
      v = c === 'x' ? r : (r & 0x3) | 0x8;
    return v.toString(16);
  });
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
