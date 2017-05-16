/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
define([
    'jquery',
    'underscore',
    'mage/template',
    'priceUtils',
    'priceBox',
    'jquery/ui',
    'jquery/jquery.parsequery'
], function ($, _, mageTemplate) {
    'use strict';
    $.widget('mage.configurable', {
        options: {
            no_photo_url: "/store/pub/media/images/no-photo.png",
            ekr_product_ajax_url: "/store/ekr_configurableproduct/index/options",
            setup_complete:false,
            superSelector: '.super-attribute-select',
            selectSimpleProduct: '[name="selected_configurable_option"]',
            priceHolderSelector: '.price-box',
            spConfig: {},
            state: {},
            priceFormat: {},
            optionTemplate: '<%- data.label %>' +
            "<% if (typeof data.finalPrice.value !== 'undefined') { %>" +
            ' <%- data.finalPrice.formatted %>' +
            '<% } %>',
            mediaGallerySelector: '[data-gallery-role=gallery-placeholder]',
            mediaGalleryInitial: "/store/pub/media/images/no-photo.png",
            onlyMainImg: false,
            sizeOptions:{},
            erkOverlays: null,
            urlVars: null,
            inlayMapping:[
                {
                    id:"carbon_fiber",
                    label:"Carbon Fiber",
                    options:{
                        "Carbon Fiber":{
                            27: "Carbon Fiber",
                            28: "Silver Carbon Fiber"
                        }
                    }
                },
                {
                    id:"titanium",
                    label:"Titanium",
                    options:{
                        "Titanium":{
                            30: "Titanium",
                        }
                    }
                },
                {
                    id: "cobalt_chrome",
                    label:"Cobalt Chrome",
                    options:{
                        "Cobalt Chrome":{
                            32: "Cobalt Chrome",
                        }
                    },
                },
                {
                    id: "mosaic",
                    label:"Mosaic",
                    options:{
                    "Mosaic":{
                            38: "Lapis",
                        }
                    }
                },
                {
                    id:"sterling_silver",
                    label:"Sterling Silver",
                    options:{
                        "Sterling Silver":{
                            70: "Sterling Silver",
                        }
                    }
                },
                {
                    id:"palladium",
                    label:"Palladium",
                    options:{
                        "Palladium":{
                            74: "Palladium",
                        }
                    }
                },
                {   id:"hardwood",
                    label:"Hardwood",
                    options:{
                        "Hardwood":{
                            76: "Desert Iron Wood Burl",
                            77: "Desert Iron Wood",
                            78: "Red Heart",
                            79: "Osage Orange",
                            80: "Padauk",
                            81: "Boxelder Burl",
                            82: "Thuya Burl",
                            83: "Maple Burl",
                            84: "Spalted Tamarind",
                            85: "Wenge",
                            86: "Cocobollo",
                            87: "Leopard Wood",
                            88: "Canxan Burl",
                            89: "KOA"
                        }
                    }
                },
                {
                    id:"meteorite",
                    label:"Meteorite",
                    options:{
                        "Meteorite":{
                            29: "Meteorite"
                        }
                    },
                },
                {
                    id:"zirconium",
                    label:"Zirconium",
                    options:{
                        "Zirconium":{
                            31: "Zirconium"
                        }
                    },
                },
                {
                    id:"damascus",
                    label:"Damascus",
                    options:{
                        "Damascus":{
                            33: "Damascus",
                            34: "Zebra Damascus",
                            35: "Flattwist Damascus",
                            36: "Basketweave Damascus",
                            37: "Tiger Damascus"
                        }
                    }
                },
                {
                    id:"camo",
                    label:"Camo",
                    options:{
                        "King's Camo":{
                            39: "King's Field",
                            40: "King's Mountain",
                            41: "King's Snow",
                            42: "King's Woodland",
                            43: "King's Pink",
                            44: "King's Desert"
                        },
                        "RealTree Camo":{
                            45: "RealTree Advantage Max",
                            46: "RealTree AP",
                            47: "RealTree APC Black",
                            48: "RealTree APC Green",
                            49: "RealTree APC Maroon",
                            50: "RealTree APC Orange",
                            51: "RealTree APC Pink",
                            52: "RealTree APC Purple",
                            53: "RealTree APC Red",
                            54: "RealTree APC Snow",
                            55: "RealTree Timber",
                            56: "RealTree Advantage Max-4",
                            57: "RealTree APC Navy Blue",
                            58: "RealTree APG"
                        },
                        "Mossy Oak Camo":{
                            59: "MossyOak Bottomland",
                            60: "MossyOak Break Up Infinity",
                            61: "MossyOak Breakup",
                            62: "MossyOak Duck Blind",
                            63: "MossyOak Brush",
                            64: "MossyOak Winter Breakup",
                            65: "MossyOak Obsession",
                            66: "MossyOak Pink Bottomland",
                            67: "MossyOak Pink Breakup",
                            68: "MossyOak SG Blades",
                            69: "MossyOak Treestand"
                        }
                    }
                },
                {
                    id: "14K Gold",
                    label: "14K Gold",
                    options:{
                        "14K Gold":{
                            71: "14K White Gold",
                            72: "14K Yellow Gold",
                            73: "14K Rose Gold",
                        }
                    }
                },
                {
                    id: "Platinum",
                    label: "Platinum",
                    options:{
                        "Platinum":{
                            75: "Platinum"
                        }
                    }
                }
            ]
        },

        /**
         * Creates widget
         * @private
         */
        _create: function () {
            // check for preselected values for attributes
            var base_metal = this._ekrGetParameterByName('base_metal');
            var inlay = this._ekrGetParameterByName('inlay');
            var finish = this._ekrGetParameterByName('finish');

            this.options.urlVars = {
                base_metal: (base_metal == null)? "" :   base_metal,
                inlay: (inlay == null)? "" :   inlay,
                finish: (finish == null)? "" :   finish,
                ring_size: (finish == null)? "" :  '10.00'
            };

            // Initial setting of various option values
            this._initializeOptions();

            // Override defaults with URL query parameters and/or inputs values
            this._overrideDefaults();

            // Change events to check select reloads
            this._setupChangeEvents();

            // Fill state
            this._fillState();

            // Setup child and prev/next settings
            this._setChildSettings();

            // Setup/configure values to inputs
            this._configureForValues();

            // set up dropdown overlays.
            this._erkConfigureAttributeOverlays();

            // initial
            //this.options.setup_complete = true;
            //this._ekrAssignPreselectedValue(document.getElementById('attribute137'));

        },

        /**
         * Initialize tax configuration, initial settings, and options values.
         * @private
         */
        _initializeOptions: function () {
            var options = this.options,
                gallery = $(options.mediaGallerySelector),
                priceBoxOptions = $(this.options.priceHolderSelector).priceBox('option').priceConfig || null;

            if (priceBoxOptions && priceBoxOptions.optionTemplate) {
                options.optionTemplate = priceBoxOptions.optionTemplate;
            }

            if (priceBoxOptions && priceBoxOptions.priceFormat) {
                options.priceFormat = priceBoxOptions.priceFormat;
            }
            options.optionTemplate = mageTemplate(options.optionTemplate);

            options.settings = options.spConfig.containerId ?
                $(options.spConfig.containerId).find(options.superSelector) :
                $(options.superSelector);

            options.values = options.spConfig.defaultValues || {};
            options.parentImage = $('[data-role=base-image-container] img').attr('src');

            this.inputSimpleProduct = this.element.find(options.selectSimpleProduct);

            gallery.on('gallery:loaded', function () {
                var galleryObject = gallery.data('gallery');
                options.mediaGalleryInitial = galleryObject.returnCurrentImages();
            });
        },

        /**
         * Override default options values settings with either URL query parameters or
         * initialized inputs values.
         * @private
         */
        _overrideDefaults: function () {
            var hashIndex = window.location.href.indexOf('#');

            if (hashIndex !== -1) {
                this._parseQueryParams(window.location.href.substr(hashIndex + 1));
            }

            if (this.options.spConfig.inputsInitialized) {
                this._setValuesByAttribute();
            }
        },

        /**
         * Parse query parameters from a query string and set options values based on the
         * key value pairs of the parameters.
         * @param {*} queryString - URL query string containing query parameters.
         * @private
         */
        _parseQueryParams: function (queryString) {
            var queryParams = $.parseQuery({
                query: queryString
            });

            $.each(queryParams, $.proxy(function (key, value) {
                this.options.values[key] = value;
            }, this));
        },

        /**
         * Override default options values with values based on each element's attribute
         * identifier.
         * @private
         */
        _setValuesByAttribute: function () {
            this.options.values = {};
            $.each(this.options.settings, $.proxy(function (index, element) {
                var attributeId;

                if (element.value) {
                    attributeId = element.id.replace("attribute", '');
                    this.options.values[attributeId] = element.value;
                }
            }, this));
        },

        /**
         * Set up .on('change') events for each option element to configure the option.
         * @private
         */
        _setupChangeEvents: function () {
            $.each(this.options.settings, $.proxy(function (index, element) {
                $(element).on('change', this, this._configure);
            }, this));
        },

        /**
         * Iterate through the option settings and set each option's element configuration,
         * attribute identifier. Set the state based on the attribute identifier.
         * @private
         */
        _fillState: function () {
            $.each(this.options.settings, $.proxy(function (index, element) {
                var attributeId = element.id.replace("attribute", '');
                if (attributeId && this.options.spConfig.attributes[attributeId]) {
                    element.config = this.options.spConfig.attributes[attributeId];
                    element.attributeId = attributeId;
                    this.options.state[attributeId] = false;
                }
            }, this));
        },

        /**
         * Set each option's child settings, and next/prev option setting. Fill (initialize)
         * an option's list of selections as needed or disable an option's setting.
         * @private
         */
        _setChildSettings: function () {
            var childSettings = [],
                settings = this.options.settings,
                index = settings.length,
                option;

            while (index--) {
                option = settings[index];

                if (index) {
                    option.disabled = true;
                } else {
                    this._fillSelect(option);
                }

                _.extend(option, {
                    childSettings: childSettings.slice(),
                    prevSetting: settings[index - 1],
                    nextSetting: settings[index + 1]
                });

                childSettings.push(option);
            }
        },

        /**
         * Setup for all configurable option settings. Set the value of the option and configure
         * the option, which sets its state, and initializes the option's choices, etc.
         * @private
         */
        _configureForValues: function () {
            if (this.options.values) {
                this.options.settings.each($.proxy(function (index, element) {
                    var attributeId = element.attributeId;
                    element.value = this.options.values[attributeId] || '';
                    this._configureElement(element);
                }, this));
            }
        },

        /**
         * Event handler for configuring an option.
         * @private
         * @param {Object} event - Event triggered to configure an option.
         */
        _configure: function (event) {
            if(this.attributeId == "ring_size"){
                event.data._reloadPrice();
                event.data._changeProductImage();
            }else{
                event.data._configureElement(this);
            }

        },

        /**
         * Configure an option, initializing it's state and enabling related options, which
         * populates the related option's selection and resets child option selections.
         * @private
         * @param {*} element - The element associated with a configurable option.
         */
        _configureElement: function (element) {
            this.simpleProduct = this._getSimpleProductId(element);
            if (element.value) {
                this.options.state[element.config.id] = element.value;
                if (element.nextSetting) {
                    element.nextSetting.disabled = false;
                    this._fillSelect(element.nextSetting);
                    this._resetChildren(element.nextSetting);
                } else {
                    if (!!document.documentMode) {
                        this.inputSimpleProduct.val(element.options[element.selectedIndex].config.allowedProducts[0]);
                    } else {
                        this.inputSimpleProduct.val(element.selectedOptions[0].config.allowedProducts[0]);
                    }
                }
            } else {
                this._resetChildren(element);
            }
            this._reloadPrice();

            var widget = this;
            setTimeout(function(){
                widget._ekrTriggerChanges();
            },200);
        },
        /**
         * Change displayed product image according to chosen options of configurable product
         * @private
         */
        _changeProductImage: function () {
            var images = [],
                initialImages = $.extend(true, [], this.options.mediaGalleryInitial),
                galleryObject = $(this.options.mediaGallerySelector).data('gallery');

            if (this.options.spConfig.images[this.simpleProduct]) {

                var possible = $.extend(true, [], this.options.spConfig.images[this.simpleProduct]);
                var element = document.getElementById('attributefinish');
                var finish = element.options[element.selectedIndex].value;
                var images = [];

                if(finish.length > 0){
                    for(var i in possible){
                        var image = possible[i];
                        if(image.finish == finish) images.push(image);
                    }
                }else{
                    images = possible;
                }
            }

            function updateGallery(imagesArr) {
                var imgToUpdate,
                    mainImg;

                mainImg = imagesArr.filter(function (img) {
                    return img.isMain;
                });

                imgToUpdate = mainImg.length ? mainImg[0] : imagesArr[0];
                galleryObject.updateDataByIndex(0, imgToUpdate);
                galleryObject.seek(1);
            }

            if (galleryObject) {
                $(".ekr-image-disclaimer").each().remove();
                var disclaimer = document.getElementById("ekr-image-disclaimer-hidden");
                console.log(images);
                if (images.length) {
                    images.map(function (img) {
                        img.type = 'image';
                    });

                    if (this.options.onlyMainImg) {
                        updateGallery(images);
                    } else {
                        galleryObject.updateData(images)
                    }
                    $(".ekr-image-disclaimer").each().remove();
                    //disclaimer.setAttribute('style',"display:none;");
                } else {
                    // show blank image
/*                    images = [
                        {
                            caption:null,
                            full: this.options.no_photo_url,
                            img: this.options.no_photo_url,
                            isMain:true,
                            position:"1",
                            thumb:this.options.no_photo_url,
                        }
                    ];*/

                    var new_dis = $("#ekr-image-disclaimer-hidden").clone();
                    //new_dis.setAttribute('style',"");

                    $(".fotorama__stage__frame").append($(new_dis));
                    $(new_dis).addClass("ekr-image-disclaimer");
                    $(new_dis).show();

                    /*if (this.options.onlyMainImg) {
                        updateGallery(initialImages);
                    } else {
                        galleryObject.updateData(this.options.mediaGalleryInitial);
                        $(this.options.mediaGallerySelector).AddFotoramaVideoEvents();
                    }*/
//                    galleryObject.updateData(images);
                }


            }

            function getOffset( el ) {
                var _x = 0;
                var _y = 0;
                while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
                    _x += el.offsetLeft - el.scrollLeft;
                    _y += el.offsetTop - el.scrollTop;
                    el = el.offsetParent;
                }
                return { top: _y, left: _x };
            }
        },

        /**
         * For a given option element, reset all of its selectable options. Clear any selected
         * index, disable the option choice, and reset the option's state if necessary.
         * @private
         * @param {*} element - The element associated with a configurable option.
         */
        _resetChildren: function (element) {
            if (element.childSettings) {
                _.each(element.childSettings, function (set) {
                    set.selectedIndex = 0;
                    set.disabled = true;
                });

                if (element.config) {
                    this.options.state[element.config.id] = false;
                }
            }
        },

        /**
         * Populates an option's selectable choices.
         * @private
         * @param {*} element - Element associated with a configurable option.
         */
        _fillSelect: function (element) {
            var attributeId = element.id.replace("attribute", ''),
                match = false,
                options = this._getAttributeOptions(attributeId),
                prevConfig,
                index = 1,
                allowedProducts,
                i,
                j;
            this._clearSelect(element);
            element.options[0] = new Option('', '');
            element.options[0].innerHTML = this.options.spConfig.chooseText;
            prevConfig = false;

            if (element.prevSetting) {
                prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
            }

            if (options) {
                for (i = 0; i < options.length; i++) {
                    allowedProducts = [];

                    if (prevConfig) {
                        for (j = 0; j < options[i].products.length; j++) {
                            // prevConfig.config can be undefined
                            if (prevConfig.config &&
                                prevConfig.config.allowedProducts &&
                                prevConfig.config.allowedProducts.indexOf(options[i].products[j]) > -1) {
                                allowedProducts.push(options[i].products[j]);
                            }
                        }
                    } else {
                        allowedProducts = options[i].products.slice(0);
                    }

                    if (allowedProducts.length > 0) {
                        match = true;
                        options[i].allowedProducts = allowedProducts;
                        element.options[index] = new Option(this._getOptionLabel(options[i]), options[i].id);

                        if (typeof options[i].price !== 'undefined') {
                            element.options[index].setAttribute('price', options[i].prices);
                        }

                        element.options[index].config = options[i];
                        index++;
                    }
                }
                if(match){
                    var widget = this;
                    setTimeout(function(){
                        widget._ekrAssignPreselectedValue(element);
                    },100);

                }
            }
        },

        /**
         * Generate the label associated with a configurable option. This includes the option's
         * label or value and the option's price.
         * @private
         * @param {*} option - A single choice among a group of choices for a configurable option.
         * @return {String} The option label with option value and price (e.g. Black +1.99)
         */
        _getOptionLabel: function (option) {
            return option.label;
        },

        /**
         * Removes an option's selections.
         * @private
         * @param {*} element - The element associated with a configurable option.
         */
        _clearSelect: function (element) {
            var i;

            for (i = element.options.length - 1; i >= 0; i--) {
                element.remove(i);
            }
        },

        /**
         * Retrieve the attribute options associated with a specific attribute Id.
         * @private
         * @param {Number} attributeId - The id of the attribute whose configurable options are sought.
         * @return {Object} Object containing the attribute options.
         */
        _getAttributeOptions: function (attributeId) {
            if (this.options.spConfig.attributes[attributeId]) {
                return this.options.spConfig.attributes[attributeId].options;
            }
        },

        /**
         * Reload the price of the configurable product incorporating the prices of all of the
         * configurable product's option selections.
         */
        _reloadPrice: function () {
            $(this.options.priceHolderSelector).trigger('updatePrice', this._getPrices());
        },

        /**
         * Get product various prices
         * @returns {{}}
         * @private
         */
        _getPrices: function () {
            var prices = {},
                elements = _.toArray(this.options.settings),
                hasProductPrice = false;
            var widget = this;
            _.each(elements, function (element) {
                // calculate price based on ring size
                switch(element.attributeId){
                    case "ring_size":
                        var selected = element.options[element.selectedIndex],
                        priceValue = {};
                        for(var i in widget.options.sizeOptions){
                            var option = widget.options.sizeOptions[i];
                            if(option.option_type_id == selected.value){
                                if(parseFloat(option.default_price) != 0){
                                    priceValue = {
                                        finalPrice: {
                                            adjustments:{},
                                            amount: parseFloat(option.default_price)
                                        }
                                    }
                                }
                                break;
                            }
                        }
                    break;
                    case "finish":
                        var selected = element.options[element.selectedIndex],
                        price = widget.options.spConfig.finish_prices[selected.value];
                        var priceValue = {
                            finalPrice: {
                                adjustments:{},
                                amount: parseFloat(price)
                            }
                        };
                    break;
                    default:
                        var selected = element.options[element.selectedIndex],
                        config = selected && selected.config,
                        priceValue = {};

                        if (config && config.allowedProducts.length === 1 && !hasProductPrice) {
                            priceValue = this._calculatePrice(config);
                            hasProductPrice = true;
                        }
                    break;
                }
                prices[element.attributeId] = priceValue;
            }, this);

            // check price for ring size
            if(prices.ring_size.hasOwnProperty('finalPrice')){
                prices.ring_size.finalPrice.amount = prices.ring_size.finalPrice.amount  * widget.options.spConfig.price_multiplier;
            }
            // change price for finish
            if(prices.finish.hasOwnProperty('finalPrice')){
                prices.finish.finalPrice.amount = prices.finish.finalPrice.amount  * widget.options.spConfig.price_multiplier;
            }

            return prices;
        },

        /**
         * Returns pracies for configured products
         *
         * @param {*} config - Products configuration
         * @returns {*}
         * @private
         */
        _calculatePrice: function (config) {
            var displayPrices = $(this.options.priceHolderSelector).priceBox('option').prices,
                newPrices = this.options.spConfig.optionPrices[_.first(config.allowedProducts)];
            _.each(displayPrices, function (price, code) {
                if (newPrices[code]) {
                    displayPrices[code].amount = newPrices[code].amount - displayPrices[code].amount;
                }
            });

            return displayPrices;
        },

        /**
         * set up custom overlays
         * @return {[type]} [description]
         */
        _erkConfigureAttributeOverlays:function(){
            var widget = this;
            // click drowndown handlers.
            $(".ekr_attribute-handler").on('click',function(){
                var $me = $(this);
                var attribute_id = $me.data('id');
                var code = $me.data('code');
                if($("#attribute" + attribute_id).prop('disabled') == true) return;
                var options = (parseFloat(attribute_id) == 139)? widget._ekrFilterInlayOptions() : null;
                widget._ekrAddOptionsToOverlay(attribute_id,options);
                var $overlay = $("#choose_attribute" + attribute_id);
                switch($overlay.data('code')){
                    case "inlay":
                        $overlay.find(".inlay_category_options,.selected-base-metal").hide();
                        $("#accept-inlay-value").removeClass("show");
                    break;
                }
                $overlay.parent().show();
                widget._ekrResizeOverlayContainers();
                $overlay.show();
                $("#ekr-inlay-preview").css('background-image',"url('')");
            });
            // select attribute
            $(".ekr_overlay").on('click','.attribute',function(e){
                var $me = $(this);
                var $overlay = $me.closest(".ekr_overlay");
                // if inlay
                var code = $overlay.data('code');
                switch(code){
                    case "inlay":
                        widget._ekrInlayMetalChanged($me,$overlay);
                    break;
                    default:
                        widget._erkAttributeSelected($overlay.data("attributeid"),$me.data('value'));
                        $overlay.hide().parent().hide();
                    break;
                }
            }).on('click','.overlay-close',function(){
                var $overlay = $(this).parent();
                $overlay.hide().parent().hide();
            }).on('click','.filter-item',function(e){
                widget._ekrToggleSelectedInlay($(this));
            }).on('click','#link_change-base-metal',function(e){
                e.preventDefault();
                widget._changeInlayMetal($(this));
            }).on('click','.accept',function(e){
                var $me = $(this);
                e.preventDefault();
                widget._erkAttributeSelected(139,$me.attr('data-value'));
                var $overlay = $me.closest(".ekr_overlay");
                $overlay.hide().parent().hide();
            });
            widget._ekrBuildInlayOptions($("#choose_attribute139"));
            var $dp = $("#product-ekr-dropdowns");
            $dp.hide();
            widget.options.erkOverlays = $dp.children();
            // window resize
            $(window).resize(function() {
                widget._ekrResizeOverlayContainers();

            });
        },
        /**
         * check what options are available in the inlay select
         * and return the only ones that are available.
         * @return object inlay options
         */
        _ekrFilterInlayOptions: function(){
            var validOptions = [];
            var optionIds = [];
            var widget = this;
            $("#attribute139 option").each(function(){
                var value = $(this).attr('value');
                if(value != "") optionIds.push(parseFloat(value));
            });
            // loop through inlay mapping
            for(var i in widget.options.inlayMapping){
                var category = widget.options.inlayMapping[i];
                // loop through all options in category
                for(var heading in category.options){
                    //loop through all ids for each heading
                    var catIds = category.options[heading];
                    var match = false;
                    for(var iid in catIds){
                        iid = parseFloat(iid);
                        // compare select option ids with the current inlayID
                        if($.inArray(iid,optionIds) > -1){
                            match = true;
                        }else{
                            // remove this option it is not available.
                            delete catIds[iid];
                        }
                    }
                    if(match){
                        category.options[heading] = catIds;
                        var added = false;
                        for(var i in validOptions){
                            var cat = validOptions[i];
                            if(cat.id == category.id){
                                added = true;
                                break;
                            }
                        }
                        if(!added) validOptions.push(category);
                    }
                }
            }

            // save valid options to widget to use later.
            widget.options.validInlayOptions = validOptions;
            return validOptions;
        },
        _ekrResizeOverlayContainers:function(){
            var winWidth = $(window).width();
            if(winWidth > 836){
                $("#ekr-inlays-list").css('height',"");
                this.options.erkOverlays.each(function(){
                    $(this).find(".attribute-values").css('height',"");
                });
                return;
            }else{
                var inlayListPadding = (winWidth > 600 && winWidth <= 836)? 200 : 424;
            }
            var winHeight = $(window).height();
            var height = winHeight - 149;
            this.options.erkOverlays.each(function(){
                $(this).find(".attribute-values").css('height',height + "px");
            });
            height = winHeight - inlayListPadding;
            $("#ekr-inlays-list").css('height',height + "px");
        },
        _changeInlayMetal:function($link){
            var $overlay = $link.closest(".ekr_overlay");
            $overlay.find('.inlay_category_options').slideUp(500,function(){
                $overlay.find('.base-metals .selected-base-metal').fadeOut(500,function(){
                    $(this).next().slideDown(500);
                });
            });
        },

        /**
         * User selected an inlay for the list.
         * select clicked one and deselect prev selected.
         * @param  object $inlay jquery element for inlay
         * @return void
         */
        _ekrToggleSelectedInlay($inlay){
                if($inlay.hasClass("selected")) return;
                var $parent = $inlay.closest(".inlay-list");
                $parent.find(".filter-item.selected").removeClass("selected");
                $inlay.addClass("selected");
                var $accept = $("#accept-inlay-value");
                if(!$accept.hasClass('show')) $accept.addClass("show");

                var name = $inlay.text().replace("'","").replace(/ /g, "_").toLowerCase();
                var source = "/store/pub/media/images/inlays/" + name + ".jpg";
                // inlay image preview
                $("#ekr-inlay-preview").css('background-image',"url(" + source + ")");

                $accept.attr('data-value',$inlay.data('value'));
        },
        /**
         * user selected an inlay metal
         * @param  jquery $metal   metal element from overlay window
         * @param  jquery $overlay overlay window
         * @return void
         */
        _ekrInlayMetalChanged:function($metal,$overlay){
            // set name.
            var widget = this;
            var $selectedBaseMetal = $overlay.find(".selected-base-metal");
            // get options
            var options = [];
            for(var i in widget.options.validInlayOptions){
                var metal = widget.options.validInlayOptions[i];
                if(metal.id == $metal.data('value')){
                    options = metal.options;
                    break;
                }
            }
            // check how many options are there.
            var count = 0;
            var last = "";
            for(var i in options){
                for (var o_id in options[i]){
                    count++;
                    last = o_id;
                }
            }
            // if only one option
            if(count == 1){
                widget._erkAttributeSelected($overlay.data("attributeid"),last);
                $overlay.hide().parent().hide();
                return;
            }

            $selectedBaseMetal.find(".base-metal-name").html($metal.find(".attribute-name").text());
            $overlay.find(".attribute-values").slideUp(500,function(){
                $selectedBaseMetal.fadeIn(500);
                var $list = widget._ekrBuildInlayOptions($overlay,options);
                $("#accept-inlay-value").removeClass("show");
                $list.parent().slideDown();
                
            });
        },
        /**
         * check inlay mapping array display options available in dropdown.
         * @param  jqueryElement $overlay
         * @param array list of possible options.
         * @return jquery object list element
         */
        _ekrBuildInlayOptions:function($overlay,options){
            var attr_id = $overlay.data("attributeid");
            var $select = $("#attribute" + attr_id);
            var html = "";
            var counter = 1;
            html += "<div class=\"filter-section section1\">";
            for(var label in options){
                var list = options[label];
                html += "<div class=\"filter-list\">";
                html += this._ekrInlayOptionsGroup(label,list,$select);
                html += "</div>";
                counter++;
                if(counter == 3){
                    html += "</div>";
                    html += "<div class=\"filter-section section2\">";
                }
            }
            html += "</div>";
            var $list = $overlay.find("#ekr-inlays-list");
            $list.html(html);
            return $list;
        },
        /**
         * create group html
         * @param string group   the title for the group
         * @param array options list of options
         * @param object $select jquery object
         * @return string         section html
         */
        _ekrInlayOptionsGroup:function(group,options,$select){
            var str = "<div class=\"inlay-group-title\">" + group + "</div><ul>";
            for(var i in options){
                str += "<li data-value=\"" + i + "\" class=\"filter-item\">" + options[i] + "</li>";
            }
            str += "</ul>";
            return str;
        },

        /**
         * add options to custom overlay
         * @param  int attribute_id
         * @param  array options use values from array or copy what is on the select element
         */
        _ekrAddOptionsToOverlay:function(attribute_id,options){
            var widget = this;
            var $overlay = $("#choose_attribute" + attribute_id + " .attribute-values");
            $overlay.html("");
            if(options != null){
                for(var i in options){
                    var option = options[i];
                    var str = widget._ekrBuildAttributeOption(attribute_id,option.id,option.label);
                    $overlay.append(str);
                }
            }else{
                $("#attribute" + attribute_id + " option").each(function(){
                    var $option = $(this);
                    var value = $option.attr("value");
                    if(value != ""){
                        var str = widget._ekrBuildAttributeOption(attribute_id,value,$option.text());
                        $overlay.append(str);
                    }
                });

            }
            $overlay.show();
        },

        _ekrBuildAttributeOption:function(attribute_id,value,label){
            var filename = label.replace(/ /g, '_').replace(/\//g, '+').toLowerCase() + ".jpg";
            var bgr = "/store/pub/media/images/" + attribute_id + "/" + filename;
            var str = "<div class=\"attribute\" data-value=\""+ value + "\">\
                <div class=\"attribute-image\" style=\"background-image:url(" + bgr + ");\"></div>\
                <div class=\"attribute-name\">" + label + "</div>\
            </div>";
            return str;
        },

        /**
         * user clicked on an attribute value in the custom overlay
         * @param  int attribute_id
         * @param  int value_id     attribute value id
         * @return void
         */
        _erkAttributeSelected: function(attribute_id,value_id){
            $("#attribute" + attribute_id).val(value_id).trigger("change");
        },

        /**
         * Returns Simple product Id
         *  depending on current selected option.
         *
         * @private
         * @param {HTMLElement} element
         * @returns {String|undefined}
         */
        _getSimpleProductId: function (element) {
            // TODO: Rewrite algorithm. It should return ID of
            //        simple product based on selected options.
            var allOptions = element.config.options,
                value = element.value,
                config;
            config = _.filter(allOptions, function (option) {
                return option.id === value;
            });
            config = _.first(config);

            var product_id = _.isEmpty(config) ?
                undefined :
                _.first(config.allowedProducts);

            var attributeId = element.id;
            $("#ekr_simple_product").val(product_id);
            if(attributeId == 'attributefinish' && typeof(product_id) != 'undefined'){
                this._ekrGetSizeOptions(product_id);
            }
            return product_id;
        },
        _ekrGetSizeOptions: function (product_id){
            var widget = this;
            $.ajax({
                url: widget.options.ekr_product_ajax_url,
                type: "POST",
                dataType:"json",
                data:{
                    product_id:product_id,
                    action:'ring_size'
                },
                success:function(response){
                    widget.options.sizeOptions = response;
                    var element = document.getElementById("attributering_size");
                    widget._clearSelect(element);
                    element.options[0] = new Option('', '');
                    element.options[0].innerHTML = widget.options.spConfig.chooseText;
                    //this.options.spConfig.attributes->ring_size->options = [];
                    if(response.length){
                        var index = 1;
                        for(var i in response){
                            var option = response[i];
                            var title = option.default_title;
/*                          var diff = parseFloat(option.default_price);
                            if(diff != 0){
                                var operator = " + ";
                                if(diff < 0){
                                    diff = -(diff);
                                    operator = " - ";
                                }
                                title += operator + "$" + diff.toFixed(2);
                            }
*/
                            var opt = new Option(title, option.option_type_id);
                            element.options[index] = opt;
                            opt.setAttribute('ring_size',title);
                            /*this.options.spConfig.attributes->ring_size->options.push({

                            })*/
                            index ++;
                        }
                        widget._ekrAssignPreselectedValue(element);
                    }
                }
            });
        },
        _ekrGetParameterByName:function(name) {

            var url = window.location.href;

            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        },
        _ekrAssignPreselectedValue(element){
            console.log(element);
            //if(!this.options.setup_complete) return;
            var code = element.getAttribute('data-code');
            var urlVars = this.options.urlVars;
            var passed = urlVars[code];
            if(passed.length > 0){
                this.options.urlVars[code] = ''; //reset value
                var attribute_id = element.getAttribute('id').replace("attribute","");
                if(attribute_id == "ring_size"){
                    var $select = $(element);
                    var value = $select.find('option[ring_size="' + passed + '"]').attr('value');
                    $select.val(value).trigger('change');
                }else{
                    this._erkAttributeSelected(attribute_id,passed);
                }
            }
            this._ekrTriggerChanges();
        },
        _erkGetProductSimpleInfo:function(){
            var widget = this;
            $.ajax({
                url: this.options.ekr_product_ajax_url,
                type: "POST",
                dataType:"json",
                data:{
                    product_id:this.simpleProduct,
                    action:'product_info'
                },
                success:function(response){
                    var name = document.getElementById('ekr_product-sku');
                    var description = document.getElementById('ekr_product-description');
                    name.innerHTML = response.name;
                    description.innerHTML = response.description;
                    name.setAttribute('style','');
                }
            });
        },
        _ekrTriggerChanges:function(){
            console.log("triggering changes");
            var empty = true;
            var index;
            for(var i in this.options.urlVars){
                if(this.options.urlVars[i].length > 0){
                    empty = false;
                    index = i;
                    break;
                }
            }
            if(!empty) console.log("no empty yet " + this.options.urlVars[index]);
            if(!empty) return;
            this._changeProductImage();
            this._erkGetProductSimpleInfo();
            setTimeout(function(){$(".price").show(); },1000);
        }
    });

    return $.mage.configurable;
});