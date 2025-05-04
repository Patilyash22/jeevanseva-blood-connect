
// Testimonial carousel functionality
document.addEventListener('DOMContentLoaded', function() {
  const testimonialSlides = document.querySelectorAll('.testimonial-slide');
  const dotsContainer = document.querySelector('.carousel-dots');
  let currentSlide = 0;
  let interval;

  // Create dots based on number of slides
  if (testimonialSlides.length > 0 && dotsContainer) {
    testimonialSlides.forEach((_, index) => {
      const dot = document.createElement('span');
      dot.classList.add('dot');
      if (index === 0) dot.classList.add('active');
      dot.addEventListener('click', () => {
        showSlide(index);
      });
      dotsContainer.appendChild(dot);
    });
  }

  // Function to show a specific slide
  function showSlide(index) {
    // Hide all slides
    testimonialSlides.forEach(slide => {
      slide.classList.remove('active');
      slide.style.opacity = 0;
    });
    
    // Update dots
    const dots = document.querySelectorAll('.dot');
    dots.forEach(dot => dot.classList.remove('active'));
    if (dots[index]) dots[index].classList.add('active');
    
    // Show the selected slide
    testimonialSlides[index].classList.add('active');
    testimonialSlides[index].style.opacity = 1;
    
    currentSlide = index;
  }

  // Function to show the next slide
  function nextSlide() {
    let next = currentSlide + 1;
    if (next >= testimonialSlides.length) next = 0;
    showSlide(next);
  }

  // Function to start the automated slideshow
  function startSlideshow() {
    interval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
  }

  // Initialize the slideshow
  if (testimonialSlides.length > 0) {
    showSlide(0);
    startSlideshow();
    
    // Pause slideshow on hover
    const carousel = document.querySelector('.testimonial-carousel');
    if (carousel) {
      carousel.addEventListener('mouseenter', () => clearInterval(interval));
      carousel.addEventListener('mouseleave', startSlideshow);
    }
  }
});
