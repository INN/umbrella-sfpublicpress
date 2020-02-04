(function() {
    var $ = jQuery;

    var Navigation = function() {
      this.scrollTop = $(window).scrollTop();
      this.previousScroll = null;
      this.initialLoad = true;
      return this.init();
    };

    /*
     * This is a shim to cover for the case where a browser may or may not have scrollbars
     * @link https://github.com/jquery/jquery/issues/1729
     * @link https://github.com/INN/largo/pull/1369
     *
     * In some browsers, having the Inspector Tools docked within the browser in a sidebar
     * configuration may cause abnormal readings for this value.
     */
    Navigation.prototype.windowwidth = function() {
      return Math.max(window.innerWidth, $(window).width());
    }
  
    /**
     * Set up the Navigation object
     */
    Navigation.prototype.init = function() {
      // Dropdowns on touch screens
      this.enableMobileDropdowns();
      this.toggleTouchClass();
  
      // Stick navigation
      this.mainEl = $('#main');
      this.mainNavEl = $('#main-nav');
  
      // the currently-open menu Element (not a jQuery object);
      this.openMenu = false;
  
      // Initially hide the things that might be overflowing the nav
      this.stickyNavTransition();
  
      // Bind events
      this.bindEvents();
  
      // Deal with long/wrapping navs
      setTimeout(this.navOverflow.bind(this), 0);
  
      // Nav on small viewports
      this.responsiveNavigation();
  
      // Nav on touch devices on large viewports
      this.touchDropdowns();
  
      return this;
    };
  
    /**
     * Run the Modernizr.touch and Modernizr.pointerevents tests at will
     *
     * because Modernizr doesn't allow rerunning the tests, so the availablilty of an input device changes while the page is loaded, the Modernizr.touch property will be inaccurate
     *
     * @link https://github.com/Modernizr/Modernizr/blob/e2c27dcd32d6185846ce3c6c83d7634cfa402d19/feature-detects/touchevents.js
     * @link https://github.com/Modernizr/Modernizr/blob/e2c27dcd32d6185846ce3c6c83d7634cfa402d19/feature-detects/pointerevents.js
     * @return bool whether or not this is (probably) a touch device at this time
     */
    Navigation.prototype.touch = function () {
      if (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
        return true;
      }
  
      domPrefixes = Modernizr._domPrefixes;
      var bool = false,
             i = domPrefixes.length;
  
      // Don't forget un-prefixed...
      bool = Modernizr.hasEvent('pointerdown');
  
      while (i-- && !bool) {
        if (Modernizr.hasEvent(domPrefixes[i] + 'pointerdown')) {
          bool = true;
        }
      }
      return bool;
    }
  
    /**
     * If a nav dropdown element is open, and something outside is clicked, close the menu
     */
    Navigation.prototype.enableMobileDropdowns = function () {
      var self = this;
  
      // Call this to close the open menu
      var closeOpenMenu = function(event) {
        // If it is a touch event, get rid of the click events.
        if (event.type == 'touchstart') {
          $(this).off('click.touchDropdown');
        }
  
        if (self.openMenu) {
          if (self.openMenu.parentElement.contains( event.target ) ) {
            // gotta navigate.
            window.location = event.target.href;
            // for top-level items, the link is handled natively.
            // items in the dropdown, it isn't.
          }
  
          self.openMenu.parentNode.classList.remove('open');
          self.openMenu = false;
          // we can't event.preventDefault here because of Chrome/Opera:
          // https://www.chromestatus.com/feature/5093566007214080
        }
      }
  
      // Close the open menu when the user taps elsewhere
      // Should this be scoped to not be on document/html/body?
      // No; because div.global-nav-bg #page and div.footer-bg are separate things,
      // and together they do not cover all of body.
      $('body').on('touchstart.touchDropdown click.touchDropdown' , closeOpenMenu);
    };
  
    /**
     * Toggle the Modernizr-added .touch and .no-touch classes
     */
    Navigation.prototype.toggleTouchClass = function () {
      $html = $('html');
      if (this.touch()) {
        $html.addClass('touch').removeClass('no-touch');
      } else {
        $html.addClass('no-touch').removeClass('touch');
      }
    }
  
    Navigation.prototype.bindEvents = function() {
      $(window).resize(this.navOverflow.bind(this));
      $(window).resize(this.enableMobileDropdowns.bind(this));
      $(window).resize(this.toggleTouchClass.bind(this));
      $(window).resize(this.touchDropdowns.bind(this));
      this.bindStickyNavEvents();
    };
  
    /**
     * Attach sticky nav resize event handlers to their events
     */
    Navigation.prototype.bindStickyNavEvents = function() {
      var self = this;
  
      // This is so that we may apply styles to the navbar based on what options are set
      // This is used with some styles in less/inc/navbar-sticky.less
      $.each(Largo.sticky_nav_options, function(idx, opt) {
        if (opt)
          self.mainNavEl.addClass(idx);
      });
  
      $(window).on('scroll resize', this.stickyNavScrollCallback.bind(this));
  
      this.stickyNavResizeCallback();
    };
  
    Navigation.prototype.stickyNavResizeCallback = function() {
      this.stickyNavTransitionDone();
    };
  
  
    /**
     * Determine whether or not to show or hide the sticky nav in response to scrolling
     */
    Navigation.prototype.stickyNavScrollCallback = function(event) {
      if (
        $(window).scrollTop() < 0
        || ( $(window).scrollTop() + $(window).outerHeight() ) >= $(document).outerHeight()
      ) {
        // if we're scrolled past the top of the page
        // or if the window is taller than the document
        // then it doesn't make sense to do the logic in this function.
        return;
      }
  
      if (
        this.windowwidth() > 768
        && !Largo.sticky_nav_options.sticky_nav_display
      ) {
        // if we're in a non-mobile case and the sticky nav is set to not display
        // then we should not be changing the status of the sticky nav based on height.
        return;
      }
  
      var self = this,
          direction = this.scrollDirection(),
          callback, wait;
  
      // this.mainEl (#main) exists in all Largo templates.
      if ( $(window).scrollTop() <= this.mainEl.offset().top ) {
        // we're near the top of the page, so now let's consider whether to hide the sticky nav:
        // if main_nav_hide_article is true, mainNavEl won't exist.
        if ( this.mainNavEl.length && this.mainNavEl.is(':visible') ) {
          // the main nav exists and is visible,
          // so we should hide the sticky nav
          this.mainNavEl.removeClass('show');
          clearTimeout(this.scrollTimeout);
          return; // don't need to do the other logic; it shouldn't show anyways
        }
      }
  
      if (
        !this.previousScroll
      ) {
        // if the page has not been scrolled,
        // Update the scroll direction,
        // then continue to the logic that would control whether to show it or not.
        this.previousScroll = direction;
      } else if ( this.previousScroll == direction ) {
        // if we're scrolling in the same direction,
        // update the scroll direction,
        // and end this function because the directional code has nothing to add.
        this.previousScroll = direction;
        return;
      }
  
      clearTimeout(this.scrollTimeout);
  
      if (direction == 'up') {
        callback = this.mainNavEl.addClass.bind(this.mainNavEl, 'show'),
        wait = 250;
      } else if (direction == 'down') {
        callback = this.mainNavEl.removeClass.bind(this.mainNavEl, 'show');
        wait = 500;
      }
  
      this.scrollTimeout = setTimeout(callback, wait);
      this.previousScroll = direction;
    };
  
    Navigation.prototype.scrollDirection = function() {
      var scrollTop = $(window).scrollTop(),
          direction;
  
      if (scrollTop > this.scrollTop)
        direction = 'down';
      else
        direction = 'up';
  
      this.scrollTop = scrollTop;
      return direction;
    };
  
    /**
     * Touch/click event handler for sticky nav and main nav items
     *
     * Goals:
     * - open when tapped, event.preventDefault
     * - when open, click on link follows that link
     *
     * Largo does not support a three-level menu, so no need to worry about dropdowns off the dropdown.
     *
     * @todo: prevent this from triggering on the mobile nav
     */
    Navigation.prototype.touchDropdowns = function() {
  
      /*
       * Define some event handlers
       */
  
      // Open the drawer when touched or clicked
      function touchstart(event) {
        // prevents this from running when the sandwich menu button is visible:
        // prevents this from running when we're doing the "phone" menu
        if ($('.navbar .toggle-nav-bar').css('display') !== 'none') {
          return false;
        }
  
        if ( $(this).closest('.dropdown').hasClass('open') ) {
        } else {
          // If it is a touch event, get rid of the click events.
          if (event.type == 'touchstart') {
            $(this).off('click.toggleNav');
          }
          $(this).parent('.dropdown').addClass('open');
          $(this).parent('.dropdown').addClass('open');
          self.openMenu = this;
          event.preventDefault();
          event.stopPropagation();
        }
      }
  
      // if the touch is canceled, close the nav
      function touchcancel(event) {
        $(this).parent('.dropdown').removeClass('open');
      }
  
      /*
       * Attach or detach them as appropriate
       */
  
      var self = this;
  
      // a selector that applies to both main-nav and sticky nav elements
      $('.nav li > .dropdown-toggle').each(function() {
        var $button = $(this);
  
        if(self.windowwidth() > 768 ){
          $button.on('touchstart.toggleNav click.toggleNav', touchstart);
          $button.on('touchcancel.toggleNav', touchcancel);
          $button.off('touchstart.toggleNav click.toggleNav');
          $button.off('touchcancel.toggleNav');
        }
      });
  
    }
  
    /**
     * Touch menu interactions and menu appearance on "phone" screen sizes.
     */
    Navigation.prototype.responsiveNavigation = function() {
      var self = this;
  
      // Tap/click this button to open/close the phone navigation, which shows on narrower viewports
      $('.navbar .toggle-nav-bar').each(function() {
        // the hamburger
        var toggleButton = $(this),
          // the parent nav of the hamburger
          navbar = toggleButton.closest('.navbar');
  
        // Support both touch and click events
        // The .toggleNav here is namespacing the click event: https://api.jquery.com/on/#event-names
        toggleButton.on('touchstart.toggleNav click.toggleNav', function(event) {
          // If it is a touch event, get rid of the click events.
          if (event.type == 'touchstart') {
            toggleButton.off('click.toggleNav');
          }
  
          navbar.toggleClass('open');
          $('html').addClass('nav-open');
          navbar.find('.nav-shelf').css({
            top: self.mainNavEl.position().top + self.mainNavEl.outerHeight()
          });
  
          if (!navbar.hasClass('open')) {
            navbar.find('.nav-shelf li.open').removeClass('open');
            $('html').removeClass('nav-open');
            self.navOverflow();
          }
  
          return false;
        });
  
        // Secondary nav items in the drop-down
        navbar.on('touchstart.toggleNav click.toggleNav', '.nav-shelf .caret', function(event) {
          // prevents this from running when the sandwich menu button is not visible:
          // prevents this from running when we're not doing the "phone" menu
          if (toggleButton.css('display') == 'none') {
            return false;
          }
  
          if (event.type == 'touchstart') {
            navbar.off('click.toggleNav', '.nav-shelf .dropdown-toggle');
          }
  
          var li = $(event.target).closest('li');
  
          if (!li.hasClass('open')) {
            navbar.find('.nav-shelf li.open').removeClass('open');
          }
  
          li.toggleClass('open');
          return false;
        });
      });
    };
  
    /**
     * On window resize, make sure nav doesn't overflow.
     * Put stuff in the overflow nav if it does.
     *
     * Event should fire enough that we can do one at a time
     * and be ok.
     *
     * @since Largo 0.5.1
     */
    Navigation.prototype.navOverflow = function() {
  
      var button = this.mainNavEl.find('.toggle-nav-bar'),
          shelfWidth = this.mainNavEl.outerWidth(),
          caretWidth = this.mainNavEl.find('.caret').first().outerWidth();
  
      if (!this.mainNavEl.hasClass('transitioning')) {
        this.stickyNavTransition();
      }
  
      /*
       * Calculate the width of the nav
       */
      var navWidth = 0;
      this.mainNavEl.find('ul.nav > li').each(function() {
        if ($(this).is(':visible'))
          navWidth += $(this).outerWidth();
      });
  
  
      if ( !this.mainNavEl.hasClass('open') ) {
        if ( navWidth > shelfWidth - caretWidth ) {
          var li = this.mainNavEl.find('ul.nav > li.menu-item:not(.overflowed)').last();
  
          li.addClass('overflowed');
          li.data('shelfwidth', shelfWidth);
          this.mainNavEl.addClass('has-overflow');
        } else if ( this.mainNavEl.find('.overflowed').length) {
          /*
           * Put items back on the main sticky menu and empty out the overflow nav menu if necessary.
           */
          var li = this.mainNavEl.find('li.overflowed').first();
  
          if (li.data('shelfwidth') < shelfWidth) {
            li.removeClass('overflowed');
          }
        }
      }
  
      // recheck if there's overflowed items
      var overflowed = this.mainNavEl.find('li.overflowed');
      if ( overflowed.length == 0 || overflowed.length == undefined ) {
        this.mainNavEl.removeClass('has-overflow');
      }
  
      /*
       * Re-calculate the width of the nav after adding/removing overflow items.
       *
       * If the nav is still wrapping, call navOverflow again.
       */
      var navWidth = 0;
      this.mainNavEl.find('ul.nav > li').each(function() {
        if ($(this).is(':visible'))
          navWidth += $(this).outerWidth();
      });
      shelfWidth = this.mainNavEl.outerWidth();
  
      if (navWidth > shelfWidth - caretWidth) {
        if (typeof this.navOverflowTimeout !== 'undefined')
          clearTimeout(this.navOverflowTimeout);
        this.navOverflowTimeout = setTimeout(this.navOverflow.bind(this), 0);
        return;
      }
  
      this.stickyNavTransitionDone();
    };
  
    Navigation.prototype.stickyNavTransition = function() {
      if (!this.mainNavEl.hasClass('transitioning')) {
        this.mainNavEl.addClass('transitioning');
      }
    };
  
    Navigation.prototype.stickyNavTransitionDone = function() {
      var self = this;
  
      if (typeof this.stickyNavTransitionTimeout !== 'undefined')
        clearTimeout(this.stickyNavTransitionTimeout);
  
      this.stickyNavTransitionTimeout = setTimeout(function() {
        if (self.mainNavEl.hasClass('transitioning'))
          self.mainNavEl.removeClass('transitioning');
      }, 500);
    };
  
    if (typeof window.Navigation == 'undefined') {
      window.Navigation = Navigation;
    }
  
    /**
     * Initialize the Navigation
     */
    $(document).ready(function() {
      // make this Navigation available to inspectors.
      window.Largo.navigation = new Navigation();
    });
  })();