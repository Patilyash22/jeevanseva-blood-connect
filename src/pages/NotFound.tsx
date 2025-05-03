
import { useLocation, Link } from "react-router-dom";
import { useEffect } from "react";

const NotFound = () => {
  const location = useLocation();

  useEffect(() => {
    console.error(
      "404 Error: User attempted to access non-existent route:",
      location.pathname
    );
  }, [location.pathname]);

  return (
    <div className="min-h-screen flex items-center justify-center bg-jeevanseva-light">
      <div className="text-center px-4 py-12">
        <div className="blood-drop mx-auto mb-4 w-20 h-20"></div>
        <h1 className="text-5xl font-bold mb-4 text-jeevanseva-red">404</h1>
        <p className="text-xl text-jeevanseva-darkred mb-6">Oops! Page not found</p>
        <p className="text-jeevanseva-gray max-w-md mx-auto mb-8">
          The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
        </p>
        <Link 
          to="/" 
          className="bg-jeevanseva-red hover:bg-jeevanseva-darkred text-white py-3 px-8 rounded-md font-medium transition inline-block"
        >
          Return to Home
        </Link>
      </div>
    </div>
  );
};

export default NotFound;
