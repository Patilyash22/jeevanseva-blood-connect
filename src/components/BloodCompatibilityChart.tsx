
import React, { useState } from 'react';
import { BloodGroup, bloodGroups } from '@/types/donor';

const BloodCompatibilityChart = () => {
  const [selectedBloodGroup, setSelectedBloodGroup] = useState<BloodGroup | null>(null);

  // Blood compatibility data
  const compatibilityMap: Record<BloodGroup, { canDonateTo: BloodGroup[], canReceiveFrom: BloodGroup[] }> = {
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

  const getCompatibilityClass = (group: BloodGroup, type: 'donate' | 'receive') => {
    if (!selectedBloodGroup) return '';
    
    const isCompatible = type === 'donate' 
      ? compatibilityMap[selectedBloodGroup].canDonateTo.includes(group)
      : compatibilityMap[selectedBloodGroup].canReceiveFrom.includes(group);
    
    return isCompatible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
  };

  return (
    <div className="w-full">
      <div className="mb-6">
        <h3 className="text-xl font-semibold mb-3">Select Your Blood Group:</h3>
        <div className="flex flex-wrap gap-2">
          {bloodGroups.map((group) => (
            <button
              key={group}
              onClick={() => setSelectedBloodGroup(group)}
              className={`px-4 py-2 rounded-full border font-medium transition-all ${
                selectedBloodGroup === group
                  ? 'bg-jeevanseva-red text-white border-jeevanseva-red'
                  : 'border-jeevanseva-red text-jeevanseva-red hover:bg-jeevanseva-light'
              }`}
            >
              {group}
            </button>
          ))}
        </div>
      </div>

      <div className="overflow-x-auto">
        <table className="w-full bg-white shadow-md rounded-lg overflow-hidden">
          <thead className="bg-jeevanseva-red text-white">
            <tr>
              <th className="py-3 px-4 text-left">Blood Group</th>
              <th className="py-3 px-4 text-left">Can Donate To</th>
              <th className="py-3 px-4 text-left">Can Receive From</th>
            </tr>
          </thead>
          <tbody>
            {bloodGroups.map((group) => (
              <tr 
                key={group} 
                className={`border-b border-gray-200 ${
                  selectedBloodGroup === group ? 'bg-jeevanseva-light' : ''
                }`}
              >
                <td className="py-3 px-4 font-semibold">{group}</td>
                <td className={`py-3 px-4 ${selectedBloodGroup ? getCompatibilityClass(group, 'donate') : ''}`}>
                  {compatibilityMap[group].canDonateTo.join(', ')}
                </td>
                <td className={`py-3 px-4 ${selectedBloodGroup ? getCompatibilityClass(group, 'receive') : ''}`}>
                  {compatibilityMap[group].canReceiveFrom.join(', ')}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {selectedBloodGroup && (
        <div className="mt-6 p-4 rounded-lg bg-jeevanseva-light">
          <h4 className="font-semibold text-lg mb-2">Blood Group {selectedBloodGroup} Compatibility:</h4>
          <p className="mb-2">
            <span className="font-medium">Can donate to: </span>
            <span className="bg-green-100 text-green-800 px-2 py-1 rounded">
              {compatibilityMap[selectedBloodGroup].canDonateTo.join(', ')}
            </span>
          </p>
          <p>
            <span className="font-medium">Can receive from: </span>
            <span className="bg-green-100 text-green-800 px-2 py-1 rounded">
              {compatibilityMap[selectedBloodGroup].canReceiveFrom.join(', ')}
            </span>
          </p>
        </div>
      )}
    </div>
  );
};

export default BloodCompatibilityChart;
