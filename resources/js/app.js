

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const siteNavigation = document.querySelector('.site-nav');

if (siteNavigation) {
	const updateNavigation = () => {
		siteNavigation.classList.toggle('is-scrolled', window.scrollY > 24);
	};

	updateNavigation();
	window.addEventListener('scroll', updateNavigation, { passive: true });
}

const revealElements = [...document.querySelectorAll('[data-reveal]')];
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (prefersReducedMotion || !('IntersectionObserver' in window)) {
	revealElements.forEach((element) => element.classList.add('is-visible'));
} else if (revealElements.length > 0) {
	document.documentElement.classList.add('has-scroll-reveal');

	const revealObserver = new IntersectionObserver((entries, observer) => {
		entries.forEach((entry) => {
			if (!entry.isIntersecting) {
				return;
			}

			entry.target.classList.add('is-visible');
			observer.unobserve(entry.target);
		});
	}, { threshold: 0.14, rootMargin: '0px 0px -40px 0px' });

	revealElements.forEach((element, index) => {
		element.style.setProperty('--reveal-delay', `${(index % 4) * 80}ms`);
		revealObserver.observe(element);
	});
}
