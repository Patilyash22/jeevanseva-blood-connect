
import React from 'react';
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious
} from '@/components/ui/carousel';

interface Testimonial {
  id: number;
  name: string;
  role: string;
  quote: string;
  avatar: string;
}

const testimonials: Testimonial[] = [
  {
    id: 1,
    name: "Rajesh Kumar",
    role: "Regular Donor",
    quote: "I've been donating blood every 3 months for the past 5 years. JeevanSeva makes the process simple and rewarding. Knowing I've helped save lives gives me immense satisfaction.",
    avatar: "/donor1.png"
  },
  {
    id: 2,
    name: "Priya Sharma",
    role: "First-time Donor",
    quote: "I was nervous about donating blood for the first time, but the JeevanSeva team made it a positive experience. The registration was easy and I received timely notifications.",
    avatar: "/donor2.png"
  },
  {
    id: 3,
    name: "Amit Patel",
    role: "Blood Recipient",
    quote: "During my emergency surgery, I needed blood urgently. Thanks to JeevanSeva, donors were found quickly. I'm forever grateful to this platform and the donors who saved my life.",
    avatar: "/donor3.png"
  },
  {
    id: 4,
    name: "Sunita Verma",
    role: "Hospital Administrator",
    quote: "JeevanSeva has been an invaluable partner for our hospital. Their quick response and wide donor network have helped us manage blood requirements efficiently during emergencies.",
    avatar: "/donor4.png"
  }
];

const TestimonialCarousel = () => {
  return (
    <Carousel
      opts={{
        align: "center",
        loop: true
      }}
      className="w-full max-w-5xl mx-auto"
    >
      <CarouselContent>
        {testimonials.map((testimonial) => (
          <CarouselItem key={testimonial.id} className="md:basis-1/2 lg:basis-1/3">
            <div className="bg-white p-6 rounded-xl shadow-md h-full flex flex-col">
              <div className="mb-4 flex items-center">
                <div className="w-12 h-12 rounded-full overflow-hidden mr-4 bg-jeevanseva-light flex items-center justify-center">
                  {testimonial.avatar ? (
                    <img 
                      src={testimonial.avatar} 
                      alt={testimonial.name} 
                      className="w-full h-full object-cover"
                    />
                  ) : (
                    <span className="text-xl font-bold text-jeevanseva-red">
                      {testimonial.name.charAt(0)}
                    </span>
                  )}
                </div>
                <div>
                  <h3 className="font-bold text-jeevanseva-darkred">{testimonial.name}</h3>
                  <p className="text-sm text-jeevanseva-gray">{testimonial.role}</p>
                </div>
              </div>
              
              <div className="flex-1">
                <svg className="w-8 h-8 text-jeevanseva-light mb-2" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                </svg>
                <p className="text-jeevanseva-gray">{testimonial.quote}</p>
              </div>
            </div>
          </CarouselItem>
        ))}
      </CarouselContent>
      <div className="flex justify-center mt-4">
        <CarouselPrevious className="relative inset-0 translate-y-0 -left-2" />
        <CarouselNext className="relative inset-0 translate-y-0 -right-2" />
      </div>
    </Carousel>
  );
};

export default TestimonialCarousel;
