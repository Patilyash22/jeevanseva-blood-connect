
/**
 * JeevanSeva Database Utility
 * Provides consistent interface for data operations with API and local fallback
 */

// API endpoint for our donor database
const API_BASE_URL = 'https://api.jeevanseva.vnest.tech/api';

/**
 * Get all donors from database
 * @returns {Promise<Array>} Array of donor objects
 */
async function getAllDonors() {
  try {
    const response = await fetch(`${API_BASE_URL}/donors`);
    if (!response.ok) {
      throw new Error('Failed to fetch donors');
    }
    return await response.json();
  } catch (error) {
    console.error('Error fetching donors:', error);
    // Fall back to local storage
    return JSON.parse(localStorage.getItem('jeevanseva-donors') || '[]');
  }
}

/**
 * Add a new donor to database
 * @param {Object} donor - Donor object with all required fields
 * @returns {Promise<Object>} Added donor object with ID
 */
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
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || 'Failed to add donor');
    }
    
    return await response.json();
  } catch (error) {
    console.error('Error adding donor:', error);
    
    // For demo purposes only - in production, should retry API call
    // or queue for later synchronization
    const newDonor = {
      ...donor,
      id: generateUniqueId(),
      createdAt: new Date().toISOString(),
    };
    
    // Store in local storage as temporary fallback
    const donors = JSON.parse(localStorage.getItem('jeevanseva-donors') || '[]');
    localStorage.setItem('jeevanseva-donors', JSON.stringify([...donors, newDonor]));
    
    return newDonor;
  }
}

/**
 * Search for donors by location and blood group
 * @param {string} location - Location to search for
 * @param {string} bloodGroup - Blood group to search for
 * @returns {Promise<Array>} Array of matching donor objects
 */
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
    
    // Fall back to local filtering
    const donors = JSON.parse(localStorage.getItem('jeevanseva-donors') || '[]');
    
    return donors.filter((donor) => {
      const matchesLocation = !location || donor.location.toLowerCase().includes(location.toLowerCase());
      const matchesBloodGroup = !bloodGroup || donor.bloodGroup === bloodGroup;
      
      return matchesLocation && matchesBloodGroup;
    });
  }
}

/**
 * Generate a simple unique ID
 * @returns {string} Unique ID
 */
function generateUniqueId() {
  return Date.now().toString(36) + Math.random().toString(36).substr(2);
}

// Export database functions
window.jeevanSevaDB = {
  getAllDonors,
  addDonor,
  searchDonors
};
