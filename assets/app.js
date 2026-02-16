import './stimulus_bootstrap.js';
import './styles/app.css';

const applyImageFallback = (target) => {
    if (!(target instanceof HTMLImageElement)) {
        return;
    }

    const fallbackSrc = target.dataset.fallbackSrc || document.body?.dataset.defaultImageFallbackSrc;

    if (!fallbackSrc || target.dataset.fallbackApplied === '1') {
        return;
    }

    if (target.currentSrc === fallbackSrc || target.getAttribute('src') === fallbackSrc) {
        target.dataset.fallbackApplied = '1';
        return;
    }

    target.dataset.fallbackApplied = '1';
    target.src = fallbackSrc;
};

document.addEventListener('error', (event) => {
    applyImageFallback(event.target);
}, true);

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('img').forEach((image) => {
        if (!image.getAttribute('src') || image.getAttribute('src').trim() === '') {
            applyImageFallback(image);
        }
    });
});
