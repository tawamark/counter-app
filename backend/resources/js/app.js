import './bootstrap';

import Alpine from 'alpinejs';
import {
    AlertTriangle,
    ArrowDownUp,
    BarChart3,
    BadgeCheck,
    Check,
    ChevronDown,
    CircleCheck,
    CircleX,
    ClipboardList,
    Coins,
    Download,
    Eye,
    EyeOff,
    FileDown,
    History,
    Info,
    LayoutDashboard,
    LogOut,
    Menu,
    Package,
    Pencil,
    Plus,
    Save,
    ScanLine,
    Search,
    Scale,
    Settings,
    Smartphone,
    Tags,
    Trash2,
    TrendingDown,
    TrendingUp,
    Truck,
    Users,
    X,
    createIcons,
} from 'lucide';

const icons = {
    AlertTriangle,
    ArrowDownUp,
    BarChart3,
    BadgeCheck,
    Check,
    ChevronDown,
    CircleCheck,
    CircleX,
    ClipboardList,
    Coins,
    Download,
    Eye,
    EyeOff,
    FileDown,
    History,
    Info,
    LayoutDashboard,
    LogOut,
    Menu,
    Package,
    Pencil,
    Plus,
    Save,
    ScanLine,
    Search,
    Scale,
    Settings,
    Smartphone,
    Tags,
    Trash2,
    TrendingDown,
    TrendingUp,
    Truck,
    Users,
    X,
};

window.Alpine = Alpine;

Alpine.start();

window.createLucideIcons = () => createIcons({ icons });

window.createLucideIcons();

const createCounterLoader = () => {
    let fullLoaderTimer = null;
    let started = false;
    let nativeSubmit = null;

    const clearSkeletons = () => {
        document.querySelectorAll('[data-counter-skeleton-overlay]').forEach((overlay) => overlay.remove());
        document.querySelectorAll('[data-counter-skeleton-host]').forEach((element) => {
            element.classList.remove('counter-skeleton-host');
            element.removeAttribute('data-counter-skeleton-host');
        });
    };

    const isVisible = (element) => {
        const rect = element.getBoundingClientRect();

        return rect.width > 0 && rect.height > 0 && rect.bottom > 64 && rect.top < window.innerHeight;
    };

    const createSkeletonLine = (overlay, blockRect, rect) => {
        const line = document.createElement('div');
        const top = Math.max(0, rect.top - blockRect.top);
        const left = Math.max(0, rect.left - blockRect.left);
        const width = Math.min(rect.width, blockRect.width - left);
        const height = Math.min(Math.max(rect.height, 10), 44);

        if (width < 12 || height < 8) {
            return;
        }

        line.className = 'counter-skeleton-line';
        line.style.left = `${left}px`;
        line.style.top = `${top}px`;
        line.style.width = `${width}px`;
        line.style.height = `${height}px`;
        overlay.appendChild(line);
    };

    const createFallbackSkeleton = (overlay, blockRect) => {
        const rows = Math.max(2, Math.min(6, Math.floor(blockRect.height / 44)));

        for (let index = 0; index < rows; index += 1) {
            const line = document.createElement('div');
            line.className = 'counter-skeleton-line';
            line.style.left = '16px';
            line.style.top = `${16 + index * 34}px`;
            line.style.width = `${Math.max(80, blockRect.width - 32 - index * 18)}px`;
            line.style.height = index === 0 ? '18px' : '14px';
            overlay.appendChild(line);
        }
    };

    const applySkeletons = () => {
        const main = document.querySelector('main');

        if (!main) {
            return;
        }

        clearSkeletons();

        Array.from(main.children).filter(isVisible).slice(0, 12).forEach((block) => {
            const blockRect = block.getBoundingClientRect();
            const overlay = document.createElement('div');
            const nodes = Array.from(block.querySelectorAll('h1, h2, h3, p, span, label, input, textarea, button, a, th, td, .rounded-md, .rounded-lg'))
                .filter((node) => !node.closest('[data-counter-skeleton-overlay]'))
                .filter(isVisible)
                .slice(0, 90);

            block.classList.add('counter-skeleton-host');
            block.setAttribute('data-counter-skeleton-host', 'true');
            overlay.className = 'counter-skeleton-overlay';
            overlay.setAttribute('data-counter-skeleton-overlay', 'true');

            nodes.forEach((node) => createSkeletonLine(overlay, blockRect, node.getBoundingClientRect()));

            if (!overlay.childElementCount) {
                createFallbackSkeleton(overlay, blockRect);
            }

            block.appendChild(overlay);
        });
    };

    const start = (options = {}) => {
        if (started) {
            return;
        }

        started = true;
        document.body.classList.add('counter-is-loading');

        if (options.skeleton !== false) {
            window.requestAnimationFrame(applySkeletons);
        }

        fullLoaderTimer = window.setTimeout(() => {
            document.body.classList.add('counter-loader-full');
        }, options.fullLoaderDelay ?? 1400);
    };

    const stop = () => {
        started = false;
        window.clearTimeout(fullLoaderTimer);
        document.body.classList.remove('counter-is-loading', 'counter-loader-full');
        clearSkeletons();
    };

    const shouldLoadLink = (anchor, event) => {
        if (!anchor || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return false;
        }

        if (anchor.target || anchor.hasAttribute('download') || anchor.hasAttribute('data-no-loader')) {
            return false;
        }

        const href = anchor.getAttribute('href');

        if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
            return false;
        }

        const url = new URL(anchor.href, window.location.href);

        return url.origin === window.location.origin && url.href !== window.location.href;
    };

    const bind = () => {
        document.addEventListener('click', (event) => {
            const clickedElement = event.target instanceof Element ? event.target : event.target.parentElement;
            const anchor = clickedElement?.closest('a');

            window.setTimeout(() => {
                if (shouldLoadLink(anchor, event)) {
                    start({ skeleton: false });
                }
            }, 0);
        });

        document.addEventListener('submit', (event) => {
            if (!event.defaultPrevented && !event.target.hasAttribute('data-no-loader')) {
                start({ skeleton: false });
            }
        });

        nativeSubmit = HTMLFormElement.prototype.submit;

        HTMLFormElement.prototype.submit = function submit() {
            if (!this.hasAttribute('data-no-loader')) {
                start({ skeleton: false });
            }

            return nativeSubmit.call(this);
        };

        window.addEventListener('pageshow', stop);
        window.addEventListener('load', stop);
    };

    return { bind, start, stop };
};

window.CounterLoader = createCounterLoader();
window.CounterLoader.bind();
