;(function ($, window, document, undefined) {
    'use strict';
    var pluginName = "flicgal_jquery_plugin",
        defaults = {
            apiKey: "",
            photosetId: "",
            imageLimit: 200,
            errorText: "<div class='flicgal-fnf'>Error generating gallery.</div>",
            loadingSpeed: 38
        },
        apiUrl = 'https://api.flickr.com/services/rest/',
        photos = [];

    function Plugin(element, options) {
        this.element = $(element);
        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;

        this._hideSpinner = function() {
            this.element.find('.flicgal-spinner-wrapper').hide().find('*').hide();
        };

        this._printError = function() {
            this.element.find('.flicgal-gallery-container').append($("<div></div>", { "class": "flicgal-col-1" })
                .append($("<div></div>", { "class": "flicgal-error-wrapper" })
                    .append($("<span></span>", { "class": "flicgal-label flicgal-label-danger flicgal-error" })
                        .html(this.settings.errorText))));
        };

        this._flickrAnimate = function() {
            this.element.find('.flicgal-gallery-container img').each($.proxy(function(index, el) {
                var image = el;
                setTimeout(function() {
                    $(image).parent().fadeIn();
                }, this.settings.loadingSpeed * index);
            }, this));
        };

        this._printGallery = function(photos) {
            var element = this.element.find('.flicgal-gallery-container');
            if (!photos || photos.length === 0) {
                this._hideSpinner();
                this._printError();
                return;
            }

            // Ensure the container is a row
            element.addClass('flicgal-row');

            var _this = this;
            $.each(photos, function(key, photo) {
                if (!photo || !photo.thumbnail) return; 
                var img = $('<img>', { 'class': 'flicgal-thumb flicgal-img-thumbnail flicgal-img-responsive', src: photo.thumbnail, 'alt': photo.title });
                
                // Map Bootstrap classes to custom ones if needed
                var colClass = photo.colLayout || 'flicgal-col-4';
                colClass = colClass.replace('col-md-3', 'flicgal-col-4'); // 4 columns
                colClass = colClass.replace('col-md-4', 'flicgal-col-3'); // 3 columns
                
                element.append($('<div></div>', { 'class': 'flicgal-gallery-item ' + colClass })
                    .append($('<a></a>', { 'class': '', href: photo.href, 'data-gallery': '#blueimp-gallery-' + _this.settings.galleryId, 'title': photo.title }).hide()
                        .append(img)));
            });

            if ($.fn.imagesLoaded) {
                element.imagesLoaded()
                    .done($.proxy(this._flickrAnimate, this))
                    .always($.proxy(this._hideSpinner, this));
            } else {
                this._flickrAnimate();
                this._hideSpinner();
            }
        };

        this._flickrPhotoset = function(photoset) {
            var _this = this;
            photos[photoset.id] = [];
            $.each(photoset.photo, function(key, photo) {
                if (photo.media !== "photo") {
                    return true; 
                }
                photos[photoset.id][key] = {
                    thumbnail: 'https://farm' + photo.farm + '.static.flickr.com/' + photo.server + '/' + photo.id + '_' + photo.secret + '_q.jpg',
                    href: 'https://farm' + photo.farm + '.static.flickr.com/' + photo.server + '/' + photo.id + '_' + photo.secret + '_b.jpg',
                    title: photo.title,
                    colLayout: _this.settings.colLayout
                };
            });

            if (photos[photoset.id].length > 0) {
                this._printGallery(photos[photoset.id]);
            } else {
                this._hideSpinner();
                this._printError();
            }
        };

        this._onFlickrResponse = function(response) {
            if (response.stat === "ok") {
                this._flickrPhotoset(response.photoset);
            } else {
                this._hideSpinner();
                this._printError();
            }
        };

        this._flickrRequest = function(method, data) {
            var url = apiUrl + "?format=json&jsoncallback=?&method=" + method + "&api_key=" + this.settings.apiKey;
            $.each(data, function(key, value) {
                url += "&" + key + "=" + value;
            });

            $.ajax({
                dataType: "json",
                url: url,
                context: this,
                success: this._onFlickrResponse
            });
        };

        this._flickrInit = function () {
            this._flickrRequest('flickr.photosets.getPhotos', {
                photoset_id: this.settings.photosetId,
                per_page: this.settings.imageLimit,
                extras: 'media'
            });
        };

        this.init();
    }

    Plugin.prototype = {
        init: function () {
            this._flickrInit();
        }
    };

    $.fn[pluginName] = function (options) {
        this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
        return this;
    };
})(jQuery, window, document);
