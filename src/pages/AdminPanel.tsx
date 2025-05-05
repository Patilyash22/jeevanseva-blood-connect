
import React, { useState } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import DonorManagement from '@/components/admin/DonorManagement';
import UserManagement from '@/components/admin/UserManagement';
import AdminDashboard from '@/components/admin/AdminDashboard';
import AdminSettings from '@/components/admin/AdminSettings';

interface AdminPanelProps {
  activeTab?: string;
}

const AdminPanel: React.FC<AdminPanelProps> = ({ activeTab = "dashboard" }) => {
  const { user } = useAuth();
  const [currentTab, setCurrentTab] = useState(activeTab);

  // Handle tab change to optimize rendering
  const handleTabChange = (value: string) => {
    setCurrentTab(value);
  };

  return (
    <div className="p-4 md:p-6 max-w-7xl mx-auto">
      <header className="mb-6 md:mb-8">
        <h1 className="text-2xl md:text-3xl font-bold text-gray-900">Admin Panel</h1>
        <p className="text-gray-600 mt-1">Welcome back, {user?.username}</p>
      </header>

      <Tabs 
        defaultValue={currentTab} 
        className="w-full"
        onValueChange={handleTabChange}
      >
        <TabsList className="mb-6 overflow-x-auto flex w-full md:w-auto">
          <TabsTrigger value="dashboard">Dashboard</TabsTrigger>
          <TabsTrigger value="donors">Donor Management</TabsTrigger>
          <TabsTrigger value="users">User Management</TabsTrigger>
          <TabsTrigger value="settings">Settings</TabsTrigger>
        </TabsList>
        
        <TabsContent value="dashboard">
          <AdminDashboard />
        </TabsContent>
        
        <TabsContent value="donors">
          <DonorManagement />
        </TabsContent>
        
        <TabsContent value="users">
          <UserManagement />
        </TabsContent>
        
        <TabsContent value="settings">
          <AdminSettings />
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default AdminPanel;
