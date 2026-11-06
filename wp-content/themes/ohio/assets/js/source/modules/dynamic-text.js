/**
 * Ohio Dynamic Text
 *
 * Typing effect (Typed.js) and Letter Reveal effect for the Dynamic Text
 * widget/shortcode. On build, assets/js/source/vendor/typed.min.js (Typed.js) is
 * prepended to this file, and the result is saved as assets/js/libs/dynamic-text.min.js.
 *
 * Loaded only on pages that use Dynamic Text (OhioHelper::add_required_script( 'dynamic-text' )).
 */
(function ($) {
    'use strict';

    // Letter Reveal: each letter rises in with a blur, holds, then floats out
    function OhioDynamicTextReveal(target, options) {
        var self = this,
            timing = options.reveal || {},
            reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        self.target = target;
        self.strings = (options.strings || []).filter(function (s) { return s !== ''; });
        self.loop = options.loop === true || options.loop === 'yes';
        self.duration = reducedMotion ? 0 : parseInt(timing.duration || 550, 10);
        self.stagger = reducedMotion ? 0 : parseInt(timing.stagger || 30, 10);
        self.hold = parseInt(timing.hold || 1200, 10);
        self.timers = [];
        self.index = 0;

        if (!self.strings.length) return;

        target.innerHTML = '';
        target.classList.add('-reveal');
        target.style.setProperty('--dt-reveal-duration', self.duration + 'ms');

        // Hidden copy used to measure each word's width
        self.measure = document.createElement('span');
        self.measure.className = 'dynamic-measure';
        self.measure.setAttribute('aria-hidden', 'true');
        target.appendChild(self.measure);

        self.onResize = function () { self.setWidth(self.strings[self.index], true); };
        window.addEventListener('resize', self.onResize);

        self.setWidth(self.strings[0], true);
        self.show(0);
    }

    OhioDynamicTextReveal.prototype.later = function (fn, ms) {
        this.timers.push(setTimeout(fn, ms));
    };

    OhioDynamicTextReveal.prototype.setWidth = function (text, instant) {
        var target = this.target;
        this.measure.innerHTML = text;
        if (instant) target.classList.remove('-animate-width');
        target.style.width = Math.ceil(this.measure.getBoundingClientRect().width) + 'px';
        if (instant) {
            void target.offsetWidth;
            target.classList.add('-animate-width');
        }
    };

    OhioDynamicTextReveal.prototype.buildWord = function (text) {
        var word = document.createElement('span'),
            tmp = document.createElement('span'),
            stagger = this.stagger;

        // Decode entities (e.g. &#39;) before splitting into letters
        tmp.innerHTML = text;
        word.className = 'dynamic-word';
        word.setAttribute('aria-label', tmp.textContent);

        Array.from(tmp.textContent).forEach(function (char, i) {
            var letter = document.createElement('span');
            letter.className = 'dynamic-letter';
            letter.setAttribute('aria-hidden', 'true');
            letter.textContent = char;
            letter.style.transitionDelay = (i * stagger) + 'ms';
            word.appendChild(letter);
        });
        return word;
    };

    OhioDynamicTextReveal.prototype.show = function (index) {
        var self = this,
            word = self.buildWord(self.strings[index]),
            count = word.children.length,
            isLast = index === self.strings.length - 1;

        self.index = index;
        self.target.appendChild(word);
        void word.offsetWidth;
        word.classList.add('-in');

        // Stop on the last word when Loop Mode is off
        if (isLast && !self.loop) return;
        if (self.strings.length < 2 && !self.loop) return;

        self.later(function () {
            var next = isLast ? 0 : index + 1;
            self.setWidth(self.strings[next]);
            word.classList.remove('-in');
            word.classList.add('-out');

            self.later(function () {
                word.remove();
                self.show(next);
            }, self.duration + count * self.stagger);
        }, self.duration + count * self.stagger + self.hold);
    };

    OhioDynamicTextReveal.prototype.destroy = function () {
        this.timers.forEach(clearTimeout);
        this.timers = [];
        window.removeEventListener('resize', this.onResize);
    };

    function handleDynamicText() {
        $( '[data-dynamic-text="true"]' ).each(function () {
            var options = JSON.parse($(this).attr('data-dynamic-text-options')),
                target = this.querySelector('.dynamic');

            if (!target) return;

            // Re-init safely (Elementor editor triggers this on every change)
            if (target._ohioDynamicText && target._ohioDynamicText.destroy) {
                target._ohioDynamicText.destroy();
            }

            if (options.effect === 'reveal') {
                target._ohioDynamicText = new OhioDynamicTextReveal(target, options);
            } else if (typeof Typed === 'function') {
                delete options.effect;
                delete options.reveal;
                target._ohioDynamicText = new Typed('#' + $(this).attr('id') + ' .dynamic', options);
            }
        });
    }
    window.ohioHandleDynamicText = handleDynamicText;

    $(window).on('load', function () {
        handleDynamicText();

        // Elementor editor re-inits widgets through this event
        setTimeout(function () {
            $(window).on('ohio:handle_ohio_dynamic_text', handleDynamicText);
        }, 1000);
    });

})(jQuery);
