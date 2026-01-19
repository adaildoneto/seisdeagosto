/**
 * City Selector with Autocomplete
 * Uses Open-Meteo Geocoding API (free, no API key required)
 */
(function($) {
    'use strict';

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // City Selector Class
    class CitySelector {
        constructor(element, options = {}) {
            this.$container = $(element);
            this.options = $.extend({
                minChars: 3,
                debounceMs: 300,
                maxResults: 8,
                placeholder: 'Digite o nome da cidade...',
                language: 'pt',
                onSelect: null
            }, options);

            this.init();
        }

        init() {
            this.render();
            this.bindEvents();
        }

        render() {
            const html = `
                <div class="city-selector">
                    <div class="city-selector-input-wrap">
                        <input type="text" 
                               class="city-selector-input form-control" 
                               placeholder="${this.options.placeholder}"
                               autocomplete="off">
                        <span class="city-selector-spinner" style="display:none;">
                            <i class="fa fa-spinner fa-spin"></i>
                        </span>
                        <span class="city-selector-clear" style="display:none;">
                            <i class="fa fa-times"></i>
                        </span>
                    </div>
                    <ul class="city-selector-results list-group" style="display:none;"></ul>
                    <div class="city-selector-selected" style="display:none;">
                        <div class="selected-city-info">
                            <i class="fa fa-map-marker"></i>
                            <span class="selected-city-name"></span>
                            <span class="selected-city-coords"></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary city-selector-change">
                            <i class="fa fa-pencil"></i> Alterar
                        </button>
                    </div>
                </div>
            `;
            this.$container.html(html);

            // Cache elements
            this.$input = this.$container.find('.city-selector-input');
            this.$spinner = this.$container.find('.city-selector-spinner');
            this.$clear = this.$container.find('.city-selector-clear');
            this.$results = this.$container.find('.city-selector-results');
            this.$selected = this.$container.find('.city-selector-selected');
            this.$selectedName = this.$container.find('.selected-city-name');
            this.$selectedCoords = this.$container.find('.selected-city-coords');
            this.$changeBtn = this.$container.find('.city-selector-change');
        }

        bindEvents() {
            // Input typing with debounce
            const debouncedSearch = debounce((query) => {
                this.search(query);
            }, this.options.debounceMs);

            this.$input.on('input', (e) => {
                const query = e.target.value.trim();
                if (query.length >= this.options.minChars) {
                    this.$spinner.show();
                    this.$clear.hide();
                    debouncedSearch(query);
                } else {
                    this.hideResults();
                    this.$spinner.hide();
                    this.$clear.toggle(query.length > 0);
                }
            });

            // Clear button
            this.$clear.on('click', () => {
                this.$input.val('').focus();
                this.$clear.hide();
                this.hideResults();
            });

            // Result click
            this.$results.on('click', '.city-result-item', (e) => {
                const $item = $(e.currentTarget);
                const cityData = $item.data('city');
                this.selectCity(cityData);
            });

            // Keyboard navigation
            this.$input.on('keydown', (e) => {
                if (!this.$results.is(':visible')) return;

                const $items = this.$results.find('.city-result-item');
                const $active = $items.filter('.active');
                let index = $items.index($active);

                switch(e.keyCode) {
                    case 40: // Down
                        e.preventDefault();
                        index = Math.min(index + 1, $items.length - 1);
                        $items.removeClass('active').eq(index).addClass('active');
                        break;
                    case 38: // Up
                        e.preventDefault();
                        index = Math.max(index - 1, 0);
                        $items.removeClass('active').eq(index).addClass('active');
                        break;
                    case 13: // Enter
                        e.preventDefault();
                        if ($active.length) {
                            $active.click();
                        }
                        break;
                    case 27: // Escape
                        this.hideResults();
                        break;
                }
            });

            // Change button
            this.$changeBtn.on('click', () => {
                this.$selected.hide();
                this.$input.val('').show().focus();
            });

            // Click outside to close
            $(document).on('click', (e) => {
                if (!this.$container.is(e.target) && !this.$container.has(e.target).length) {
                    this.hideResults();
                }
            });
        }

        async search(query) {
            try {
                const url = new URL('https://geocoding-api.open-meteo.com/v1/search');
                url.searchParams.set('name', query);
                url.searchParams.set('count', this.options.maxResults);
                url.searchParams.set('language', this.options.language);
                url.searchParams.set('format', 'json');

                const response = await fetch(url);
                const data = await response.json();

                this.$spinner.hide();

                if (data.results && data.results.length > 0) {
                    this.showResults(data.results);
                } else {
                    this.showNoResults();
                }
            } catch (error) {
                console.error('City search error:', error);
                this.$spinner.hide();
                this.showError();
            }
        }

        showResults(cities) {
            const html = cities.map((city, index) => {
                const country = city.country || '';
                const admin = city.admin1 || '';
                const location = [admin, country].filter(Boolean).join(', ');
                
                // Country flag emoji (if country_code available)
                const flag = city.country_code ? this.getFlagEmoji(city.country_code) : '';

                return `
                    <li class="city-result-item list-group-item list-group-item-action ${index === 0 ? 'active' : ''}"
                        data-city='${JSON.stringify({
                            name: city.name,
                            latitude: city.latitude,
                            longitude: city.longitude,
                            country: country,
                            admin1: admin,
                            country_code: city.country_code,
                            timezone: city.timezone
                        })}'>
                        <div class="city-result-main">
                            <span class="city-result-flag">${flag}</span>
                            <strong class="city-result-name">${city.name}</strong>
                        </div>
                        <small class="city-result-location text-muted">${location}</small>
                        <small class="city-result-coords text-muted">
                            ${city.latitude.toFixed(4)}, ${city.longitude.toFixed(4)}
                        </small>
                    </li>
                `;
            }).join('');

            this.$results.html(html).show();
        }

        showNoResults() {
            this.$results.html(`
                <li class="list-group-item text-muted text-center">
                    <i class="fa fa-search"></i> Nenhuma cidade encontrada
                </li>
            `).show();
        }

        showError() {
            this.$results.html(`
                <li class="list-group-item text-danger text-center">
                    <i class="fa fa-exclamation-triangle"></i> Erro na busca
                </li>
            `).show();
        }

        hideResults() {
            this.$results.hide().empty();
        }

        selectCity(cityData) {
            this.hideResults();
            this.$input.hide();
            
            const flag = cityData.country_code ? this.getFlagEmoji(cityData.country_code) : '';
            const location = [cityData.admin1, cityData.country].filter(Boolean).join(', ');
            
            this.$selectedName.html(`${flag} <strong>${cityData.name}</strong> - ${location}`);
            this.$selectedCoords.text(`(${cityData.latitude.toFixed(4)}, ${cityData.longitude.toFixed(4)})`);
            this.$selected.show();

            // Trigger callback
            if (typeof this.options.onSelect === 'function') {
                this.options.onSelect(cityData);
            }

            // Trigger custom event
            this.$container.trigger('citySelected', [cityData]);
        }

        // Get selected city data
        getSelectedCity() {
            return this.selectedCity || null;
        }

        // Set city programmatically
        setCity(cityData) {
            this.selectedCity = cityData;
            this.selectCity(cityData);
        }

        // Clear selection
        clear() {
            this.selectedCity = null;
            this.$selected.hide();
            this.$input.val('').show();
        }

        // Convert country code to flag emoji
        getFlagEmoji(countryCode) {
            if (!countryCode || countryCode.length !== 2) return '';
            const codePoints = countryCode
                .toUpperCase()
                .split('')
                .map(char => 127397 + char.charCodeAt());
            return String.fromCodePoint(...codePoints);
        }
    }

    // jQuery plugin
    $.fn.citySelector = function(options) {
        return this.each(function() {
            if (!$.data(this, 'citySelector')) {
                $.data(this, 'citySelector', new CitySelector(this, options));
            }
        });
    };

    // Export for global use
    window.CitySelector = CitySelector;

})(jQuery);
