export function initCounters() {
    const counters = document.querySelectorAll('.counter');
    if (!counters.length) return;
    
    let animated = false;
    
    const animateCounters = () => {
        if (animated) return;
        
        counters.forEach((counter, index) => {
            const target = parseFloat(counter.dataset.target);
            const duration = 1500;
            const startTime = performance.now();
            const delay = index * 100;
            
            setTimeout(() => {
                const updateCounter = (currentTime) => {
                    const elapsed = currentTime - startTime - delay;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    // Easing function (ease-out)
                    const easeOut = 1 - Math.pow(1 - progress, 3);
                    const current = target * easeOut;
                    
                    counter.textContent = current.toFixed(2);
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    }
                };
                requestAnimationFrame(updateCounter);
            }, delay);
        });
        
        animated = true;
    };
    
    // Intersection Observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.disconnect();
            }
        });
    }, { threshold: 0.3 });
    
    const mockApp = document.querySelector('[data-mock-app]');
    if (mockApp) {
        observer.observe(mockApp);
    }
}
