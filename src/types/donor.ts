
export interface Donor {
  id: string;
  name: string;
  age: number;
  gender: string;
  location: string;
  bloodGroup: string;
  phone: string;
  email: string;
  createdAt: string;
}

export type BloodGroup = 'A+' | 'A-' | 'B+' | 'B-' | 'AB+' | 'AB-' | 'O+' | 'O-';

export const bloodGroups: BloodGroup[] = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
