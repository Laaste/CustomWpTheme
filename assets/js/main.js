import $, { ajaxSettings, param } from 'jquery';
import 'slick-carousel-latest';
// import '../vendor/bootstrap-datepicker-1.9.0/bootstrap-datepicker.min.js';
// import '../vendor/bootstrap-datepicker-1.9.0/bootstrap-datepicker.pl.min.js';
// import 'sharer.js';
// import AOS from 'aos';
import tippy, { createSingleton } from 'tippy.js';

const projectNameSpace = 'customproject';

var windowWidth = $(window).width();
var windowHeight = $(window).height();

$(function()
{
	manageOnDraw();

	bootSlicks(); //once
});

$(window).on('resize', debounce(function()
{
	manageOnDraw();
}, 200));

$('.js-bg-btn-under').on('click', function(e)
{
	if($('.js-bg-under').is(':visible'))
	{
		setCookie('debugshowunder', 0, '1');
	}
	else
	{
		setCookie('debugshowunder', 1, '1');
	}

	$('.js-bg-under').fadeToggle();
});

$('.js-bg-btn-above').on('click', function(e)
{
	if($('.js-bg-above').is(':visible'))
	{
		setCookie('debugshowabove', 0, '1');
	}
	else
	{
		setCookie('debugshowabove', 1, '1');
	}

	$('.js-bg-above').fadeToggle();
});


import { 
	OverlayScrollbars,
	ScrollbarsHidingPlugin,
	SizeObserverPlugin,
	ClickScrollPlugin
} from 'overlayscrollbars';

//////////////////////////////
//#region Utility

function vh(){
	return Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
}

function setCookie(cname, cvalue, exdays)
{
	var d = new Date();
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));

	var expires = 'expires=' + d.toUTCString();
	document.cookie = cname + '=' + cvalue + ';' + expires + ';path=/';
};

function getCookie(cname)
{
	var name = cname + '=';
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');

	for (var i = 0; i < ca.length; i++)
	{
		var c = ca[i];

		while (c.charAt(0) == ' ')
		{
			c = c.substring(1);
		};

		if (c.indexOf(name) == 0)
		{
			return c.substring(name.length, c.length);
		};
	};

	return false;
};

function deleteCookie(name, path = '/', domain)
{
	if(getCookie(name))
	{
		document.cookie = name + "=" +
			((path) ? ";path="+path:"")+
			((domain)?";domain="+domain:"") +
			";expires=Thu, 01 Jan 1970 00:00:01 GMT";
	}
}

function getParameterByName(name, url = window.location.href)
{
	name = name.replace(/[\[\]]/g, '\\$&');
	var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
		results = regex.exec(url);
	if (!results) return null;
	if (!results[2]) return '';

	return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function addOrUpdateUrlParameter(url, param, value)
{
	const urlObj = new URL(url);
	urlObj.searchParams.set(param, value);
	return urlObj.toString();
}

function removeUrlParameter(url, param)
{
	const urlObj = new URL(url);
	urlObj.searchParams.delete(param);
	return urlObj.toString();
}

function debounce(func, timeout = 300)
{
	let timer;

	return (...args) =>
	{
		clearTimeout(timer);

		timer = setTimeout(() =>
		{
			func.apply(this, args);
		}, timeout);
	};
}

// function redirectToLanguageSection(languageCode) {
// 	let currentUrl = window.location.href;

// 	currentUrl = currentUrl.replace(/\/(en|pl|fr|de|es)\//, "/");

// 	let newUrl;

// 	if (languageCode == 'pl') {
// 		newUrl = currentUrl.replace(window.location.origin + "/pl", window.location.origin);
// 	} else {
// 		newUrl = currentUrl.replace(window.location.origin, `${window.location.origin}/${languageCode}`);
// 	}

// 	window.location.href = newUrl;
// }

function copyTextToClipboard(valueToCopy)
{
	if(! navigator.clipboard)
	{
		try
		{
			document.execCommand('copy');
		} catch (err)
		{}
	}
	else
	{
		navigator.clipboard.writeText(valueToCopy).then(
			function() {},
			function(err) {}
		);
	}
}


/**
 * @param {*} nameSpace - assign to window by this name
 * @param {*} functionName - name to call by execFunction
 * @param {*} closure - function it self
 */
function namespaceFunction(nameSpace, functionName, closure)
{
	var context = window;

	// doesnt exists or empty
	if(! context.hasOwnProperty(nameSpace)
	|| Object.keys(context[nameSpace]) === 0)
	{
		context[nameSpace] = {};
	}

	context[nameSpace][functionName] = closure;
}

/**
 * Args are passed
 * 
 * @param {*} alias - nameSpace.functionName
 * @returns 
 */
function execFunction(alias /*, args */)
{
	var context = window;

	var args = Array.prototype.slice.call(arguments, 2);

	var namespaces = alias.split(".");

	var func = namespaces.pop();
	for(var i = 0; i < namespaces.length; i++)
	{
		context = context[namespaces[i]];
	}

	return context[func].apply(context, args);
}

// Button to hide admnin bar
document.addEventListener('DOMContentLoaded', function() {
	const adminBar = document.getElementById('wpadminbar');

	if(! adminBar) return;

	const toggleBtn = document.createElement('button');
	toggleBtn.innerText = '☰';
	toggleBtn.style.position = 'fixed';
	toggleBtn.style.bottom = '0';
	toggleBtn.style.left = '0';
	toggleBtn.style.zIndex = '10000';
	toggleBtn.style.background = '#23282d';
	toggleBtn.style.color = '#fff';
	toggleBtn.style.border = 'none';
	toggleBtn.style.padding = '5px 10px';
	toggleBtn.style.cursor = 'pointer';

	document.body.appendChild(toggleBtn);

	toggleBtn.addEventListener('click', function() {
		adminBar.classList.toggle('hidden');

		if(adminBar.classList.contains('hidden'))
		{
			document.documentElement.style.setProperty('margin-top', '0', 'important');
		}
		else
		{
			document.documentElement.style.removeProperty('margin-top');
		}
	});
});

//#endregion Utility
//////////////////////////////


//////////////////////////////
//#region Base

OverlayScrollbars.plugin(ClickScrollPlugin);

function toggleScrollState(osInstance, state = null){
	// get the current behavior
	var currentBehavior = osInstance.options().overflow;

	var newBehaviour = state;
	if(state === true)
	{
		newBehaviour = 'scroll';
	}
	else if(state === false)
	{
		newBehaviour = 'hidden';
	}
	else if(newBehaviour === null)
	{
		newBehaviour = (currentBehavior.y == 'scroll') ? 'hidden' : 'scroll'
	}

	// set the new behavior & update
	osInstance.options({
		overflow: {
			y: currentBehavior.y = newBehaviour,
			// y: currentBehavior.y == 'scroll'
		}
	});
}

function changeScrollState(osInstance, state){
	// set the new behavior & update
	osInstance.options({
		overflow: {
			y: state
		}
	});
}

// If options object is omitted, OverlayScrollbars won't work.
// var osInstanceBody = OverlayScrollbars(document.body,
// {
// 	overflow: {
// 		x: 'hidden'
// 	},
// 	scrollbars: {
// 		autoHide: 'scroll',
// 		autoHide: 'never',
// 		clickScroll: true
// 	},
// });

function makeScrollbars(elementSelector = '.js-scrollbar')
{
	var elementsWithScrollbars = $(elementSelector);

	if (elementsWithScrollbars.length)
	{
		elementsWithScrollbars.each(function()
		{
			var $el = $(this);
			var bp = $el.attr('data-breakpoint');
			var windowWidth = window.innerWidth;

			var ranges = {
				sm: windowWidth <= 575.98,
				md: windowWidth >= 576 && windowWidth <= 991.98,
				lg: windowWidth >= 992
			};

			var allow = false;

			if(! bp)
			{
				allow = true;
			}
			else
			{
				var breakpoints = bp.split(',').map(b => b.trim());
				allow = breakpoints.some(b => ranges[b]);
			}

			var osInstance = $el.data('osInstance');

			if(allow)
			{
				if(! osInstance)
				{
					osInstance = OverlayScrollbars(
					{
						target: $el[0],
					},
					{
						overflow: {
							x: 'hidden',
						},
						scrollbars: {
							autoHide: 'never',
							visibility: 'visible',
							clickScroll: true,
						},
					});

					$el.data('osInstance', osInstance);
				}
			}
			else
			{
				if(osInstance)
				{
					osInstance.destroy();
					$el.removeData('osInstance');
				}
			}
		});
	}
}

function addToBodyClassWithSuffix(baseClass, suffix)
{
	var body = $('body');

	body.addClass(baseClass + suffix);
}

function changeFontSize()
{
	var fontSize = getCookie('fontsize');

	if(fontSize)
	{
		$(':root').css('--fontsize', fontSize);

		addToBodyClassWithSuffix('fontsize--', fontSize);
		removeFromBodyClassesWithSuffix('fontsize--', fontSize);
	}

	$('.js-font-size').on('click', function()
	{
		var size = $(this).attr('data-size'); // font-size: x * calc(var(--font-size) / 100%);

		if(size)
		{
			$(':root').css('--fontsize', size);

			setCookie('fontsize', size, 14);

			addToBodyClassWithSuffix('fontsize--', size);
			removeFromBodyClassesWithSuffix('fontsize--', size);

			refreshSlicks();
		}
	});
}

//Anything that should be callled after load and on resize
function manageOnDraw()
{
	windowWidth = $(window).width();
	windowHeight = $(window).height();

	makeScrollbars();

	// adjustItemsHeightInColumns(
	// {
	// 	containerSelector: '.js-adjust-items-height',
	// 	toAdjustSelectors: [
	// 		'.js-adjust-items-height-title',
	// 		'.js-adjust-items-height-text',
	// 	],
	// 	columnsInRowLg: false,
	// 	columnsInRowMd: false,
	// 	columnsInRowSm: false,
	// });

	///////////////
}

//////////////////////////////
// #region slideDownOnHover
$(function()
{
	var hideTimeouts = {};

	$(document).on('mouseenter', '.js-hover-toggle', function()
	{
		var that = $(this);
		var id = that.index(); // lub możesz użyć unikalnego ID, jeśli istnieje
		var targetSelector = that.attr('data-hover-target');

		clearTimeout(hideTimeouts[id]);

		$(targetSelector).not(that.find(targetSelector)).stop(true, true).slideUp();
		that.find(targetSelector).stop(true, true).slideDown();
	});

	$(document).on('mouseleave', '.js-hover-toggle', function()
	{
		var that = $(this);
		var id = that.index();
		var targetSelector = that.attr('data-hover-target');

		hideTimeouts[id] = setTimeout(function()
		{
			that.find(targetSelector).stop(true, true).slideUp();
		}, 500);
	});

	$(document).on('mouseenter', '.js-hover-toggle-target', function()
	{
		var parent = $(this).closest('.js-hover-toggle');
		var id = parent.index();

		clearTimeout(hideTimeouts[id]);
	});

	$(document).on('mouseleave', '.js-hover-toggle-target', function()
	{
		var parent = $(this).closest('.js-hover-toggle');
		var id = parent.index();
		var targetSelector = parent.attr('data-hover-target');

		hideTimeouts[id] = setTimeout(function()
		{
			parent.find(targetSelector).stop(true, true).slideUp();
		}, 500);
	});

	$(document).on('click touchstart', '.js-hover-toggle', function(e)
	{
		var that = $(this);

		if(windowWidth < 992)
		{
			setTimeout(function(e)
			{
				var targetSelector = that.attr('data-hover-target');
	
				if(that.find(targetSelector).is(':visible'))
				{
					that.find(targetSelector).stop(true, true).slideUp();
				}
				else
				{
					$(targetSelector).not(that.find(targetSelector)).stop(true, true).slideUp();
					that.find(targetSelector).stop(true, true).slideDown();
				}
			}, 150);
		}
	});
});
// #endregion slideDownOnHover
//////////////////////////////

var menuOpen = false;

$(document).on('click', '.js-hamburger', function (e)
{
	var $that = $(this);

	menuOpen = !menuOpen;

	$that.toggleClass('active');

	$('body').toggleClass('overflow-hidden');

	$('.js-header').fadeToggle();
	$('.js-header-links').toggleClass('active');

	$('.header').toggleClass('active');

	if(typeof osInstanceBody != 'undefined')
	{
		toggleScrollState(osInstanceBody);
	}
});

$(document).on('click', '.js-header-expand-close', function(e)
{
	var $headerExpand = $('.js-header-links');
	$headerExpand.removeClass('active');
	$('.header').removeClass('active');
	$('.js-hamburger').removeClass('active');
	$('body').removeClass('overflow-hidden');
	
	var $headerList = $('.js-header-list');
	var $headerContent = $('.js-header-content');

	$headerList.show();
	$headerContent.hide();
	$('.js-header-parent').removeClass('active');

	if(typeof osInstanceBody != 'undefined')
	{
		toggleScrollState(osInstanceBody, true);
	}
});

$(document).on('click', '.js-scroll', function (e){
	e.preventDefault();
	e.stopPropagation();

	var target = $(this).attr('href');
	var targetObj = $(target);

	if(targetObj.length){
		var offset = targetObj.offset().top;

		if(target == '.js-test-ride-page') offset -= 50;

		$('html, body').animate({
			scrollTop: offset
		}, 500);
	} else {
		setCookie('scrollTo', target, 1);
		setTimeout(function(e){
			window.location.href = baseUrl;
		}, 100);
	}

	// if(windowWidth > 992)
	// {
	// 	target = ($($(this).attr('href')).offset().top - $('header').outerHeight());
	// }
	// else
	// {
		// target = $($(this).attr('href')).offset().top;
	// }
});


/**
 * Scroll to a target element based on nesting and selector.
 * 
 * @event click
 * @selector .js-scroll-to-next
 * 
 * @data-target {string} selector of the target element (e.g., ".class", "#id", "tag")
 * @data-nesting {string} optional, defines the search scope relative to the current element:
 *      "inner"   - inside the current element
 *      "above"   - in parent elements
 *      "sibling" - in following siblings (default)
 *      "prev"    - in previous siblings
 *      "exact"   - search in dom for first occurent
 */
$('.js-scroll-to-next').on('click', function(e)
{
	e.preventDefault();
	e.stopPropagation();

	var that = $(this);
	var selector = that.attr('data-target') || 'section';
	var nesting = that.attr('data-nesting') || 'sibling'; 

	// sekcja (rodzic najwyższego poziomu wg selektora)
	var parentEl = that.closest(selector);
	var targetEl;

	switch(nesting)
	{
		case 'inner':
			targetEl = that.find(selector).first();
			break;
		case 'above':
			targetEl = parentEl.nextAll(selector).first();
			break;
		case 'sibling':
			targetEl = that.nextAll(selector).first();
			break;
		case 'prev':
			targetEl = that.prevAll(selector).first();
			break;
		case 'exact':
			targetEl = $(selector).first();
			break;
		default:
			targetEl = that.nextAll(selector).first();
	}

	if(targetEl.length)
	{
		scrollToEl(targetEl);
	}

	if(targetEl && targetEl.length)
	{
		scrollToEl(targetEl);
	}
});

var scrolling = false;

/**
 * Scroll do elementu z opcjonalnym kontenerem i czasem animacji
 * @param {jQuery} targetObj - element do którego scrollujemy
 * @param {jQuery} [container=$('html, body')] - opcjonalny kontener scrolla
 * @param {number} [duration=500] - czas animacji w ms
 */
function scrollToEl(targetObj, container = $('html, body'), duration = 500, offset = 0, isOz = false)
{
	if(scrolling) return false;

	scrolling = true;

	if(!targetObj?.length)
	{
		return;
	}

	if(isOz === true)
	{
		var osInstance = container.data('osInstance');

		if(osInstance)
		{
			const { viewport } = osInstance.elements();

			// 1. Pobierz wysokość paddingu / sticky header (jeśli jest)

			// 2. Oblicz docelowy scroll
			var top = targetObj.offset().top;

			viewport.scrollTo({
				top: top,
				behavior: 'smooth'
			});
		}

		return;
	}

	let scrollOffset = targetObj.offset().top;

	container.animate(
	{
		scrollTop: scrollOffset + offset
	}, duration);

	var header = $('.js-header-fixed');

	if(header.length)
	{
		header.addClass('header--hidden');
	}

	scrolling = false;
}


$(function(){
	var scrollTo = getCookie('scrollTo');

	if(scrollTo){
		var targetObj = $(scrollTo);

		if(targetObj?.length){
			setTimeout(function(e){
				if(targetObj?.length){
					// var offset = targetObj.offset().top - $('header').outerHeight();
					var offset = targetObj.offset().top;

					$('html, body').animate({
						scrollTop: offset
					}, 500);
				}
			}, 100);
		}
	}

	deleteCookie('scrollTo');
});

$('.js-copy-to-clipboard').on('click', function(e)
{
	var $that = $(this);

	var valueToCopy = $that.attr('data-to-copy');

	if(valueToCopy)
	{
		copyTextToClipboard(valueToCopy);
	}
});

function convertRemToPixels(rem)
{
	return rem * parseFloat(getComputedStyle(document.documentElement).fontSize);
}

function pxToRem(value)
{
	return value / parseFloat(getComputedStyle(document.documentElement).fontSize);
}

/**
 * Adjusts the heights of elements in a grid-like layout so that items in the same row
 * have the same height. The number of columns per row can be customized for different
 * screen sizes (large, medium, small).
 *
 * @param {Object} options - Configuration object
 * @param {string} options.containerSelector - CSS selector for the container element - like row that contains columns
 * @param {string[]} [options.toAdjustSelectors=[]] - Array of CSS selectors for items to adjust - like each text inside column
 * @param {number|false} [options.columnsInRowLg=4] - Number of columns in a row for large screens (≥ 992px). Set to false to disable
 * @param {number|false} [options.columnsInRowMd=false] - Number of columns in a row for medium screens (≥ 576px and < 992px). Set to false to disable
 * @param {number|false} [options.columnsInRowSm=false] - Number of columns in a row for small screens (< 576px). Set to false to disable
 */
function adjustItemsHeightInColumns(
{
	containerSelector,
	toAdjustSelectors = [],
	columnsInRowLg = 4,
	columnsInRowMd = false,
	columnsInRowSm = false
})
{
	var container = $(containerSelector);

	if(container.length)
	{
		var columns = columnsInRowSm;

		if(windowWidth >= 576 && windowWidth < 992)
		{
			columns = columnsInRowMd;
		}
		else if(windowWidth >= 992)
		{
			columns = columnsInRowLg;
		}

		if(columns !== false)
		{
			$.each(toAdjustSelectors, function(index, itemToAdjustSelector)
			{
				var elements = container.find(itemToAdjustSelector).filter(':visible');

				if(elements.length)
				{
					// Reset height to read starting height
					elements.css('height', 'auto');

					var heightPerRow = [];

					setTimeout(function()
					{
						// Czytanie wysokości tylko widocznych elementów
						elements.each(function(i, elementRead)
						{
							var rowNo = Math.ceil((i + 1) / columns);
							var rowIndex = rowNo - 1;

							var elementObjRead = $(elementRead);
							var elementHeight = pxToRem(elementObjRead.innerHeight());

							if(heightPerRow[rowIndex] === undefined || elementHeight > heightPerRow[rowIndex])
							{
								heightPerRow[rowIndex] = elementHeight;
							}
						});

						// Ustawianie wysokości tylko widocznych elementów
						elements.each(function(i, elementSet)
						{
							var rowNoSet = Math.ceil((i + 1) / columns);
							var rowIndexSet = rowNoSet - 1;

							$(elementSet).css('height', heightPerRow[rowIndexSet] + 'rem');
						});
					}, 100);
				}
			});
		}
	}
}

$(document).on('click', '.js-close-target', function(e)
{
	var that = $(this);

	var targetSelector = that.attr('data-target');

	if(! targetSelector)
	{
		return;
	}

	var target = $(targetSelector)

	if(target.length)
	{
		target.fadeOut();
	}
});

$(document).on('click', '.js-open-target', function(e)
{
	var that = $(this);

	var targetSelector = that.attr('data-target');

	if(! targetSelector)
	{
		return;
	}

	var target = $(targetSelector)

	if(target.length)
	{
		target.fadeIn();

		var scrollToOpen = that.attr('data-scroll');

		if(typeof scrollToOpen != 'undefined'
		&& scrollToOpen != 'false')
		{
			scrollToEl(target);
		}
	}
});

// #region cslSelect
/**
 * Custom select with search on options list
 */
$(document).on('click', '.js-csl-container', function(e)
{
	var that = $(this);

	if(that.attr('data-prevent-default') != "false")
	{
		e.preventDefault();
	}

	if(that.attr('data-stop-propagation') != "false")
	{
		e.stopPropagation();
	}

	var that = $(this);

	cslOpenOptions(that);
	cslHightlightMatches(that);
});

$(document).on('click', '.js-csl-option', function(e)
{
	e.preventDefault();
	e.stopPropagation();

	var that = $(this);

	var customClick = that.attr('data-custom-click');

	if(customClick !== "true"
	&& customClick !== true)
	{
		cslOption(that);
	}
});

function cslOption(that)
{
	var container = that.closest('.js-csl-container');
	var value = that.attr('data-value');
	var isMulti = container.attr('data-multiselect') === 'true';
	var checkbox = that.find('input[type="checkbox"]');

	var selectedValues = JSON.parse(container.attr('data-selected-values') || '[]');

	if(! isMulti)
	{
		container.find('.js-csl-option.selected').each(function()
		{
			var prev = $(this);

			if(prev[0] !== that[0])
			{
				prev.removeClass('selected');
				prev.find('input[type="checkbox"]').prop('checked', false);
				selectedValues = selectedValues.filter(v => v !== prev.attr('data-value'));
			}
		});
	}

	// Toggle currne option
	if(selectedValues.includes(value))
	{
		selectedValues = selectedValues.filter(v => v !== value);
		that.removeClass('selected');
		checkbox.prop('checked', false);
	}
	else
	{
		selectedValues = isMulti ? [...selectedValues, value] : [value];
		that.addClass('selected');
		checkbox.prop('checked', true);
	}

	container.attr('data-selected-values', JSON.stringify(selectedValues));

	cslUpdateCurrent(container);

	if(!isMulti) cslCloseOptions(container);
}

$(document).on('input', '.js-csl-current', function(e)
{
	e.preventDefault();
	e.stopPropagation();

	var that = $(this);
	var container = that.closest('.js-csl-container');
	var value = that.val();

	if(! value
	|| value.length == 0)
	{
		that.attr('data-value', JSON.stringify(['default']));
	}

	cslHightlightMatches(container);
});

function cslUpdateCurrent($container)
{
	var $input = $container.find('.js-csl-current');
	var defaultLabel = $input.attr('data-default-label') || 'Wybierz';
	var selectedValues = JSON.parse($container.attr('data-selected-values') || '[]');

	var $inputError = $container.find('.js-input-error');

	$input.removeClass('error');

	if($inputError.length)
	{
		$inputError.hide();
	}
	
	if(selectedValues.length === 0)
	{
		if($input.is('input'))
		{
			$input.val(defaultLabel);
		}
		else
		{
			$input.html(defaultLabel);
		}

		$input.removeClass('choosed');
		$input.attr('data-value', '[]');
	}
	else
	{
		var labels = selectedValues.map(function(v)
		{
			var $option = $container.find(`.js-csl-option[data-value="${v}"]`)
			var optionLabel = $option.attr('data-label');

			if(optionLabel === undefined
			|| ! optionLabel.trim().length)
			{
				optionLabel = $option.text();
			}

			return optionLabel.trim();
		});

		//If declared then assing values to it too
		var valueTargetSelector = $container.attr('data-value-target');

		if(valueTargetSelector
		&& valueTargetSelector.length)
		{
			var valueTarget = $container.find(valueTargetSelector);
			valueTarget.val(selectedValues.join(', '));
		}

		if($input.is('input'))
		{
			$input.val(labels.join(', '));
		}
		else
		{
			$input.html(labels.join(', '));
		}

		$input.addClass('choosed');
		$input.attr('data-value', JSON.stringify(selectedValues));
	}
}

function cslHightlightMatches(container, search)
{
	var input = container.find('.js-csl-current');

	if(! input.length)
	{
		return false;
	}

	var search = input.val().trim().toLowerCase();
	var options = container.find('.js-csl-option');

	if(options.length
	&& search.length)
	{
		options.each(function(index, optionHtml)
		{
			var option = $(optionHtml);
			var optionText = option.text().trim().toLowerCase();
			var optionWords = optionText.split(' ');

			var anyWordMatch = optionWords.some(word => word.startsWith(search) && search.length);

			if(anyWordMatch)
			{
				option.addClass('hightlight');
				option.show();
			}
			else
			{
				option.removeClass('hightlight');
				option.hide();
			}
		});
	}
	else
	{
		options.removeClass('hightlight');
		options.show();
	}
}

function cslUnhightlightMatches(container)
{
	var options = container.find('.js-csl-option');

	if(options.length)
	{
		options.removeClass('hightlight');
		options.show();
	}
}

function cslOpenOptions(container)
{
	if(container?.length)
	{
		var options = container.find('.js-csl-options');

		if(options.length)
		{
			options.slideDown();
		}

		container.addClass('active');
	}
}

function cslCloseOptions(container)
{
	if(container.length)
	{
		var options = container.find('.js-csl-options');

		if(options?.length)
		{
			cslUnhightlightMatches(container);

			options.slideUp();
		}

		container.removeClass('active');
	}
}

$(document).on("click", function(event) 
{
	var cslContainers = $(".js-csl-container");

	cslContainers.each(function(index, cslContainerElement)
	{
		var cslContainer = $(cslContainerElement);

		if(cslContainer.length
		&& ! cslContainer.is(event.target)
		&& cslContainer.has(event.target).length == 0) 
		{
			cslCloseOptions(cslContainer);
		}
	});
});

//#endregion cslSelect
////////////////////////////////

function removeFromBodyClassesWithSuffix(baseClass, exceptionSuffixes = [])
{
	var body = $('body');
	var classes = body.attr('class') ? body.attr('class').split(/\s+/) : [];

	classes.forEach(function(singleClass)
	{
		var currentSuffix = singleClass.slice(baseClass.length);

		if(singleClass.startsWith(baseClass)
		&& ! exceptionSuffixes.includes(currentSuffix))
		{
			body.removeClass(singleClass);
		}
	});
}

$(function()
{
	var fontSize = getCookie('fontsize');

	if(fontSize)
	{
		$(':root').css('--fontsize', fontSize);

		addToBodyClassWithSuffix('fontsize--', fontSize);
		removeFromBodyClassesWithSuffix('fontsize--', fontSize);
	}

	$('.js-font-size').on('click', function()
	{
		var size = $(this).attr('data-size'); // font-size: x * calc(var(--font-size) / 100%);

		if(size)
		{
			$(':root').css('--fontsize', size);

			setCookie('fontsize', size, 14);

			addToBodyClassWithSuffix('fontsize--', size);
			removeFromBodyClassesWithSuffix('fontsize--', size);

			refreshSlicks();
		}
	});
});

//#endregion Base
//////////////////////////////


/////////////////////////
//#region Slick
//
var isDraggingOnSlider = false;

var introSliders = [];

var sliders = [];

function bootSlicks()
{
	bootIntroSliders();

	sliders = {
		'introSliders': introSliders,
	};
}

function bootIntroSliders()
{
	var $sliders = $('.js-intro-slider');

	if($sliders.length)
	{
		$sliders.each(function(e)
		{
			var slider = this;
			introSliders.push(slider);

			var $slider = $(slider);
			var $sliderContainer = $slider.closest('.js-intro-slider-container');
			var $sliderNav = $sliderContainer.find('.js-intro-slider-nav');
			var slidesCount = $sliderContainer.find('.js-intro-slide').length;

			var $introSlider = $slider.slick(
			{
				arrows: false,
				dots: false,
				infinite: false,
				slidesToShow: 1,
				variableWidth: false,
				adaptiveHeight: true,
				autoplay: true,
				autoplaySpeed: 28000,
				lazyLoad: 'anticipated',
				responsive: [
					{
						breakpoint: 991,
						settings: {
							slidesToShow: 1,
						}
					}
				]
			});

			var $sliderNavButtons = $sliderNav.find('.js-tabs-button');
			var $currentNavButton = $sliderNavButtons.filter('active');

			// var $prevButton = $currentNavButton.find('.js-base-slider-prev');
			// var $nextButton = $currentNavButton.find('.js-base-slider-next');

			// $prevButton.on('click', function()
			// {
			// 	$introSlider.slick('slickPrev');
			// });

			// $nextButton.on('click', function()
			// {
			// 	$introSlider.slick('slickNext');
			// });

			$introSlider.on('afterChange', function(event, slick, index)
			{
				// updateNavState(slick, $prevButton, $nextButton);
				if(! blockOnChangeIntroSlider)
				{
					$sliderNavButtons.removeClass('active');
					$currentNavButton = $sliderNavButtons.eq(index).addClass('active');

					var $tabsContainer = $sliderNavButtons.closest('.js-tabs-container');
					var $tabs = $tabsContainer.find('.js-tabs-item');

					var $allTabsVideos = $tabs.find('[data-lazy-video-ready]')
					$allTabsVideos.attr('data-lazy-video-ready', 'false');

					var $newCurrentTab = $tabs.eq(index);
					var $currentTabVideos = $newCurrentTab.find('[data-lazy-video-ready]')
					$currentTabVideos.attr('data-lazy-video-ready', 'true');

					processLazyVideoScroll();
				}
			});
		});
	}
}

function currentSlideInLoop(currentSlide, delta, totalFrames)
{
	if(totalFrames <= 0)
		return 0;

	let direction = delta > 0 ? -1 : 1;
	let newSlide = (currentSlide + direction + totalFrames) % totalFrames;

	return newSlide;
}

function updateNavState(slick, $prevButton, $nextButton)
{
	var current = slick.currentSlide;
	var max = slick.slideCount - slick.options.slidesToShow;

	if(current > 0)
	{
		$prevButton.addClass('active');
	}
	else
	{
		$prevButton.removeClass('active');
	}

	if(current < max)
	{
		$nextButton.addClass('active');
	}
	else
	{
		$nextButton.removeClass('active');
	}
}

function refreshSlicks()
{
	for(var [sliderType, sliderEntries] of Object.entries(sliders))
	{
		if(sliderEntries.length)
		{
			sliderEntries.forEach(function(sliderEl)
			{
				var $slider = $(sliderEl);

				if($slider.length
				&& $slider.hasClass('slick-initialized'))
				{
					$slider.slick('refresh');
				}
			});
		}
	}
}

//
//#endregion Slick
/////////////////////////

$(document).on('click', '.js-video-poster', function(e)
{
	var $thatVideoPoster = $(this);
	var $videoContainer = $thatVideoPoster.closest('.js-video-container');

	if($videoContainer?.length)
	{
		var $video = $videoContainer.find('.js-video');

		if($video?.length)
		{
			$video.attr('src', $video.attr('data-src'));
			$video.css('visibility', 'visible');
			$thatVideoPoster.hide();

			$video[0].play();
		}
	}
});

////////////////////////////////////////
// Lazyloading

var offsetTop = $(window).scrollTop();
var minScrollTime = 100;

var dataLazyVideoArray = [];
var lazyVideoAttr = 'data-lazy-video';

function processLazyVideoScroll()
{
	offsetTop = $(window).scrollTop();

	$.each(dataLazyVideoArray, function(index, element)
	{
		var source = $(element);
		var video = source.closest('video').get(0);

		if(video.dataset.loaded) return;

		if(source.attr('data-lazy-video-ready') == 'false') return;

		// === BREAKPOINT CHECK ===
		var breakpoint = source.data('lazy-video-breakpoint');

		if(breakpoint)
		{
			var allowed = false;
			var breakpoints = breakpoint.split(',');

			for(var i = 0; i < breakpoints.length; i++)
			{
				var bp = breakpoints[i].trim();

				if
				(
					(bp === 'lg' && windowWidth >= 992) ||
					(bp === 'md' && windowWidth >= 576) ||
					(bp === 'sm' && windowWidth < 576)
				)
				{
					allowed = true;
					break;
				}
			}

			if(! allowed) return;
		}
		// ========================

		var elementTop = video.getBoundingClientRect().top + offsetTop;

		if(offsetTop + (windowHeight * 1.5) > elementTop)
		{
			source.attr('src', source.attr(lazyVideoAttr));
			video.load();
			video.play();

			video.dataset.loaded = true;
			source.removeAttr(lazyVideoAttr);
		}
	});
}


$(function()
{
	dataLazyVideoArray = $('[' + lazyVideoAttr + ']');

	if(dataLazyVideoArray.length)
	{
		setTimeout(function()
		{
			windowHeight = $(window).height();
			
			// First call
			offsetTop = $(window).scrollTop();
			processLazyVideoScroll();

			var scrollTimer;
			var lastScrollFireTime = 0;

			$(window).on('scroll resize', function()
			{
				var now = new Date().getTime();

				if(! scrollTimer)
				{
					if(now - lastScrollFireTime > (3 * minScrollTime))
					{
						processLazyVideoScroll(); // od razu przy pierwszym scrollu
						lastScrollFireTime = now;
					}

					scrollTimer = setTimeout(function()
					{
						scrollTimer = null;
						lastScrollFireTime = new Date().getTime();
						processLazyVideoScroll();
					}, minScrollTime);
				}
			});
		}, 200);
	}
});

var lazyBgAttr = 'data-lazy-bg';
var dataLazyBgArray = [];

function processLazyBgScroll()
{
	var offsetTop = $(window).scrollTop();

	$.each(dataLazyBgArray, function(index, element)
	{
		var tmpEl = $(element);

		if(tmpEl.css('background-image') !== 'none')
		{
			return; // Skip processed elements
		}

		if(offsetTop + (windowHeight * 1.5) > element.getBoundingClientRect().top)
		{
			tmpEl.css('background-image', 'url(' + tmpEl.attr(lazyBgAttr) + ')');
			tmpEl.removeAttr(lazyBgAttr);
		}
	});
}

$(function()
{
	dataLazyBgArray = $('[' + lazyBgAttr + ']');

	if(dataLazyBgArray.length)
	{
		setTimeout(function(e)
		{
			windowHeight = $(window).height();
			
			// First call
			offsetTop = $(window).scrollTop();

			processLazyBgScroll();

			var scrollTimer;
			var lastScrollFireTime = 0;

			$(window).on('scroll', function()
			{
				var now = new Date().getTime();

				if(! scrollTimer)
				{
					if(now - lastScrollFireTime > (3 * minScrollTime))
					{
						processLazyBgScroll(); // fire immediately on first scroll
						lastScrollFireTime = now;
					}

					scrollTimer = setTimeout(function()
					{
						scrollTimer = null;
						lastScrollFireTime = new Date().getTime();
						processLazyBgScroll();
					}, minScrollTime);
				}
			});
		}, 200);
	}
});

var lazyImgAttr = 'data-lazy-img';
var dataLazyImgArray = [];

function processLazyImgScroll()
{
	offsetTop = $(window).scrollTop();

	$.each(dataLazyImgArray, function(index, element)
	{
		var tmpEl = $(element);

		if(tmpEl.attr('src') && tmpEl.attr('src') !== '')
		{
			return; // Skip processed elements
		}

		if(offsetTop + (windowHeight * 1.5) > element.getBoundingClientRect().top)
		{
			tmpEl.attr('src', tmpEl.attr(lazyImgAttr));
			tmpEl.removeAttr(lazyImgAttr);
		}
	});
}

$(function()
{
	dataLazyImgArray = $('[' + lazyImgAttr + ']');

	if(dataLazyImgArray.length)
	{
		setTimeout(function(e)
		{
			windowHeight = $(window).height();

			// First call
			offsetTop = $(window).scrollTop();

			processLazyImgScroll();

			var scrollTimer;
			var lastScrollFireTime = 0;

			$(window).on('scroll', function()
			{
				var now = new Date().getTime();

				if(! scrollTimer)
				{
					if(now - lastScrollFireTime > (3 * minScrollTime))
					{
						processLazyImgScroll(); // fire immediately on first scroll
						lastScrollFireTime = now;
					}

					scrollTimer = setTimeout(function()
					{
						scrollTimer = null;
						lastScrollFireTime = new Date().getTime();

						processLazyImgScroll();

					}, minScrollTime);
				}
			});
		}, 200);
	}
});

////////////////////////////////////////
var blockOnChangeIntroSlider = false;

$(document).on('click', '.js-tabs-button', function(e)
{
	blockOnChangeIntroSlider = true;

	var $that = $(this);
	var $container = $that.closest('.js-tabs-container');
	
	var $buttons = $container.find('.js-tabs-button');
	var thatIndex = $buttons.index($that);
	var $tabs = $container.find('.js-tabs-item');
	var $previousActive = $buttons.filter('.active');
	var previousActiveIndex = $previousActive.index('.js-tabs-button');

	$buttons.removeClass('active');
	$that.addClass('active');

	$tabs.hide();
	var $allTabsVideos = $tabs.find('[data-lazy-video-ready]');
	$allTabsVideos.attr('data-lazy-video-ready', 'false');

	var $newCurrentTab = $tabs.eq(thatIndex);

	if($newCurrentTab.length)
	{
		$newCurrentTab.fadeIn();
	}

	var $introSlider = $container.find('.js-intro-slider');

	if($introSlider.length)
	{
		$introSlider.slick('slickGoTo', thatIndex);
	}

	var $currentTabVideos = $newCurrentTab.find('[data-lazy-video-ready]')
	$currentTabVideos.attr('data-lazy-video-ready', 'true');

	processLazyVideoScroll();

	blockOnChangeIntroSlider = false;
});

$(document).on('click', '.js-active-target-data', function(e)
{
	var $that = $(this);

	var targetSelector = $that.data('active-target-selector');
	var targetValue = String($that.data('active-target-value'));

	var $targets = $(targetSelector);

	if(! $targets.length)
	{
		return;
	}

	var targetAttribute = targetSelector.match(/\[data-([^\]=]+)(?:=[^\]]+)?\]/);

	if(!targetAttribute)
	{
		return;
	}

	targetAttribute = 'data-' + targetAttribute[1];

	$targets.each(function()
	{
		var $target = $(this);
		var value = String($target.attr(targetAttribute));

		$target.toggleClass('active', value === targetValue);
	});
});

function extractRGB(color)
{
	var match = color.match(/rgba?\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i);

	if (!match)
	{
		return null;
	}

	return {
		r: match[1],
		g: match[2],
		b: match[3]
	};
}

function replaceRGB(gradientStop, rgb)
{
	return gradientStop.replace(
		/rgba?\s*\(\s*\d+\s*,\s*\d+\s*,\s*\d+/i,
		'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b
	);
}

$(document).on('click', '.js-input-clear', function(e)
{
	var $that = $(this);
	var $container = $that.closest('.js-input-container');
	var $input = $container.find('input');

	$input.val('');

	$that.fadeOut();
});

$(document).on('input', '.js-input-container input', function(e)
{
	var $that = $(this);
	var $container = $that.closest('.js-input-container');

	var value = $that.val();

	if($container.length)
	{
		var $clearButton = $container.find('.js-input-clear');
		var $inputError = $container.find('.js-input-error');
		
		$that.removeClass('error');
		$inputError.hide();

		if($clearButton.length)
		{
			if(value.length)
			{
				$clearButton.fadeIn();
			}
			else
			{
				$clearButton.fadeOut();
			}
		}
	}
});

$(document).on('input', '.js-input-atleas-one input', function(e)
{
	var $that = $(this);
	var $inputsContainer = $that.closest('.js-input-atleas-one');

	if($inputsContainer.length)
	{
		var $inputs = $inputsContainer.find('input');
		var $inputsError = $inputsContainer.find('.js-input-error');

		$inputs.removeClass('error');
		$inputsError.hide();
	}
});

$(document).on('input', '.js-post-code', function()
{
	var value = $(this).val();

	// usuń wszystko oprócz cyfr i myślnika
	value = value.replace(/[^0-9-]/g, '');

	// jeżeli ktoś wpisał dokładnie "12-" — zostaw
	if(/^\d{2}-$/.test(value))
	{
		$(this).val(value);
		return;
	}

	// usuń wszystkie myślniki (poza przypadkiem wyżej)
	value = value.replace(/-/g, '');

	// max 5 cyfr
	value = value.substring(0, 5);

	// auto myślnik po 2 cyfrach
	if(value.length > 2)
	{
		value = value.substring(0, 2) + '-' + value.substring(2);
	}

	$(this).val(value);
});


$(document).on('click', '.js-stop-propagation', function(e)
{
	e.stopPropagation();
})

/////////////////////////////////////////
// AJAX submit button
function blockAjaxSubmitButton($button)
{
	var button = $button.get(0);

	if(! button || $button.data('ajax-submit-pending') === true) return false;

	$button.find('.ajax-submit-loader').remove();

	$button
		.data('ajax-submit-pending', true)
		.data('ajax-submit-was-disabled', button.disabled)
		.data('ajax-submit-opacity', button.style.opacity)
		.data('ajax-submit-pointer-events', button.style.pointerEvents)
		.prop('disabled', true)
		.attr('aria-busy', 'true')
		.css({
			opacity: 0.7,
			pointerEvents: 'none',
		})
		.append('<span class="ajax-submit-loader" aria-hidden="true"></span>');

	return true;
}

function unblockAjaxSubmitButton($button)
{
	var button = $button.get(0);

	if(! button) return;

	button.style.opacity = $button.data('ajax-submit-opacity') || '';
	button.style.pointerEvents = $button.data('ajax-submit-pointer-events') || '';

	$button
		.prop('disabled', $button.data('ajax-submit-was-disabled') === true)
		.removeAttr('aria-busy')
		.find('.ajax-submit-loader').remove().end()
		.removeData('ajax-submit-pending')
		.removeData('ajax-submit-was-disabled')
		.removeData('ajax-submit-opacity')
		.removeData('ajax-submit-pointer-events');
}


/////////////////////////////////////////
// Example AJAX form
$(document).on('click', '.js-submit-form', function(e)
{
	var $that = $(this);
	var $contact = $that.closest('form');

	if(! $contact.length) return;

	e.preventDefault();

	if($that.prop('disabled')) return;

	var params = {};
	var $inputs = $contact.find('input');

	var cslInputNames = [];
	var checkboxInputsNames = [];

	$inputs.each(function(index, input)
	{
		var $input = $(input);
		var $inputContainer = $input.closest('.js-input-container');
		var $inputError = $inputContainer.find('.js-input-error');
		var name = $input.attr('name');
		var value = $input.val();

		if(cslInputNames.includes(name))
		{
			value = $input.attr('data-value');
		}

		$input.removeClass('error');
		$inputError.hide();

		if(checkboxInputsNames.includes(name))
		{
			value = $input.is(':checked');
		}

		params[name] = value;
	});

	params['current-id'] = currentId;

	Object.assign(params, getUtmParams());

	if(! blockAjaxSubmitButton($that)) return;

	$.ajax(
	{
		type: "POST",
		url: ajaxUrl,
		data: {
			action: 'submit-form',
			params: JSON.stringify(params),
		},
		success: function(response)
		{
			if(response?.success)
			{
				$inputs.each(function(index, input)
				{
					var $input = $(input);
					var name = $input.attr('name');

					if(checkboxInputsNames.includes(name))
					{
						$input.prop('checked', false).trigger('change');
					}
					else if(cslInputNames.includes(name))
					{
						var defaultValue = $input.attr('data-default-value');
						$input.val(defaultValue);
						$input.attr('data-value', defaultValue);
					}
					else if($input.attr('type')
					&& $input.attr('type') != 'hidden')
					{
						$input.val('');
					}
				});
			}
			else
			{
				if(response?.missingFields)
				{
					for(const inputName of response.missingFields)
					{
						var $input = $contact.find('input[name="' + inputName + '"]');
						var $inputContainer = $input.closest('.js-input-container');
						var $inputError = $inputContainer.find('.js-input-error');

						$input.addClass('error');

						if($input.attr('type') == 'checkbox')
						{
							if(checkboxInputsNames.includes(inputName))
							{
								$inputError.html('Należy wybrać');
								$inputError.show();
							}
							else
							{
								$inputError.html('To pole jest wymagane');
							}
						}
						else
						{
							if(inputName == 'name')
							{
								$inputError.html('Imię jest wymagane');
							}
							else if(inputName == 'surname')
							{
								$inputError.html('Nazwisko jest wymagane');
							}
							else if(inputName == 'phone')
							{
								$inputError.html('Numer telefonu jest wymagany');
							}
							else if(inputName == 'email')
							{
								$inputError.html('Adres e-mail jest wymagany');
							}
							else
							{
								$inputError.html('To pole jest wymagane');
							}

							$inputError.show();
						}
					}
				}

				if(response?.wrongValueFields)
				{
					for(const inputName of response.wrongValueFields)
					{
						var $input = $contact.find('input[name="' + inputName + '"]');
						var $inputContainer = $input.closest('.js-input-container');
						var $inputError = $inputContainer.find('.js-input-error');

						$input.addClass('error');

						if($input.attr('type') == 'checkbox')
						{
							if(checkboxInputsNames.includes(inputName))
							{
								$inputError.html('Należy wybrać');
								$inputError.show();
							}
							else
							{
								$inputError.html('To pole jest wymagane');
							}
						}
						else
						{
							if(inputName == 'name')
							{
								$inputError.html('Wpisz poprawne Imię');
							}
							else if(inputName == 'surname')
							{
								$inputError.html('Wpisz poprawne Nazwisko');
							}
							else if(inputName == 'phone')
							{
								$inputError.html('Wpisz poprawny numer telefonu');
							}
							else if(inputName == 'email')
							{
								$inputError.html('Wpisz poprawny adres e-mail');
							}
							else
							{
								$inputError.html('Wpisz poprawne dane');
							}

							$inputError.show();
						}
					}
				}

				var $firstError = $inputs.filter('.error').first();

				if($firstError.length)
				{
					scrollToEl($firstError);
				}
			}

			var message = response?.message;
			var messageText = [message?.title, message?.text].filter(Boolean).join('\n');

			if(messageText)
			{
				window.alert(messageText);
			}
		},
		error: function(err)
		{
			console.log(err);
			alert('Błąd serwera!');
		},
		complete: function()
		{
			unblockAjaxSubmitButton($that);
		}
	});
});

/////////////////////////////////////

function bindScrollTranslateY($element, maxTranslateConfig, offsets)
{
	if(! $element.length) return false;

	const startScroll = $element.offset().top;

	$(window).on('scroll resize', function()
	{
		const width = window.innerWidth;

		// --- resolve maxTranslateRem ---
		let maxTranslateRem = null;

		if (maxTranslateConfig === false)
		{
			$element.css('transform', '');
			return;
		}

		if(maxTranslateConfig === null)
		{
			let lg = $element.attr('data-bind-to-rem-lg');
			let md = $element.attr('data-bind-to-rem-md');
			let sm = $element.attr('data-bind-to-rem-sm');

			lg = lg === undefined ? null : (lg === 'false' ? false : parseFloat(lg));
			md = md === undefined ? null : (md === 'false' ? false : parseFloat(md));
			sm = sm === undefined ? null : (sm === 'false' ? false : parseFloat(sm));

			// --- inheritance ---
			if(lg !== null)
			{
				if(md === null) md = lg;
				if(sm === null) sm = md;
			}

			// false blocks inheritance
			if(md === false) sm = false;

			maxTranslateConfig =
			{
				lg: lg,
				md: md,
				sm: sm
			};
		}

		if (typeof maxTranslateConfig === 'object')
		{
			if (width > 992)
			{
				maxTranslateRem = maxTranslateConfig.lg ?? maxTranslateConfig.md ?? maxTranslateConfig.sm ?? false;
			}
			else if (width > 576)
			{
				maxTranslateRem = maxTranslateConfig.md ?? maxTranslateConfig.lg ?? maxTranslateConfig.sm ?? false;
			}
			else
			{
				maxTranslateRem = maxTranslateConfig.sm ?? maxTranslateConfig.md ?? maxTranslateConfig.lg ?? false;
			}
		}
		else
		{
			maxTranslateRem = maxTranslateConfig;
		}

		if (maxTranslateRem === false)
		{
			$element.css('transform', '');
			return;
		}

		const maxTranslatePx = convertRemToPixels(maxTranslateRem);

		// --- resolve offsetRem ---
		let offsetRem = 0;

		if (typeof offsets === 'object')
		{
			if (width > 992)
			{
				offsetRem = offsets.lg ?? offsets.md ?? offsets.sm ?? 0;
			}
			else if (width > 576)
			{
				offsetRem = offsets.md ?? offsets.lg ?? offsets.sm ?? 0;
			}
			else
			{
				offsetRem = offsets.sm ?? offsets.md ?? offsets.lg ?? 0;
			}
		}
		else
		{
			offsetRem = offsets || 0;
		}

		// --- scroll logic ---
		const scrollTop = $(window).scrollTop() + convertRemToPixels(offsetRem);
		const deltaPx = Math.max(scrollTop - startScroll, 0);
		const clampedPx = Math.min(deltaPx, maxTranslatePx);
		const translateRem = pxToRem(clampedPx);

		$element.css(
			'transform',
			`translateY(${translateRem}rem)`
		);
	});
}

function bindScrollFixedTranslateY($element, maxTranslateConfig, offsets)
{
	if(!$element.length) return false;

	const startScroll = $element.offset().top - $(window).scrollTop();

	function updatePosition()
	{
		const width = window.innerWidth;

		// --- resolve maxTranslateRem ---
		let maxTranslateRem = null;

		if(maxTranslateConfig === false)
		{
			$element.css({
				position: '',
				top: '',
				transform: ''
			});
			return;
		}

		if(maxTranslateConfig === null)
		{
			let lg = $element.attr('data-bind-to-rem-lg');
			let md = $element.attr('data-bind-to-rem-md');
			let sm = $element.attr('data-bind-to-rem-sm');

			lg = lg === undefined ? null : (lg === 'false' ? false : parseFloat(lg));
			md = md === undefined ? null : (md === 'false' ? false : parseFloat(md));
			sm = sm === undefined ? null : (sm === 'false' ? false : parseFloat(sm));

			if(lg !== null)
			{
				if(md === null) md = lg;
				if(sm === null) sm = md;
			}

			if(md === false) sm = false;

			maxTranslateConfig =
			{
				lg: lg,
				md: md,
				sm: sm
			};
		}

		if(typeof maxTranslateConfig === 'object')
		{
			if(width > 992)
				maxTranslateRem = maxTranslateConfig.lg ?? maxTranslateConfig.md ?? maxTranslateConfig.sm ?? false;
			else if(width > 576)
				maxTranslateRem = maxTranslateConfig.md ?? maxTranslateConfig.lg ?? maxTranslateConfig.sm ?? false;
			else
				maxTranslateRem = maxTranslateConfig.sm ?? maxTranslateConfig.md ?? maxTranslateConfig.lg ?? false;
		}
		else
			maxTranslateRem = maxTranslateConfig;

		if(maxTranslateRem === false)
		{
			$element.css({
				position: '',
				top: '',
				transform: ''
			});
			return;
		}

		const maxTranslatePx = convertRemToPixels(maxTranslateRem);

		// --- resolve offsetRem ---
		let offsetRem = 0;

		if(typeof offsets === 'object')
		{
			if(width > 992)
				offsetRem = offsets.lg ?? offsets.md ?? offsets.sm ?? 0;
			else if(width > 576)
				offsetRem = offsets.md ?? offsets.lg ?? offsets.sm ?? 0;
			else
				offsetRem = offsets.sm ?? offsets.md ?? offsets.lg ?? 0;
		}
		else
			offsetRem = offsets || 0;

		const scrollTop = $(window).scrollTop() + convertRemToPixels(offsetRem);
		const deltaPx = Math.max(scrollTop - startScroll, 0);

		if(deltaPx <= 0)
		{
			$element.css({
				position: '',
				top: '',
				transform: ''
			});
		}
		else if(deltaPx < maxTranslatePx)
		{
			$element.css({
				position: 'fixed',
				top: `${offsetRem}rem`,
				transform: ''
			});
		}
		else
		{
			const translateRem = maxTranslateRem;

			$element.css({
				position: 'absolute',
				top: '0rem',
				transform: `translateY(${translateRem}rem)`
			});
		}
	}

	$(window).on('scroll resize', updatePosition);

	return updatePosition;
}

$(window).on('load', function()
{
	const updateConfBox = bindScrollFixedTranslateY(
		$('.conf__box__left'),
		null,
		{
			lg: 70,
		}
	);

	if(typeof updateConfBox == 'function')
	{
		updateConfBox();
	}
});

//////////////////

function isElementInViewport($element)
{
	if ($element.length === 0)
	{
		return false;
	}

	let elementTop = $element.offset().top;
	let elementBottom = elementTop + $element.outerHeight();

	let viewportTop = $(window).scrollTop();
	let viewportBottom = viewportTop + $(window).height();

	return elementBottom > viewportTop && elementTop < viewportBottom;
}

$(window).on('scroll', function ()
{
	let $trigger = $('.js-scroll-trigger-fadein');
	let $targetDesktop = $('.js-scroll-trigger-fadein-desktop');
	let $targetMobile = $('.js-scroll-trigger-fadein-mobile');

	if(isElementInViewport($trigger))
	{
		if(windowWidth > 992)
		{
			$targetDesktop.fadeOut('slow');
			$targetMobile.hide();
		}
		else
		{
			$targetDesktop.hide();
			$targetMobile.fadeOut('slow');
		}
	}
	else
	{
		if(windowWidth > 992)
		{
			$targetDesktop.fadeIn('fast');
			$targetMobile.hide();
		}
		else
		{
			$targetDesktop.hide();
			$targetMobile.fadeIn('fast');
		}
	}
});

$(window).on('resize', function ()
{
	let $targetDesktop = $('.js-scroll-trigger-fadein-desktop');
	let $targetMobile = $('.js-scroll-trigger-fadein-mobile');

	if(windowWidth > 992)
	{
		$targetDesktop.show();
		$targetMobile.hide();
	}
	else
	{
		$targetDesktop.hide();
		$targetMobile.show();
	}
});

function getUtmParams()
{
	const urlParams = new URLSearchParams(window.location.search);

	const utm = {};

	const keys = [
		'utm_source',
		'utm_medium',
		'utm_campaign',
		'utm_term',
		'utm_content'
	];

	keys.forEach(function(key)
	{
		const value = urlParams.get(key);

		if(value && value !== '')
		{
			utm[key] = value;
		}
	});

	return utm;
}

// $(function(e)
// {
// 	var params = {};

// 	Object.assign(params, getUtmParams());

// 	$.ajax(
// 	{
// 		type: "POST",
// 		url: ajaxUrl,
// 		data: {
// 			action: 'saveSessionUtm',
// 			params: JSON.stringify(params),
// 		},
// 		success: function(response)
// 		{
// 			// var message = response?.message ?? {};

// 			if(response?.success)
// 			{
// 				// if(response?.message)
// 				// {
// 				// 	showPopup({
// 				// 		'title': response.message?.title ?? '',
// 				// 		'text': response.message?.text ?? '',
// 				// 	});
// 				// }
// 			}
// 			else
// 			{
// 				if(response?.message)
// 				{
// 					showPopup({
// 						'title': response.message?.title ?? '',
// 						'text': response.message?.text ?? '',
// 					});
// 				}
// 			}
// 		},
// 		error: function(err)
// 		{
// 			console.log(err);
// 			alert('Błąd serwera!');
// 		}
// 	});
// });

const paramsGet = new URLSearchParams(window.location.search);
