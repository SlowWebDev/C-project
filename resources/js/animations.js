/**
 * Animations - AOS and GSAP Setup
 * 
 * Provides scroll-based animations and counters for stats section,
 * plus general enter animations.
 * 
 * Author: SlowWebDev
 */
import AOS from 'aos';
import 'aos/dist/aos.css';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);
AOS.init({ once: true, offset: 50, duration: 800 });

document.addEventListener('DOMContentLoaded', () => {

    /* --------------------------
       General Scroll Animations
    --------------------------- */
    gsap.utils.toArray('[data-scroll]').forEach(el => {
        gsap.fromTo(el, { y: 30, opacity: 1 }, {
            y: 0, opacity: 1, duration: 0.8,
            scrollTrigger: { trigger: el, start: "top 90%", once: true }
        });
    });

    /* --------------------------
       Why-Choose-Us section
    --------------------------- */
    const statsSection = document.querySelector('.why-choose-stats');
    const counters = document.querySelectorAll('.stat-counter');
    if (!statsSection || !counters.length) return;

    const animateCounter = (el, delay) => {
        setTimeout(() => {
            gsap.to(el, {
                textContent: +el.dataset.target,
                duration: el.dataset.target > 100 ? 2.5 : 2,
                ease: "power2.out",
                snap: { textContent: 1 },
                onUpdate: () => el.textContent = (el.dataset.prefix) + Math.round(el.textContent)
            });
        }, delay);
    };

    const runCounters = () => counters.forEach((c, i) => animateCounter(c, i * 200));

    ScrollTrigger.create({
        trigger: statsSection,
        start: 'top 85%',
        once: true,
        onEnter: runCounters
    });

    const rect = statsSection.getBoundingClientRect();
    if (rect.top < innerHeight && rect.bottom >= 0) runCounters();
});