
import { Donor } from '@/types/donor';

// Local storage keys
const DONORS_STORAGE_KEY = 'jeevanseva-donors';

// Get all donors from local storage
export const getDonors = (): Donor[] => {
  const donorsJson = localStorage.getItem(DONORS_STORAGE_KEY);
  return donorsJson ? JSON.parse(donorsJson) : [];
};

// Add a new donor to local storage
export const addDonor = (donor: Omit<Donor, 'id' | 'createdAt'>): Donor => {
  const donors = getDonors();
  
  const newDonor: Donor = {
    ...donor,
    id: crypto.randomUUID(),
    createdAt: new Date().toISOString(),
  };
  
  localStorage.setItem(DONORS_STORAGE_KEY, JSON.stringify([...donors, newDonor]));
  return newDonor;
};

// Search for donors by location and blood group
export const searchDonors = (location: string, bloodGroup: string): Donor[] => {
  const donors = getDonors();
  
  return donors.filter(donor => {
    const matchesLocation = donor.location.toLowerCase().includes(location.toLowerCase());
    const matchesBloodGroup = bloodGroup ? donor.bloodGroup === bloodGroup : true;
    
    return matchesLocation && matchesBloodGroup;
  });
};
