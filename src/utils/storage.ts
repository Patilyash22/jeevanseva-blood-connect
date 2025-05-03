
import { Donor } from '@/types/donor';

// API endpoint for our donor database
const API_BASE_URL = 'https://api.jeevanseva.vnest.tech/api';

// Get all donors from database
export const getDonors = async (): Promise<Donor[]> => {
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
};

// Add a new donor to database
export const addDonor = async (donor: Omit<Donor, 'id' | 'createdAt'>): Promise<Donor> => {
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
    const newDonor: Donor = {
      ...donor,
      id: crypto.randomUUID(),
      createdAt: new Date().toISOString(),
    };
    
    // Store in local storage as fallback
    const donors = JSON.parse(localStorage.getItem('jeevanseva-donors') || '[]');
    localStorage.setItem('jeevanseva-donors', JSON.stringify([...donors, newDonor]));
    
    return newDonor;
  }
};

// Search for donors by location and blood group
export const searchDonors = async (location: string, bloodGroup: string): Promise<Donor[]> => {
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
    
    return donors.filter((donor: Donor) => {
      const matchesLocation = donor.location.toLowerCase().includes(location.toLowerCase());
      const matchesBloodGroup = bloodGroup ? donor.bloodGroup === bloodGroup : true;
      
      return matchesLocation && matchesBloodGroup;
    });
  }
};
