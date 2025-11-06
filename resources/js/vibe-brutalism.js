/**
 * VIBE BRUTALISM - JavaScript Component Library
 * Interactive functionality for neo-brutalist components
 * Version: 2.0.0
 * WCAG 2.1 AA & Section 508 Compliant
 */

(function() {
  'use strict';

  // ============================================
  // ACCESSIBILITY UTILITIES
  // ============================================

  const a11y = {
    /**
     * Creates an ARIA live region for screen reader announcements
     * @returns {HTMLElement} The live region element
     */
    createLiveRegion() {
      if (!document.getElementById('vb-live-region')) {
        const liveRegion = document.createElement('div');
        liveRegion.id = 'vb-live-region';
        liveRegion.className = 'vb-sr-only';
        liveRegion.setAttribute('aria-live', 'polite');
        liveRegion.setAttribute('aria-atomic', 'true');
        document.body.appendChild(liveRegion);
      }
      return document.getElementById('vb-live-region');
    },

    /**
     * Announces a message to screen readers via ARIA live region
     * @param {string} message - The message to announce
     * @param {string} [priority='polite'] - Priority level ('polite' or 'assertive')
     */
    announce(message, priority = 'polite') {
      const liveRegion = this.createLiveRegion();
      liveRegion.setAttribute('aria-live', priority);
      liveRegion.textContent = message;

      // Clear after announcement
      setTimeout(() => {
        liveRegion.textContent = '';
      }, 1000);
    },

    /**
     * Traps keyboard focus within an element (for modals, dialogs)
     * @param {HTMLElement} element - The element to trap focus within
     * @returns {Function|null} Cleanup function to remove the trap, or null if no focusable elements
     */
    trapFocus(element) {
      const focusableElements = element.querySelectorAll(
        'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
      );

      if (focusableElements.length === 0) return null;

      const firstFocusable = focusableElements[0];
      const lastFocusable = focusableElements[focusableElements.length - 1];

      const handler = (e) => {
        if (e.key === 'Tab') {
          if (e.shiftKey && document.activeElement === firstFocusable) {
            e.preventDefault();
            lastFocusable.focus();
          } else if (!e.shiftKey && document.activeElement === lastFocusable) {
            e.preventDefault();
            firstFocusable.focus();
          }
        }
      };

      element.addEventListener('keydown', handler);
      firstFocusable.focus();

      // Return cleanup function
      return () => element.removeEventListener('keydown', handler);
    }
  };

  // ============================================
  // MODAL FUNCTIONALITY (WCAG 2.1 AA)
  // ============================================

  /**
   * Modal dialog component with full accessibility support
   * @class
   */
  class VBModal {
    /**
     * Creates a new modal instance
     * @param {HTMLElement} element - The modal element
     */
    constructor(element) {
      this.modal = element;
      this.closeButtons = this.modal.querySelectorAll('.vb-modal-close, [data-vb-modal-close]');
      this.previousActiveElement = null;
      this.focusTrapCleanup = null;
      this.escHandler = this.handleEscape.bind(this);
      this.clickHandler = this.handleBackdropClick.bind(this);
      this.closeHandler = this.close.bind(this);
      this.init();
    }

    init() {
      // Set ARIA attributes
      this.modal.setAttribute('role', 'dialog');
      this.modal.setAttribute('aria-modal', 'true');

      if (!this.modal.getAttribute('aria-labelledby')) {
        const title = this.modal.querySelector('.vb-modal-title');
        if (title && !title.id) {
          title.id = `modal-title-${Math.random().toString(36).slice(2, 11)}`;
        }
        if (title) {
          this.modal.setAttribute('aria-labelledby', title.id);
        }
      }

      // Close button events - using bound handler for proper cleanup
      this.closeButtons.forEach(btn => {
        btn.setAttribute('aria-label', 'Close dialog');
        btn.addEventListener('click', this.closeHandler);
      });

      // Close on outside click
      this.modal.addEventListener('click', this.clickHandler);
    }

    /**
     * Destroys the modal and cleans up event listeners
     */
    destroy() {
      // Remove close button listeners
      this.closeButtons.forEach(btn => {
        btn.removeEventListener('click', this.closeHandler);
      });

      // Remove backdrop click listener
      this.modal.removeEventListener('click', this.clickHandler);

      // Remove ESC key handler if modal is open
      document.removeEventListener('keydown', this.escHandler);

      // Cleanup focus trap if active
      if (this.focusTrapCleanup) {
        this.focusTrapCleanup();
        this.focusTrapCleanup = null;
      }
    }

    handleBackdropClick(e) {
      if (e.target === this.modal) {
        this.close();
      }
    }

    handleEscape(e) {
      if (e.key === 'Escape' && this.modal.classList.contains('active')) {
        this.close();
      }
    }

    /**
     * Opens the modal and manages focus
     */
    open() {
      this.previousActiveElement = document.activeElement;
      this.modal.classList.add('active');
      document.body.style.overflow = 'hidden';

      // Trap focus and store cleanup function
      this.focusTrapCleanup = a11y.trapFocus(this.modal);

      // Add ESC key handler
      document.addEventListener('keydown', this.escHandler);

      a11y.announce('Dialog opened');
    }

    /**
     * Closes the modal and restores focus
     */
    close() {
      this.modal.classList.remove('active');
      document.body.style.overflow = '';

      // Cleanup focus trap
      if (this.focusTrapCleanup) {
        this.focusTrapCleanup();
        this.focusTrapCleanup = null;
      }

      // Remove ESC key handler
      document.removeEventListener('keydown', this.escHandler);

      // Return focus
      if (this.previousActiveElement) {
        this.previousActiveElement.focus();
      }

      a11y.announce('Dialog closed');
    }
  }

  // Initialize all modals
  const modals = {};
  document.querySelectorAll('.vb-modal').forEach(modal => {
    const id = modal.id;
    if (id) {
      modals[id] = new VBModal(modal);
    }
  });

  // Modal trigger buttons
  document.querySelectorAll('[data-vb-modal-target]').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.dataset.vbModalTarget;
      if (modals[targetId]) {
        modals[targetId].open();
      }
    });
  });

  // ============================================
  // HAMBURGER MENU FUNCTIONALITY (WCAG 2.1 AA)
  // ============================================

  class VBHamburgerMenu {
    constructor(navbar) {
      this.navbar = navbar;
      this.toggle = navbar.querySelector('.vb-navbar-toggle');
      this.menu = navbar.querySelector('.vb-navbar-menu');
      this.escHandler = this.handleEscape.bind(this);
      this.toggleHandler = this.toggleMenu.bind(this);

      if (this.toggle && this.menu) {
        this.init();
      }
    }

    init() {
      // Set ARIA attributes
      this.toggle.setAttribute('aria-label', 'Toggle navigation menu');
      this.toggle.setAttribute('aria-expanded', 'false');
      this.toggle.setAttribute('aria-controls', this.menu.id || 'navbar-menu');

      if (!this.menu.id) {
        this.menu.id = 'navbar-menu';
      }

      // Toggle on click - using bound handler for proper cleanup
      this.toggle.addEventListener('click', this.toggleHandler);

      // Close on ESC - using bound handler for proper cleanup
      document.addEventListener('keydown', this.escHandler);
    }

    /**
     * Destroys the hamburger menu and cleans up event listeners
     */
    destroy() {
      if (this.toggle) {
        this.toggle.removeEventListener('click', this.toggleHandler);
      }
      document.removeEventListener('keydown', this.escHandler);
    }

    handleEscape(e) {
      if (e.key === 'Escape' && this.menu.classList.contains('active')) {
        this.closeMenu();
        this.toggle.focus();
      }
    }

    toggleMenu() {
      const isOpen = this.menu.classList.toggle('active');
      this.toggle.classList.toggle('active');
      this.toggle.setAttribute('aria-expanded', isOpen);

      a11y.announce(isOpen ? 'Menu opened' : 'Menu closed');
    }

    closeMenu() {
      this.menu.classList.remove('active');
      this.toggle.classList.remove('active');
      this.toggle.setAttribute('aria-expanded', 'false');
    }
  }

  // Initialize hamburger menus and store references for cleanup
  const hamburgerMenus = [];
  document.querySelectorAll('.vb-navbar').forEach(navbar => {
    hamburgerMenus.push(new VBHamburgerMenu(navbar));
  });

  // ============================================
  // DROPDOWN FUNCTIONALITY (WCAG 2.1 AA)
  // ============================================

  class VBDropdown {
    constructor(element) {
      this.dropdown = element;
      this.toggle = this.dropdown.querySelector('.vb-dropdown-toggle');
      this.menu = this.dropdown.querySelector('.vb-dropdown-menu');

      if (!this.toggle || !this.menu) {
        console.warn('Vibe Brutalism: Dropdown requires .vb-dropdown-toggle and .vb-dropdown-menu');
        return;
      }

      this.items = this.menu.querySelectorAll('.vb-dropdown-item');
      this.outsideClickHandler = this.handleOutsideClick.bind(this);
      this.init();
    }

    init() {
      // Set ARIA attributes
      this.toggle.setAttribute('aria-haspopup', 'true');
      this.toggle.setAttribute('aria-expanded', 'false');
      this.menu.setAttribute('role', 'menu');

      this.items.forEach(item => {
        item.setAttribute('role', 'menuitem');
        item.setAttribute('tabindex', '-1');
      });

      // Toggle on click
      this.toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        this.toggleDropdown();
      });

      // Keyboard navigation
      this.toggle.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          this.open();
          this.items[0]?.focus();
        }
      });

      this.items.forEach((item, index) => {
        item.addEventListener('keydown', (e) => {
          if (e.key === 'ArrowDown') {
            e.preventDefault();
            this.items[(index + 1) % this.items.length]?.focus();
          } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            this.items[(index - 1 + this.items.length) % this.items.length]?.focus();
          } else if (e.key === 'Escape') {
            e.preventDefault();
            this.close();
            this.toggle.focus();
          }
        });
      });

      // Prevent closing when clicking inside menu
      this.menu.addEventListener('click', (e) => {
        e.stopPropagation();
      });
    }

    handleOutsideClick() {
      this.close();
    }

    toggleDropdown() {
      const isOpen = this.dropdown.classList.toggle('active');
      this.toggle.setAttribute('aria-expanded', isOpen);

      if (isOpen) {
        this.items.forEach(item => item.setAttribute('tabindex', '0'));
        // Add outside click handler when open
        setTimeout(() => {
          document.addEventListener('click', this.outsideClickHandler);
        }, 0);
      } else {
        this.items.forEach(item => item.setAttribute('tabindex', '-1'));
        // Remove outside click handler when closed
        document.removeEventListener('click', this.outsideClickHandler);
      }
    }

    open() {
      this.dropdown.classList.add('active');
      this.toggle.setAttribute('aria-expanded', 'true');
      this.items.forEach(item => item.setAttribute('tabindex', '0'));
      // Add outside click handler when open
      setTimeout(() => {
        document.addEventListener('click', this.outsideClickHandler);
      }, 0);
    }

    close() {
      this.dropdown.classList.remove('active');
      this.toggle.setAttribute('aria-expanded', 'false');
      this.items.forEach(item => item.setAttribute('tabindex', '-1'));
      // Remove outside click handler when closed
      document.removeEventListener('click', this.outsideClickHandler);
    }
  }

  // Initialize all dropdowns
  document.querySelectorAll('.vb-dropdown').forEach(dropdown => {
    new VBDropdown(dropdown);
  });

  // ============================================
  // TABS FUNCTIONALITY (WCAG 2.1 AA)
  // ============================================

  class VBTabs {
    constructor(element) {
      this.tabs = element;
      this.buttons = this.tabs.querySelectorAll('.vb-tab-button');
      this.contents = this.tabs.querySelectorAll('.vb-tab-content');
      this.init();
    }

    init() {
      // Set ARIA attributes
      const tablist = this.tabs.querySelector('.vb-tab-list');
      if (tablist) {
        tablist.setAttribute('role', 'tablist');
      }

      this.buttons.forEach((button, index) => {
        const tabId = `tab-${Math.random().toString(36).slice(2, 11)}`;
        const panelId = `panel-${Math.random().toString(36).slice(2, 11)}`;

        button.setAttribute('role', 'tab');
        button.setAttribute('id', tabId);
        button.setAttribute('aria-controls', panelId);
        button.setAttribute('tabindex', '-1');

        this.contents[index].setAttribute('role', 'tabpanel');
        this.contents[index].setAttribute('id', panelId);
        this.contents[index].setAttribute('aria-labelledby', tabId);
        this.contents[index].setAttribute('tabindex', '0');

        // Click event
        button.addEventListener('click', () => {
          this.switchTab(index);
        });

        // Keyboard navigation
        button.addEventListener('keydown', (e) => {
          let newIndex = index;

          if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
            e.preventDefault();
            newIndex = (index + 1) % this.buttons.length;
          } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
            e.preventDefault();
            newIndex = (index - 1 + this.buttons.length) % this.buttons.length;
          } else if (e.key === 'Home') {
            e.preventDefault();
            newIndex = 0;
          } else if (e.key === 'End') {
            e.preventDefault();
            newIndex = this.buttons.length - 1;
          }

          if (newIndex !== index) {
            this.switchTab(newIndex);
            this.buttons[newIndex].focus();
          }
        });
      });

      // Activate first tab by default
      if (this.buttons.length > 0 && !this.tabs.querySelector('.vb-tab-button.active')) {
        this.switchTab(0);
      }
    }

    switchTab(index) {
      // Remove active class and update ARIA
      this.buttons.forEach((btn, i) => {
        btn.classList.remove('active');
        btn.setAttribute('aria-selected', 'false');
        btn.setAttribute('tabindex', '-1');
      });

      this.contents.forEach(content => {
        content.classList.remove('active');
      });

      // Add active class to selected button and content
      this.buttons[index].classList.add('active');
      this.buttons[index].setAttribute('aria-selected', 'true');
      this.buttons[index].setAttribute('tabindex', '0');
      this.contents[index].classList.add('active');

      a11y.announce(`Tab ${index + 1} selected`);
    }
  }

  // Initialize all tabs
  document.querySelectorAll('.vb-tabs').forEach(tabs => {
    new VBTabs(tabs);
  });

  // ============================================
  // ACCORDION FUNCTIONALITY (WCAG 2.1 AA)
  // ============================================

  class VBAccordion {
    constructor(element) {
      this.accordion = element;
      this.items = this.accordion.querySelectorAll('.vb-accordion-item');
      this.headers = []; // Cache headers for better performance
      this.bodies = [];  // Cache bodies for better performance
      this.init();
    }

    init() {
      this.items.forEach((item, index) => {
        const header = item.querySelector('.vb-accordion-header');
        const body = item.querySelector('.vb-accordion-body');

        // Cache references for performance
        this.headers.push(header);
        this.bodies.push(body);

        // Set ARIA attributes
        const headerId = `accordion-header-${Math.random().toString(36).slice(2, 11)}`;
        const bodyId = `accordion-body-${Math.random().toString(36).slice(2, 11)}`;

        header.setAttribute('id', headerId);
        header.setAttribute('aria-expanded', 'false');
        header.setAttribute('aria-controls', bodyId);

        body.setAttribute('id', bodyId);
        body.setAttribute('role', 'region');
        body.setAttribute('aria-labelledby', headerId);

        // Click event
        header.addEventListener('click', () => {
          this.toggle(header, body);
        });

        // Keyboard support - use cached headers
        header.addEventListener('keydown', (e) => {
          if (e.key === 'ArrowDown') {
            e.preventDefault();
            this.headers[index + 1]?.focus();
          } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            this.headers[index - 1]?.focus();
          } else if (e.key === 'Home') {
            e.preventDefault();
            this.headers[0]?.focus();
          } else if (e.key === 'End') {
            e.preventDefault();
            this.headers[this.headers.length - 1]?.focus();
          }
        });
      });
    }

    toggle(header, body) {
      const isActive = header.classList.contains('active');

      // Close all items - use cached arrays for better performance
      this.headers.forEach(h => {
        h.classList.remove('active');
        h.setAttribute('aria-expanded', 'false');
      });
      this.bodies.forEach(b => {
        b.classList.remove('active');
      });

      // Toggle current item
      if (!isActive) {
        header.classList.add('active');
        header.setAttribute('aria-expanded', 'true');
        body.classList.add('active');
        a11y.announce('Section expanded');
      } else {
        a11y.announce('Section collapsed');
      }
    }
  }

  // Initialize all accordions
  document.querySelectorAll('.vb-accordion').forEach(accordion => {
    new VBAccordion(accordion);
  });

  // ============================================
  // CAROUSEL FUNCTIONALITY (WCAG 2.1 AA)
  // ============================================

  class VBCarousel {
    constructor(element) {
      this.carousel = element;
      this.track = this.carousel.querySelector('.vb-carousel-track');
      this.items = this.carousel.querySelectorAll('.vb-carousel-item');
      this.prevBtn = this.carousel.querySelector('.vb-carousel-control-prev');
      this.nextBtn = this.carousel.querySelector('.vb-carousel-control-next');
      this.indicators = this.carousel.querySelectorAll('.vb-carousel-indicator');
      this.currentIndex = 0;
      this.autoplayInterval = null;
      this.touchStartX = 0;
      this.touchEndX = 0;

      // Bind handlers for proper cleanup
      this.prevHandler = () => this.prev();
      this.nextHandler = () => this.next();
      this.keydownHandler = (e) => this.handleKeydown(e);
      this.mouseenterHandler = () => this.pauseAutoplay();
      this.mouseleaveHandler = () => this.startAutoplay();
      this.focusinHandler = () => this.pauseAutoplay();
      this.focusoutHandler = () => this.startAutoplay();
      this.touchstartHandler = (e) => { this.touchStartX = e.changedTouches[0].screenX; };
      this.touchendHandler = (e) => { this.touchEndX = e.changedTouches[0].screenX; this.handleSwipe(); };

      this.init();
    }

    handleKeydown(e) {
      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        this.prev();
      } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        this.next();
      }
    }

    init() {
      if (this.items.length === 0 || !this.track) {
        console.warn('Vibe Brutalism: Carousel requires .vb-carousel-track and at least one .vb-carousel-item');
        return;
      }

      // Set ARIA attributes
      this.carousel.setAttribute('role', 'region');
      this.carousel.setAttribute('aria-label', 'Carousel');
      this.carousel.setAttribute('aria-roledescription', 'carousel');

      this.items.forEach((item, index) => {
        item.setAttribute('role', 'group');
        item.setAttribute('aria-roledescription', 'slide');
        item.setAttribute('aria-label', `Slide ${index + 1} of ${this.items.length}`);
      });

      // Store indicator handlers for cleanup
      this.indicatorHandlers = [];

      // Previous button
      if (this.prevBtn) {
        this.prevBtn.setAttribute('aria-label', 'Previous slide');
        this.prevBtn.addEventListener('click', this.prevHandler);
      }

      // Next button
      if (this.nextBtn) {
        this.nextBtn.setAttribute('aria-label', 'Next slide');
        this.nextBtn.addEventListener('click', this.nextHandler);
      }

      // Indicators
      this.indicators.forEach((indicator, index) => {
        indicator.setAttribute('aria-label', `Go to slide ${index + 1}`);
        indicator.setAttribute('role', 'button');
        const handler = () => this.goTo(index);
        this.indicatorHandlers.push(handler);
        indicator.addEventListener('click', handler);
      });

      // Keyboard navigation
      this.carousel.addEventListener('keydown', this.keydownHandler);

      // Pause autoplay on hover/focus
      this.carousel.addEventListener('mouseenter', this.mouseenterHandler);
      this.carousel.addEventListener('mouseleave', this.mouseleaveHandler);
      this.carousel.addEventListener('focusin', this.focusinHandler);
      this.carousel.addEventListener('focusout', this.focusoutHandler);

      // Touch/Swipe support
      this.carousel.addEventListener('touchstart', this.touchstartHandler, { passive: true });
      this.carousel.addEventListener('touchend', this.touchendHandler, { passive: true });

      // Initialize
      this.goTo(0);

      // Start autoplay if enabled
      if (this.carousel.dataset.autoplay === 'true') {
        this.startAutoplay();
      }
    }

    handleSwipe() {
      const swipeThreshold = 50;
      const diff = this.touchStartX - this.touchEndX;

      if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
          // Swipe left - go to next
          this.next();
        } else {
          // Swipe right - go to previous
          this.prev();
        }
      }
    }

    goTo(index) {
      this.currentIndex = index;
      const offset = -index * 100;
      this.track.style.transform = `translateX(${offset}%)`;

      // Update indicators
      this.indicators.forEach((indicator, i) => {
        indicator.classList.toggle('active', i === index);
        indicator.setAttribute('aria-current', i === index ? 'true' : 'false');
      });

      // Update items
      this.items.forEach((item, i) => {
        item.setAttribute('aria-hidden', i !== index);
      });

      a11y.announce(`Slide ${index + 1} of ${this.items.length}`);
    }

    next() {
      const nextIndex = (this.currentIndex + 1) % this.items.length;
      this.goTo(nextIndex);
    }

    prev() {
      const prevIndex = (this.currentIndex - 1 + this.items.length) % this.items.length;
      this.goTo(prevIndex);
    }

    startAutoplay() {
      const interval = parseInt(this.carousel.dataset.interval) || 5000;
      this.autoplayInterval = setInterval(() => this.next(), interval);
    }

    pauseAutoplay() {
      if (this.autoplayInterval) {
        clearInterval(this.autoplayInterval);
        this.autoplayInterval = null;
      }
    }

    /**
     * Destroys the carousel and cleans up all event listeners
     */
    destroy() {
      // Pause autoplay and clear interval
      this.pauseAutoplay();

      // Remove button listeners
      if (this.prevBtn) {
        this.prevBtn.removeEventListener('click', this.prevHandler);
      }
      if (this.nextBtn) {
        this.nextBtn.removeEventListener('click', this.nextHandler);
      }

      // Remove indicator listeners
      this.indicators.forEach((indicator, index) => {
        if (this.indicatorHandlers[index]) {
          indicator.removeEventListener('click', this.indicatorHandlers[index]);
        }
      });

      // Remove carousel listeners
      this.carousel.removeEventListener('keydown', this.keydownHandler);
      this.carousel.removeEventListener('mouseenter', this.mouseenterHandler);
      this.carousel.removeEventListener('mouseleave', this.mouseleaveHandler);
      this.carousel.removeEventListener('focusin', this.focusinHandler);
      this.carousel.removeEventListener('focusout', this.focusoutHandler);
      this.carousel.removeEventListener('touchstart', this.touchstartHandler);
      this.carousel.removeEventListener('touchend', this.touchendHandler);
    }
  }

  // Initialize all carousels
  const carousels = {};
  document.querySelectorAll('.vb-carousel').forEach(carousel => {
    const id = carousel.id || `carousel-${Math.random().toString(36).slice(2, 11)}`;
    carousel.id = id;
    carousels[id] = new VBCarousel(carousel);
  });

  // ============================================
  // TOAST NOTIFICATIONS (WCAG 2.1 AA)
  // ============================================

  /**
   * Toast notification manager with auto-dismiss and accessibility support
   * @class
   */
  class VBToastManager {
    constructor() {
      this.container = null;
      this.toasts = [];
    }

    getContainer(position = 'top-right') {
      const containerId = `vb-toast-container-${position}`;
      let container = document.getElementById(containerId);

      if (!container) {
        container = document.createElement('div');
        container.id = containerId;
        container.className = `vb-toast-container ${position}`;
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('aria-atomic', 'false');
        document.body.appendChild(container);
      }

      return container;
    }

    /**
     * Shows a toast notification
     * @param {string} message - The message to display (text only, HTML not supported for security)
     * @param {string} [type='info'] - Toast type: 'success', 'warning', 'danger', or 'info'
     * @param {number} [duration=5000] - Duration in milliseconds (0 for permanent)
     * @param {string} [position='top-right'] - Position: 'top-right', 'top-left', 'top-center', 'bottom-right', 'bottom-left'
     * @returns {HTMLElement|null} The toast element or null if validation fails
     */
    show(message, type = 'info', duration = 5000, position = 'top-right') {
      // Input validation
      if (typeof message !== 'string' || message.trim() === '') {
        console.error('Vibe Brutalism: Toast message must be a non-empty string');
        return null;
      }
      if (duration < 0) {
        console.error('Vibe Brutalism: Toast duration must be non-negative');
        return null;
      }

      // Validate and sanitize type parameter (prevent CSS injection)
      const validTypes = ['success', 'warning', 'danger', 'info'];
      const safeType = validTypes.includes(type) ? type : 'info';

      // Validate and sanitize position parameter
      const validPositions = ['top-right', 'top-left', 'top-center', 'bottom-right', 'bottom-left'];
      const safePosition = validPositions.includes(position) ? position : 'top-right';

      const container = this.getContainer(safePosition);

      const toast = document.createElement('div');
      toast.className = `vb-toast vb-toast-${safeType}`;
      toast.setAttribute('role', 'status');
      toast.setAttribute('aria-live', 'polite');

      // Icon
      const icons = {
        success: '✓',
        warning: '⚠',
        danger: '✕',
        info: 'ℹ'
      };

      // Create elements safely to prevent XSS
      const iconSpan = document.createElement('span');
      iconSpan.className = 'vb-toast-icon';
      iconSpan.setAttribute('aria-hidden', 'true');
      iconSpan.textContent = icons[safeType] || icons.info;

      const contentDiv = document.createElement('div');
      contentDiv.className = 'vb-toast-content';
      contentDiv.textContent = message; // Use textContent to prevent XSS

      const closeBtn = document.createElement('button');
      closeBtn.className = 'vb-toast-close';
      closeBtn.setAttribute('aria-label', 'Close notification');
      closeBtn.textContent = '×';

      toast.appendChild(iconSpan);
      toast.appendChild(contentDiv);
      toast.appendChild(closeBtn);

      // Close button
      closeBtn.addEventListener('click', () => this.remove(toast));

      container.appendChild(toast);
      this.toasts.push(toast);

      // Announce to screen readers
      a11y.announce(message, 'polite');

      // Auto remove
      if (duration > 0) {
        setTimeout(() => this.remove(toast), duration);
      }

      return toast;
    }

    /**
     * Removes a toast notification
     * @param {HTMLElement} toast - The toast element to remove
     */
    remove(toast) {
      toast.classList.add('removing');
      setTimeout(() => {
        toast.remove();
        const index = this.toasts.indexOf(toast);
        if (index > -1) {
          this.toasts.splice(index, 1);
        }
      }, 300);
    }
  }

  const toastManager = new VBToastManager();

  // ============================================
  // SNACKBAR FUNCTIONALITY (WCAG 2.1 AA)
  // ============================================

  /**
   * Snackbar notification component with optional action button
   * @class
   */
  class VBSnackbar {
    constructor() {
      this.snackbar = null;
      this.timeout = null;
    }

    /**
     * Shows a snackbar notification with optional action
     * @param {string} message - The message to display (text only, HTML not supported for security)
     * @param {string|null} [actionText=null] - Text for action button (null for no action)
     * @param {Function|null} [actionCallback=null] - Callback when action button is clicked
     * @param {number} [duration=5000] - Duration in milliseconds (0 for permanent)
     */
    show(message, actionText = null, actionCallback = null, duration = 5000) {
      // Input validation
      if (typeof message !== 'string' || message.trim() === '') {
        console.error('Vibe Brutalism: Snackbar message must be a non-empty string');
        return;
      }
      if (actionText && typeof actionText !== 'string') {
        console.error('Vibe Brutalism: Snackbar actionText must be a string');
        return;
      }
      if (actionCallback && typeof actionCallback !== 'function') {
        console.error('Vibe Brutalism: Snackbar actionCallback must be a function');
        return;
      }
      if (duration < 0) {
        console.error('Vibe Brutalism: Snackbar duration must be non-negative');
        return;
      }

      // Remove existing snackbar
      if (this.snackbar) {
        this.hide();
      }

      // Create snackbar safely to prevent XSS
      this.snackbar = document.createElement('div');
      this.snackbar.className = 'vb-snackbar';
      this.snackbar.setAttribute('role', 'status');
      this.snackbar.setAttribute('aria-live', 'polite');

      // Create message element
      const messageDiv = document.createElement('div');
      messageDiv.className = 'vb-snackbar-message';
      messageDiv.textContent = message; // Use textContent to prevent XSS
      this.snackbar.appendChild(messageDiv);

      // Create action button if provided
      if (actionText && actionCallback) {
        const actionBtn = document.createElement('button');
        actionBtn.className = 'vb-snackbar-action';
        actionBtn.textContent = actionText; // Use textContent to prevent XSS
        actionBtn.addEventListener('click', () => {
          actionCallback();
          this.hide();
        });
        this.snackbar.appendChild(actionBtn);
      }

      // Create close button
      const closeBtn = document.createElement('button');
      closeBtn.className = 'vb-snackbar-close';
      closeBtn.setAttribute('aria-label', 'Close');
      closeBtn.textContent = '×';
      closeBtn.addEventListener('click', () => this.hide());
      this.snackbar.appendChild(closeBtn);

      document.body.appendChild(this.snackbar);

      // Show with animation
      setTimeout(() => {
        this.snackbar.classList.add('show');
      }, 10);

      // Announce to screen readers
      a11y.announce(message, 'polite');

      // Auto hide
      if (duration > 0) {
        this.timeout = setTimeout(() => this.hide(), duration);
      }
    }

    /**
     * Hides the currently displayed snackbar
     */
    hide() {
      if (this.snackbar) {
        this.snackbar.classList.remove('show');
        setTimeout(() => {
          this.snackbar?.remove();
          this.snackbar = null;
        }, 300);
      }

      if (this.timeout) {
        clearTimeout(this.timeout);
        this.timeout = null;
      }
    }
  }

  const snackbar = new VBSnackbar();

  // ============================================
  // ALERT CLOSE FUNCTIONALITY
  // ============================================

  document.querySelectorAll('[data-vb-alert-close]').forEach(btn => {
    btn.addEventListener('click', () => {
      const alert = btn.closest('.vb-alert');
      if (alert) {
        alert.style.opacity = '0';
        setTimeout(() => {
          alert.remove();
        }, 300);
      }
    });
  });

  // ============================================
  // PROGRESS BAR
  // ============================================

  /**
   * Updates a progress bar element
   * @param {string} elementId - The ID of the progress container element
   * @param {number} percentage - Progress percentage (0-100)
   * @returns {boolean} True if successful, false if validation fails or element not found
   */
  window.VBSetProgress = function(elementId, percentage) {
    // Input validation
    if (typeof elementId !== 'string' || elementId.trim() === '') {
      console.error('Vibe Brutalism: VBSetProgress elementId must be a non-empty string');
      return false;
    }
    if (typeof percentage !== 'number' || isNaN(percentage)) {
      console.error('Vibe Brutalism: VBSetProgress percentage must be a number');
      return false;
    }
    if (percentage < 0 || percentage > 100) {
      console.error('Vibe Brutalism: VBSetProgress percentage must be between 0 and 100');
      return false;
    }

    try {
      const progressBar = document.querySelector(`#${CSS.escape(elementId)} .vb-progress-bar`);
      if (!progressBar) {
        console.warn(`Vibe Brutalism: Progress bar not found for element ID "${elementId}"`);
        return false;
      }

      progressBar.style.width = `${percentage}%`;
      progressBar.textContent = `${percentage}%`;
      progressBar.setAttribute('aria-valuenow', percentage);
      a11y.announce(`Progress: ${percentage}%`);
      return true;
    } catch (error) {
      console.error('Vibe Brutalism: Error setting progress:', error);
      return false;
    }
  };

  // ============================================
  // FORM VALIDATION
  // ============================================

  /**
   * Validates all required fields in a form and provides accessibility feedback
   * @param {HTMLElement} formElement - The form element to validate
   * @returns {boolean} True if all required fields are valid, false otherwise
   */
  window.VBValidateForm = function(formElement) {
    // Input validation
    if (!formElement || !(formElement instanceof HTMLElement)) {
      console.error('Vibe Brutalism: VBValidateForm requires a valid HTML element');
      return false;
    }

    try {
      const inputs = formElement.querySelectorAll('.vb-input[required], .vb-textarea[required]');
      let isValid = true;

      inputs.forEach(input => {
        // Get or create error message element
        let errorMessage = input.nextElementSibling;
        if (!errorMessage || !errorMessage.classList.contains('vb-form-error')) {
          errorMessage = document.createElement('div');
          errorMessage.className = 'vb-form-error vb-sr-only';
          errorMessage.setAttribute('role', 'alert');
          input.parentNode.insertBefore(errorMessage, input.nextSibling);
        }

        const fieldName = input.getAttribute('name') || input.getAttribute('id') || 'This field';

        if (!input.value.trim()) {
          input.style.borderColor = 'var(--vb-danger)';
          input.setAttribute('aria-invalid', 'true');
          errorMessage.textContent = `${fieldName} is required`;
          errorMessage.id = errorMessage.id || `error-${Math.random().toString(36).slice(2, 11)}`;
          input.setAttribute('aria-describedby', errorMessage.id);
          isValid = false;

          // Announce error to screen readers
          a11y.announce(`${fieldName} is required`, 'assertive');
        } else {
          input.style.borderColor = 'var(--vb-black)';
          input.setAttribute('aria-invalid', 'false');
          errorMessage.textContent = '';
          input.removeAttribute('aria-describedby');
        }
      });

      return isValid;
    } catch (error) {
      console.error('Vibe Brutalism: Error validating form:', error);
      return false;
    }
  };

  // ============================================
  // GLOBAL API
  // ============================================

  /**
   * Global VB object - Main API for Vibe Brutalism
   * @namespace VB
   * @property {Object} modals - Collection of modal instances by ID
   * @property {Object} carousels - Collection of carousel instances by ID
   * @property {Function} toast - Show a toast notification (recommended)
   * @property {Function} Toast - Show a toast notification (deprecated, use toast)
   * @property {Function} snackbar - Show a snackbar notification (recommended)
   * @property {Function} Snackbar - Show a snackbar notification (deprecated, use snackbar)
   * @property {Function} setProgress - Update a progress bar (recommended)
   * @property {Function} SetProgress - Update a progress bar (deprecated, use setProgress)
   * @property {Function} validateForm - Validate form fields (recommended)
   * @property {Function} ValidateForm - Validate form fields (deprecated, use validateForm)
   * @property {Function} announce - Announce message to screen readers
   */
  window.VB = {
    modals,
    carousels,

    // ============================================
    // MODERN API (camelCase - RECOMMENDED)
    // ============================================

    /**
     * Shows a toast notification (recommended modern API)
     * @param {string} message - Message to display
     * @param {string} [type='info'] - Toast type
     * @param {number} [duration=5000] - Duration in ms
     * @param {string} [position='top-right'] - Position
     * @returns {HTMLElement|null} Toast element or null
     */
    toast: (message, type, duration, position) => toastManager.show(message, type, duration, position),

    /**
     * Shows a snackbar notification (recommended modern API)
     * @param {string} message - Message to display
     * @param {string|null} [actionText] - Action button text
     * @param {Function|null} [actionCallback] - Action button callback
     * @param {number} [duration=5000] - Duration in ms
     */
    snackbar: (message, actionText, actionCallback, duration) => snackbar.show(message, actionText, actionCallback, duration),

    /**
     * Updates a progress bar (recommended modern API)
     * @param {string} elementId - Progress container ID
     * @param {number} percentage - Progress (0-100)
     * @returns {boolean} Success indicator
     */
    setProgress: (elementId, percentage) => window.VBSetProgress(elementId, percentage),

    /**
     * Validates form required fields (recommended modern API)
     * @param {HTMLElement} formElement - Form to validate
     * @returns {boolean} Validation result
     */
    validateForm: (formElement) => window.VBValidateForm(formElement),

    /**
     * Announces message to screen readers
     * @param {string} message - Message to announce
     * @param {string} [priority='polite'] - Priority level
     */
    announce: (message, priority) => a11y.announce(message, priority),

    // ============================================
    // LEGACY API (PascalCase - DEPRECATED)
    // Maintained for backward compatibility
    // ============================================

    /**
     * @deprecated Use VB.toast() instead
     * Shows a toast notification
     * @param {string} message - Message to display
     * @param {string} [type='info'] - Toast type
     * @param {number} [duration=5000] - Duration in ms
     * @param {string} [position='top-right'] - Position
     * @returns {HTMLElement|null} Toast element or null
     */
    Toast: (message, type, duration, position) => toastManager.show(message, type, duration, position),

    /**
     * @deprecated Use VB.snackbar() instead
     * Shows a snackbar notification
     * @param {string} message - Message to display
     * @param {string|null} [actionText] - Action button text
     * @param {Function|null} [actionCallback] - Action button callback
     * @param {number} [duration=5000] - Duration in ms
     */
    Snackbar: (message, actionText, actionCallback, duration) => snackbar.show(message, actionText, actionCallback, duration),

    /**
     * @deprecated Use VB.setProgress() instead
     * Updates a progress bar
     * @param {string} elementId - Progress container ID
     * @param {number} percentage - Progress (0-100)
     * @returns {boolean} Success indicator
     */
    SetProgress: window.VBSetProgress,

    /**
     * @deprecated Use VB.validateForm() instead
     * Validates form required fields
     * @param {HTMLElement} formElement - Form to validate
     * @returns {boolean} Validation result
     */
    ValidateForm: window.VBValidateForm
  };

  // Initialize accessibility features
  if (document.body) {
    a11y.createLiveRegion();
    console.log('🎨 Vibe Brutalism v2.0.0 initialized with WCAG 2.1 AA compliance!');
  } else {
    console.warn('Vibe Brutalism: document.body not available. Retrying on DOMContentLoaded...');
    document.addEventListener('DOMContentLoaded', () => {
      a11y.createLiveRegion();
      console.log('🎨 Vibe Brutalism v2.0.0 initialized with WCAG 2.1 AA compliance!');
    });
  }
})();
