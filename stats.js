
document.addEventListener('DOMContentLoaded', function() {
  // Animate the stats numbers
  const statNumbers = document.querySelectorAll('.stat-number');
  
  // Check if we have any stat numbers to animate
  if (statNumbers.length === 0) return;
  
  // Function to animate counting up
  const animateCounter = (element, target) => {
    // Get current value
    let current = 0;
    // Calculate animation duration based on target value
    const duration = Math.min(2000, Math.max(1000, target / 10));
    // Calculate increment per frame
    const increment = target / (duration / 16); // 16ms is roughly 60fps
    
    // Set up the animation
    const timer = setInterval(() => {
      current += increment;
      // Format the number with commas
      element.textContent = Math.floor(current).toLocaleString();
      
      // Stop the animation when we reach the target
      if (current >= target) {
        element.textContent = parseInt(target).toLocaleString();
        clearInterval(timer);
      }
    }, 16);
  };
  
  // Check if element is in viewport
  const isInViewport = (element) => {
    const rect = element.getBoundingClientRect();
    return (
      rect.top >= 0 &&
      rect.left >= 0 &&
      rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
  };
  
  // Set up intersection observer for detecting when stats are visible
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        // Get the target value from the data-count attribute
        const target = parseInt(entry.target.getAttribute('data-count'));
        // Start the animation
        animateCounter(entry.target, target);
        // Stop observing once animation starts
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });
  
  // Observe all stat numbers
  statNumbers.forEach(stat => {
    observer.observe(stat);
  });
});
