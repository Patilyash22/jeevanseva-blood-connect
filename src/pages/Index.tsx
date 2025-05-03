
import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import HomePage from './HomePage';

const Index = () => {
  const navigate = useNavigate();

  useEffect(() => {
    // This effect ensures users on the root path see the HomePage component
    if (window.location.pathname === '/') {
      navigate('/', { replace: true });
    }
  }, [navigate]);

  return <HomePage />;
};

export default Index;
