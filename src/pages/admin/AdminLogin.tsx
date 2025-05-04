
import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';

const AdminLogin = () => {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const navigate = useNavigate();

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    
    // For demo purposes, accept any admin/admin123 login
    if (username === 'admin' && password === 'admin123') {
      // Simulate API call
      setTimeout(() => {
        setIsLoading(false);
        // In a real app, you'd store a token from your authentication service
        localStorage.setItem('adminLoggedIn', 'true');
        navigate('/admin');
      }, 1000);
    } else {
      setError('Invalid username or password');
      setIsLoading(false);
    }
  };

  return (
    <div className="h-screen flex items-center justify-center bg-gray-100">
      <Card className="w-full max-w-md border-none shadow-lg">
        <CardHeader className="space-y-1 items-center text-center border-b p-6">
          <div className="flex items-center justify-center mb-2">
            <div className="blood-drop" style={{ width: 30, height: 30 }}></div>
          </div>
          <CardTitle className="text-2xl font-bold text-jeevanseva-darkred">JeevanSeva Admin</CardTitle>
          <CardDescription className="text-gray-500">
            Enter your credentials to access the admin panel
          </CardDescription>
        </CardHeader>
        
        <form onSubmit={handleLogin}>
          <CardContent className="space-y-4 p-6">
            {error && (
              <div className="bg-red-50 p-3 rounded-md text-red-500 text-sm border border-red-200">
                {error}
              </div>
            )}
            
            <div className="space-y-2">
              <Label htmlFor="username">Username</Label>
              <Input 
                id="username"
                placeholder="Enter your username" 
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                required
                autoFocus
              />
            </div>
            
            <div className="space-y-2">
              <div className="flex items-center justify-between">
                <Label htmlFor="password">Password</Label>
                <a href="#" className="text-sm font-medium text-jeevanseva-red hover:text-jeevanseva-darkred">
                  Forgot password?
                </a>
              </div>
              <Input 
                id="password" 
                type="password" 
                placeholder="Enter your password" 
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
              />
            </div>
            
            <div className="flex items-center space-x-2">
              <Checkbox id="remember" />
              <label htmlFor="remember" className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                Remember me
              </label>
            </div>
          </CardContent>
          
          <CardFooter className="flex flex-col p-6 pt-0">
            <Button 
              type="submit" 
              className="w-full bg-jeevanseva-red hover:bg-jeevanseva-darkred"
              disabled={isLoading}
            >
              {isLoading ? "Authenticating..." : "Login to Admin Panel"}
            </Button>
            
            <div className="mt-4 text-center text-sm text-gray-500">
              Back to <a href="/" className="text-jeevanseva-red hover:text-jeevanseva-darkred">Main Website</a>
            </div>
          </CardFooter>
        </form>
      </Card>
    </div>
  );
};

export default AdminLogin;
