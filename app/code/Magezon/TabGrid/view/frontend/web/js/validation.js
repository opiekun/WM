/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint jquery:true browser:true*/
/*global BASE_URL:true*/
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define([
            "jquery",
            "underscore",
            'Magento_Ui/js/modal/alert',
            "jquery/ui",
            "jquery/validate",
            "mage/translate",
            "mage/validation"
        ], factory);
    } else {
        factory(jQuery);
    }
}(function ($, _, alert) {
    "use strict";
    
    $.extend(true, $.validator.prototype, {
        /**
         * Focus invalid fields
         */
        focusInvalid: function() {
            if (this.settings.focusInvalid) {
                try {
                    $(this.errorList.length && this.errorList[0].element || [])
                        .focus()
                        .trigger("focusin");
                } catch (e) {
                    // ignore IE throwing errors when focusing hidden elements
                }
            }
        },
        elements: function () {
            var validator = this,
                rulesCache = {};

            // select all valid inputs inside the form (no submit or reset buttons)
            return $(this.currentForm)
                .find("input, select, textarea")
                .not(this.settings.forceIgnore)
                .not(':submit, :reset, :image, [disabled]')
                .not(this.settings.ignore)
                .filter(function () {
                    if (!this.name && validator.settings.debug && window.console) {
                        console.error('%o has no name assigned', this);
                    }

                    // select only the first element for each name, and only those with rules specified
                    if (this.name in rulesCache || !validator.objectLength($(this).rules())) {
                        return false;
                    }

                    rulesCache[this.name] = true;

                    return true;
                });
        }
    });

    $.extend($.fn, {
        /**
         * ValidationDelegate overridden for those cases where the form is located in another form,
         *     to avoid not correct working of validate plug-in
         * @override
         * @param {string} delegate - selector, if event target matched against this selector,
         *     then event will be delegated
         * @param {string} type - event type
         * @param {function} handler - event handler
         * @return {Element}
         */
        validateDelegate: function (delegate, type, handler) {
            return this.on(type, $.proxy(function (event) {
                var target = $(event.target);
                var form = target[0].form;
                if(form && $(form).is(this) && $.data(form, "validator") && target.is(delegate)) {
                    return handler.apply(target, arguments);
                }
            }, this));
        }
    });

    $.widget("mage.validation", $.mage.validation, {
        options: {
            messagesId: 'messages',
            forceIgnore: '',
            ignore: ':disabled, .ignore-validate, .no-display.template, ' +
                ':disabled input, .ignore-validate input, .no-display.template input, ' +
                ':disabled select, .ignore-validate select, .no-display.template select, ' +
                ':disabled textarea, .ignore-validate textarea, .no-display.template textarea',
            errorElement: 'label',
            errorUrl: typeof BASE_URL !== 'undefined' ? BASE_URL : null,
            highlight: function(element) {
                if ($.validator.defaults.highlight && $.isFunction($.validator.defaults.highlight)) {
                    $.validator.defaults.highlight.apply(this, arguments);
                }
                $(element).trigger('highlight.validate');
            },
            unhighlight: function(element) {
                if ($.validator.defaults.unhighlight && $.isFunction($.validator.defaults.unhighlight)) {
                    $.validator.defaults.unhighlight.apply(this, arguments);
                }
                $(element).trigger('unhighlight.validate');
            }
        },

        /**
         * Validation creation
         * @protected
         */
        _create: function() {
            if (!this.options.submitHandler && $.type(this.options.submitHandler) !== 'function') {
                if (!this.options.frontendOnly && this.options.validationUrl) {
                    this.options.submitHandler = $.proxy(this._ajaxValidate, this);
                } else {
                    this.options.submitHandler = $.proxy(this._submit, this);
                }
            }
            this.element.on('resetElement', function(e) {$(e.target).rules('remove');});
            this._super('_create');
        },

        /**
         * Validation listening
         * @protected
         */
        _listenFormValidate: function () {
            $('form').on('invalid-form.validate', function (event, validation) {
                var firstActive = $(validation.errorList[0].element || []),
                    lastActive = $(validation.findLastActive() || validation.errorList.length && validation.errorList[0].element || []);

                // if (lastActive.is(':hidden') && !$(this).find('_current').eq(0)) {
                //     var parent = lastActive.parent();
                //     var windowHeight = $(window).height();
                //     $('html, body').animate({
                //         scrollTop: parent.offset().top - windowHeight / 2
                //     });
                // }

                // ARIA (removing aria attributes if success)
                var successList = validation.successList;
                if (successList.length) {
                    $.each(successList, function () {
                        $(this)
                            .removeAttr('aria-describedby')
                            .removeAttr('aria-invalid');
                    })
                }
                if (firstActive.length) {
                    firstActive.focus();
                }
            });
        },

        /**
         * ajax validation
         * @protected
         */
        _ajaxValidate: function() {
            $.ajax({
                url: this.options.validationUrl,
                type: 'POST',
                dataType: 'json',
                data: this.element.serialize(),
                context: $('body'),
                success: $.proxy(this._onSuccess, this),
                error: $.proxy(this._onError, this),
                showLoader: true,
                dontHide: false
            });
        },

        /*
         * Process ajax success
         * @protected
         * @param {Object} JSON-response
         * @param {string} response status
         * @param {Object} The jQuery XMLHttpRequest object returned by $.ajax()
         */
        _onSuccess: function(response) {
            if (!response.error) {
                this._submit();
            } else {
                this._showErrors(response);
                $('body').trigger('processStop');
            }
        },

        /**
         * Submitting a form
         * @private
         */
        _submit: function() {
            $(this.element[0]).trigger('afterValidate.beforeSubmit');
            this.element[0].submit();
        },

        /**
         * Displays errors after backend validation.
         * @param {Object} data - Data that came from backend.
         */
        _showErrors: function(data) {
            $('body').notification('clear')
                .notification('add', {
                    error: data.error,
                    message: data.message,
                    insertMethod: function(message) {
                        $('.messages:first').html(message);
                    }
                });
        },

        /**
         * Tries to retrieve element either by id or by inputs' name property.
         * @param {String} code - String to search by.
         * @returns {jQuery} jQuery element.
         */
        _getByCode: function(code) {
            var parent = this.element[0],
                element;

            element = parent.querySelector('#' + code) || parent.querySelector('input[name=' + code + ']');

            return $(element);
        },

        /*
         * Process ajax error
         * @protected
         */
        _onError: function() {
            this.trigger('processStop');
            
            if (this.options.errorUrl) {
                location.href = this.options.errorUrl;
            }
        }
    });

    _.each({
        'validate-greater-zero-based-on-option': [
            function (v, el) {
                var optionType = $(el)
                    .closest('.form-list')
                    .prev('.fieldset-alt')
                    .find('select.select-product-option-type'),
                    optionTypeVal = optionType.val();
                v = Number(v) || 0;
                if (optionType && (optionTypeVal == 'checkbox' || optionTypeVal == 'multi') && v <= 0) {
                    return false;
                }
                return true;
            },
            'Please enter a number greater 0 in this field.'
        ],
        'validate-rating': [
            function () {
                var ratings = $('#detailed_rating').find('.field-rating'),
                    noError = true;

                ratings.each(function (index, rating) {
                    noError = noError && $(rating).find('input:checked').length > 0;
                });
                return noError;
            },
            'Please select one of each ratings above.'
        ],
        'validate-downloadable-file': [
            function (v, element) {
                var elmParent = $(element).parent(),
                    linkType = elmParent.find('input[value="file"]'),
                    newFileContainer;

                if (linkType.is(':checked') && (v === '' || v === '[]')) {
                    newFileContainer = elmParent.find('.new-file');
                    if (!alertAlreadyDisplayed && (newFileContainer.empty() || newFileContainer.is(':visible'))) {
                        alertAlreadyDisplayed = true;
                        alert({
                            content: $.mage.__('There are files that were selected but not uploaded yet. ' +
                            'Please upload or remove them first')
                        });
                    }
                    return false;
                }
                return true;
            },
            'Please upload a file.'
        ],
        'validate-downloadable-url': [
            function (v, element) {
                var linkType = $(element).parent().find('input[value="url"]');
                if (linkType.is(':checked') && v === '') {
                    return false;
                }
                return true;
            },
            'Please specify Url.'
        ]
    }, function (rule, i) {
        rule.unshift(i);
        $.validator.addMethod.apply($.validator, rule);
    });

    return $.mage.validation;
}));
