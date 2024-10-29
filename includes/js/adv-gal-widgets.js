"use strict";

(function ($) {
	'use strict';

	var $window = $(window);

	function advFilterNav($scope, filterFn) {
		var $filterNav = $scope.find('.adv-gal-js-filter'),
			defaultFilter = $filterNav.data('default-filter');

		if ($filterNav.length) {
			$filterNav.on('click.onFilterNav', 'button', function (event) {
				event.stopPropagation();
				var $current = $(this);
				$current.addClass('adv-gal-filter__item--active').siblings().removeClass('adv-gal-filter__item--active');
				filterFn($current.data('filter'));
			});
			$filterNav.find('[data-filter="' + defaultFilter + '"]').click();
		}
	}

	function debounce(func, wait, immediate) {
		var timeout;
		return function () {
			var context = this,
				args = arguments;

			var later = function later() {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};

			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	}

	$window.on('elementor/frontend/init', function () {
		var ModuleHandler = elementorModules.frontend.handlers.Base;

		/**
		* JUSTIFIED GALLERY
		*/

		var JustifiedGrid = ModuleHandler.extend({
			onInit: function onInit() {
				ModuleHandler.prototype.onInit.apply(this, arguments);
				this.run();
				$window.on('resize', debounce(this.run.bind(this), 100));
			},

			getDefaultElements: function getDefaultElements() {
				return {
					$container: this.findElement('.adv-justified-gallery'),
					$lbcontainer: this.findElement('[data-fancybox="adv-gallery"]')
				};
			},

			getDefaultSettings: function getDefaultSettings() {
				return {
					rowHeight: +this.getElementSettings('advRowHeight.size') || 150,
					lastRow: 'justify',
					margins: +this.getElementSettings('advImgMargin.size'),
					captions: false
				};
			},
			getLightBoxSettings: function getLightBoxSettings() {
				return {
					buttons: [
						"zoom",
						"slideShow",
						"fullScreen",
						"thumbs",
						"close"
					],
					arrows: true,
				};
			},
			onElementChange: function onElementChange(changedProp) {
				if (['advRowHeight', 'advImgMargin'].indexOf(changedProp) !== -1) {
					this.run();
				}
			},
			run: function run() {
				var self = this;
				self.elements.$container.justifiedGallery(this.getDefaultSettings());
				self.elements.$lbcontainer.fancybox(this.getLightBoxSettings());
			}
		});

		/**
		* Masonry GALLERY
		*/
		var MasonryGrid = ModuleHandler.extend({
			onInit: function onInit() {
				ModuleHandler.prototype.onInit.apply(this, arguments);
				this.run();
				$window.on('resize', debounce(this.run.bind(this), 100));
			},

			getDefaultSettings: function getDefaultSettings() {
				return {
					itemSelector: '.filter-item',
					gutter: +this.getElementSettings('advImgSpacing.size'),
					layoutMode: 'masonry'
				};
			},
			getDefaultElements: function getDefaultElements() {
				return {
					$container: this.findElement('.adv-gal-image-grid__wrap.adv-masonry-gallery'),
					$lbcontainer: this.findElement('[data-fancybox="adv-gallery"]')
				};
			},
			getLightBoxSettings: function getLightBoxSettings() {
				return {
					buttons: [
						"zoom",
						"slideShow",
						"fullScreen",
						"thumbs",
						"close"
					],
					arrows: true,
				};
			},
			onElementChange: function onElementChange(changedProp) {
				if (['advImgSpacing', 'advImgCol'].indexOf(changedProp) !== -1) {
					this.run();
				}
			},
			run: function run() {
				var self = this;
				self.elements.$container.isotope(this.getDefaultSettings());
				self.elements.$lbcontainer.fancybox(this.getLightBoxSettings());
			},
		});

		/**
		* Filterable Gallery
		*/
		var FilterGrid = ModuleHandler.extend({
			onInit: function onInit() {
				ModuleHandler.prototype.onInit.apply(this, arguments);
				this.run();
				this.runFilter();
			},

			getDefaultSettings: function getDefaultSettings() {
				return {
					itemSelector: '.filter-item',
					layoutMode: 'masonry',
					percentPosition: true,
					gutter: +this.getElementSettings('advImgSpacing.size')

				};
			},
			getDefaultElements: function getDefaultElements() {
				return {
					$container: this.findElement('.adv-gal-image-grid__wrap.adv-filterable-gallery'),
					$lbcontainer: this.findElement('[data-fancybox="adv-gallery"]')
				};
			},
			getLightBoxSettings: function getLightBoxSettings() {
				return {
					buttons: [
						"zoom",
						"slideShow",
						"fullScreen",
						"thumbs",
						"close"
					],
					arrows: true,
				};
			},
			onElementChange: function onElementChange(changedProp) {
				if (['advImgSpacing', 'advImgCol'].indexOf(changedProp) !== -1) {
					this.run();
				}
			},
			run: function run() {
				var self = this;
				self.elements.$container.isotope(this.getDefaultSettings());
				self.elements.$lbcontainer.fancybox(this.getLightBoxSettings());
			},
			runFilter: function runFilter() {
				var self = this;
				advFilterNav(this.$element, function (filter) {
					self.elements.$container.isotope({
						filter: filter,
					});
				});
			},
		});

		var classHandlers = {
			'advgallery-justified-gallery.default': JustifiedGrid,
			'advgallery-masonry-gallery.default': MasonryGrid,
			'advgallery-filterable-gallery.default': FilterGrid,
		};

		$.each(classHandlers, function (widgetName, handlerClass) {
			elementorFrontend.hooks.addAction('frontend/element_ready/' + widgetName, function ($scope) {
				elementorFrontend.elementsHandler.addHandler(handlerClass, {
					$element: $scope
				});
			});
		});
	});

})(jQuery);