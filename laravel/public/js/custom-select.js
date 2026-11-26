/**
 * High-End Luxury Custom Select Component for KHO - CA
 * Automatically transforms standard <select> elements into custom interactive dropdowns.
 */
(function () {
    'use strict';

    function initCustomSelect(select) {
        if (!select || select.dataset.customized === 'true') return;
        if (select.dataset.noCustom !== undefined) return;

        select.dataset.customized = 'true';

        // Container wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'custom-select-wrapper';
        if (select.className) {
            wrapper.classList.add(...select.className.split(' ').filter(c => c && c !== 'form-control'));
        }
        if (select.classList.contains('form-control')) {
            wrapper.classList.add('custom-select--form-control');
        }
        if (select.hasAttribute('disabled')) {
            wrapper.classList.add('is-disabled');
        }
        if (select.hidden || select.style.display === 'none') {
            wrapper.style.display = 'none';
        }

        // Copy custom inline width if specified
        if (select.style.width) {
            wrapper.style.width = select.style.width;
        }

        // Trigger Button
        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'custom-select-trigger';
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');

        const label = document.createElement('span');
        label.className = 'custom-select-label';

        const arrow = document.createElement('span');
        arrow.className = 'custom-select-arrow';
        arrow.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>`;

        trigger.appendChild(label);
        trigger.appendChild(arrow);

        // Menu Dropdown Card
        const menu = document.createElement('div');
        menu.className = 'custom-select-menu';
        menu.setAttribute('role', 'listbox');

        const optionsContainer = document.createElement('div');
        optionsContainer.className = 'custom-select-options';
        menu.appendChild(optionsContainer);

        // Populate options
        function buildOptions() {
            optionsContainer.innerHTML = '';
            const selectedOpt = select.options[select.selectedIndex] || select.options[0];
            label.textContent = selectedOpt ? selectedOpt.text : 'Chọn...';

            Array.from(select.options).forEach((opt, idx) => {
                const optEl = document.createElement('div');
                optEl.className = 'custom-select-option';
                optEl.setAttribute('role', 'option');
                optEl.dataset.value = opt.value;
                optEl.dataset.index = idx;

                if (opt.selected) {
                    optEl.classList.add('is-selected');
                    optEl.setAttribute('aria-selected', 'true');
                }
                if (opt.disabled) {
                    optEl.classList.add('is-disabled');
                }

                const textSpan = document.createElement('span');
                textSpan.className = 'custom-select-option-text';
                textSpan.textContent = opt.text;
                optEl.appendChild(textSpan);

                const checkIcon = document.createElement('span');
                checkIcon.className = 'custom-select-check';
                checkIcon.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
                optEl.appendChild(checkIcon);

                optEl.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (opt.disabled) return;
                    selectOption(idx);
                });

                optionsContainer.appendChild(optEl);
            });
        }

        function selectOption(index) {
            const prevIndex = select.selectedIndex;
            select.selectedIndex = index;

            const selectedOpt = select.options[index];
            label.textContent = selectedOpt ? selectedOpt.text : '';

            // Update UI selected states
            Array.from(optionsContainer.children).forEach((child, idx) => {
                const isCurrent = idx === index;
                child.classList.toggle('is-selected', isCurrent);
                child.setAttribute('aria-selected', isCurrent ? 'true' : 'false');
            });

            closeMenu();

            // Trigger change event if value changed
            if (prevIndex !== index || true) {
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        function openMenu() {
            if (wrapper.classList.contains('is-disabled')) return;
            
            // Close any other open custom selects
            document.querySelectorAll('.custom-select-wrapper.is-open').forEach(w => {
                if (w !== wrapper) w.classList.remove('is-open', 'opens-up');
            });

            // Check if dropdown fits below or should flip up
            const rect = trigger.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom;
            if (spaceBelow < 220 && rect.top > spaceBelow) {
                wrapper.classList.add('opens-up');
            } else {
                wrapper.classList.remove('opens-up');
            }

            wrapper.classList.add('is-open');
            trigger.setAttribute('aria-expanded', 'true');

            // Scroll selected option into view
            const selectedOpt = optionsContainer.querySelector('.custom-select-option.is-selected');
            if (selectedOpt) {
                selectedOpt.scrollIntoView({ block: 'nearest' });
            }
        }

        function closeMenu() {
            wrapper.classList.remove('is-open', 'opens-up');
            trigger.setAttribute('aria-expanded', 'false');
        }

        function toggleMenu() {
            if (wrapper.classList.contains('is-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        }

        // Toggle click
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });

        // Keyboard navigation
        trigger.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown' || e.key === 'Down') {
                e.preventDefault();
                if (!wrapper.classList.contains('is-open')) {
                    openMenu();
                } else {
                    const next = Math.min(select.options.length - 1, select.selectedIndex + 1);
                    selectOption(next);
                }
            } else if (e.key === 'ArrowUp' || e.key === 'Up') {
                e.preventDefault();
                if (!wrapper.classList.contains('is-open')) {
                    openMenu();
                } else {
                    const prev = Math.max(0, select.selectedIndex - 1);
                    selectOption(prev);
                }
            } else if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleMenu();
            } else if (e.key === 'Escape') {
                closeMenu();
            } else if (e.key === 'Tab') {
                closeMenu();
            }
        });

        // Listen for native changes / updates
        select.addEventListener('change', () => {
            const selectedOpt = select.options[select.selectedIndex];
            if (selectedOpt) {
                label.textContent = selectedOpt.text;
                Array.from(optionsContainer.children).forEach((child, idx) => {
                    const isCurrent = idx === select.selectedIndex;
                    child.classList.toggle('is-selected', isCurrent);
                    child.setAttribute('aria-selected', isCurrent ? 'true' : 'false');
                });
            }
        });

        // Sync with form resets
        const form = select.closest('form');
        if (form) {
            form.addEventListener('reset', () => {
                setTimeout(() => {
                    buildOptions();
                }, 10);
            });
        }

        // Observe attribute changes (hidden, disabled, etc.)
        const observer = new MutationObserver(() => {
            if (select.hidden || select.style.display === 'none') {
                wrapper.style.display = 'none';
            } else {
                wrapper.style.display = '';
            }
            wrapper.classList.toggle('is-disabled', select.hasAttribute('disabled'));
            buildOptions();
        });
        observer.observe(select, { attributes: true, childList: true, subtree: true });

        // Assemble DOM
        buildOptions();
        select.classList.add('custom-select-native-hidden');
        
        // Insert wrapper into DOM
        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);
        wrapper.appendChild(trigger);
        wrapper.appendChild(menu);
    }

    // Global outside click to close
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-select-wrapper')) {
            document.querySelectorAll('.custom-select-wrapper.is-open').forEach(w => {
                w.classList.remove('is-open', 'opens-up');
                const btn = w.querySelector('.custom-select-trigger');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            });
        }
    });

    // Public init function
    window.initCustomSelects = function (root) {
        const scope = root || document;
        const selects = scope.querySelectorAll('select:not([data-customized="true"])');
        selects.forEach(initCustomSelect);
    };

    // Auto-init on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => window.initCustomSelects());
    } else {
        window.initCustomSelects();
    }
})();
