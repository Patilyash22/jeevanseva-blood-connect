
import React, { useState } from 'react';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';

type FAQItem = {
  question: string;
  answer: React.ReactNode;
};

interface FAQSectionProps {
  title?: string;
  faqs: FAQItem[];
  className?: string;
}

const FAQSection = ({ title = "Frequently Asked Questions", faqs, className = "" }: FAQSectionProps) => {
  const [openItems, setOpenItems] = useState<Record<number, boolean>>({});

  const toggleItem = (index: number) => {
    setOpenItems(prev => ({
      ...prev,
      [index]: !prev[index]
    }));
  };

  return (
    <div className={`py-8 ${className}`}>
      <div className="container mx-auto px-4 md:px-6">
        <h2 className="text-3xl font-bold mb-2 text-center text-jeevanseva-darkred">{title}</h2>
        <p className="text-center text-jeevanseva-gray mb-8 max-w-2xl mx-auto">
          Have questions about blood donation? Find answers to commonly asked questions below.
        </p>
        
        <div className="max-w-3xl mx-auto space-y-4">
          {faqs.map((faq, index) => (
            <Collapsible 
              key={index}
              open={openItems[index]} 
              onOpenChange={() => toggleItem(index)}
              className="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow"
            >
              <CollapsibleTrigger className="flex justify-between items-center w-full p-4 text-left">
                <h3 className="text-lg font-medium text-jeevanseva-darkred">{faq.question}</h3>
                <span className={`transition-transform duration-300 ${openItems[index] ? 'rotate-180' : ''}`}>
                  <svg className="w-5 h-5 text-jeevanseva-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </span>
              </CollapsibleTrigger>
              <CollapsibleContent className="p-4 pt-0 border-t border-gray-100 animate-accordion-down">
                <div className="prose prose-sm max-w-none text-jeevanseva-gray">
                  {faq.answer}
                </div>
              </CollapsibleContent>
            </Collapsible>
          ))}
        </div>

        <div className="mt-8 text-center">
          <p className="mb-4 text-jeevanseva-gray">Can't find what you're looking for?</p>
          <a 
            href="/contact" 
            className="inline-flex items-center text-jeevanseva-red hover:text-jeevanseva-darkred font-medium"
          >
            Contact us for more information
            <svg 
              className="ml-2 w-4 h-4" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24" 
              xmlns="http://www.w3.org/2000/svg"
            >
              <path 
                strokeLinecap="round" 
                strokeLinejoin="round" 
                strokeWidth="2" 
                d="M14 5l7 7m0 0l-7 7m7-7H3"
              ></path>
            </svg>
          </a>
        </div>
      </div>
    </div>
  );
};

export default FAQSection;
