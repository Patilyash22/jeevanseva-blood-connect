
import React from 'react';
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

  return (
    <div className="p-6 max-w-7xl mx-auto">
      <header className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Admin Panel</h1>
        <p className="text-gray-600 mt-1">Welcome back, {user?.username}</p>
      </header>

      <Tabs defaultValue={activeTab} className="w-full">
        <TabsList className="mb-6">
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
