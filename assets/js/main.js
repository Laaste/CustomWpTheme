import $, { ajaxSettings } from 'jquery';
// import 'slick-carousel-latest';
// import '../vendor/bootstrap-datepicker-1.9.0/bootstrap-datepicker.min.js';
// import '../vendor/bootstrap-datepicker-1.9.0/bootstrap-datepicker.pl.min.js';
// import tippy from 'tippy.js';
// import 'sharer.js';
// import AOS from 'aos';

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

	if(!adminBar) return;

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

function toggleScrollState(osInstance){
	// get the current behavior
	var currentBehavior = osInstance.options().overflow;

	// set the new behavior & update
	osInstance.options({
		overflow: {
			y: currentBehavior.y == 'scroll' ? 'hidden' : 'scroll'
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
var osInstanceBody = OverlayScrollbars(document.body,
{
	overflow: {
		x: 'hidden'
	},
	scrollbars: {
		autoHide: 'scroll',
		autoHide: 'never',
		clickScroll: true
	},
});

function makeScrollbars()
{
	var elementsWithScrollbars = $('.js-scrollbar');

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

	adjustItemsHeightInColumns(
	{
		containerSelector: '.js-adjust-items-height',
		toAdjustSelectors: [
			'.js-adjust-items-height-title',
			'.js-adjust-items-height-text',
		],
		columnsInRowLg: 3,
		columnsInRowMd: false,
		columnsInRowSm: false,
	});
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
	menuOpen = !menuOpen;

	$(this).toggleClass('active');

	$('body').toggleClass('overflow-hidden');

	$('.js-header').fadeToggle();
	$('.js-header-links').toggleClass('active');

	$('.header').toggleClass('active');

	if(typeof osInstanceBody != 'undefined')
	{
		toggleScrollState(osInstanceBody);
	}
});

$(document).on('click', '.js-scroll', function (e){
	e.preventDefault();
	e.stopPropagation();

	var target = $(this).attr('href');
	var targetObj = $(target);

	if(targetObj.length){
		var offset = targetObj.offset().top;

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

/**
 * Scroll do elementu z opcjonalnym kontenerem i czasem animacji
 * @param {jQuery} targetObj - element do którego scrollujemy
 * @param {jQuery} [container=$('html, body')] - opcjonalny kontener scrolla
 * @param {number} [duration=500] - czas animacji w ms
 */
function scrollToEl(targetObj, container = $('html, body'), duration = 500, offset = 0)
{
	if(! targetObj?.length)
	{
		return;
	}

	let scrollOffset = targetObj.offset().top;

	container.animate(
	{
		scrollTop: (scrollOffset + offset)
	}, duration);

	var header = $('.js-header-fixed');

	if(header.length)
	{
		header.addClass('header--hidden');
	}
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
	var that = $(this);

	var valueToCopy = that.attr('data-to-copy');

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
	
	if(selectedValues.length === 0)
	{
		$input.html(defaultLabel);
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
		
		$input.html(labels.join(', '));
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

function addToBodyClassWithSuffix(baseClass, suffix)
{
	var body = $('body');

	body.addClass(baseClass + suffix);
}

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

// var $introModelSlider;
// var $introModelSliderNav;

// var sliders = [];

// function bootSlicks()
// {
// 	bootIntroModelSlider();

// 	sliders = [
// 		$introModelSlider,
// 		$introModelSliderNav,
// 	];
// }


// var $introModelSlider;
// var $introModelSliderNav;

// function bootIntroModelSlider()
// {
// 	var $sliders = $('.js-intro-model-slider');

// 	if($sliders.length)
// 	{
// 		$sliders.each(function()
// 		{
// 			var $slider = $(this);
// 			var $sliderContainer = $slider.closest('.js-intro-model-slider-container');
// 			var $nav = $sliderContainer.find('.js-intro-model-slider-nav');
// 			var $progressBar = $sliderContainer.find('.js-intro-model-slider-progress');
// 			var $itemLink = $sliderContainer.find('.js-intro-model-slider-item-link');

// 			if($nav.length)
// 			{
// 				$introModelSliderNav = $nav.slick(
// 				{
// 					arrows: false,
// 					dots: false,
// 					infinite: true,
// 					slidesToShow: 1,
// 					adaptiveHeight: true,
// 					autoplay: false,
// 					draggable: false,
// 					swipe: false,
// 					responsive: [
// 						{
// 							breakpoint: 576,
// 							settings:
// 							{
// 								adaptiveHeight: true
// 							}
// 						}
// 					]
// 				});
// 			}

// 			$introModelSlider = $slider.slick(
// 			{
// 				arrows: false,
// 				dots: false,
// 				infinite: true,
// 				slidesToShow: 1,
// 				adaptiveHeight: false,
// 				autoplay: true,
// 				autoplaySpeed: 5000,
// 				lazyLoad: 'anticipated'
// 			});

// 			// przyciski powiązane z danym sliderContainer
// 			$sliderContainer.find('.js-intro-model-slider-prev').on('click', function()
// 			{
// 				$introModelSlider.slick('slickPrev');
// 			});

// 			$sliderContainer.find('.js-intro-model-slider-next').on('click', function()
// 			{
// 				$introModelSlider.slick('slickNext');
// 			});

// 			var slidesCount = $slider.attr('data-slides-count');

// 			function updateItemLink(index)
// 			{
// 				if(! $itemLink.length) return;

// 				var $current = $slider.find('.slick-slide[data-slick-index="' + index + '"]');
// 				var link = $current.find('.js-intro-model-slider-item').attr('data-slide-link');

// 				if(link)
// 				{
// 					$itemLink.removeClass('invisible');
// 				}
// 				else
// 				{
// 					$itemLink.addClass('invisible');
// 				}

// 				$itemLink.attr('href', link);
// 			}

// 			function updateProgressBar(index)
// 			{
// 				if(!$progressBar.length) return;

// 				var progress = (index == 0 && slidesCount == 1)
// 					? 100
// 					: (index / (slidesCount - 1)) * 100;

// 				$progressBar.css('width', progress + '%');
// 			}

// 			updateItemLink(0);
// 			updateProgressBar(0);

// 			$introModelSlider.on('afterChange', function(event, slick, index)
// 			{
// 				if($introModelSliderNav)
// 				{
// 					$introModelSliderNav.slick('slickGoTo', index);
// 				}

// 				updateItemLink(index);
// 				updateProgressBar(index);
// 			});
// 		});
// 	}
// }

// function refreshSlicks()
// {
// 	sliders.forEach(function($sliderType)
// 	{
// 		if($sliderType.length)
// 		{
// 			$sliderType.each(function(sliderEl)
// 			{
// 				var $slider = $(sliderEl);

// 				if($slider.length
// 				&& $slider.hasClass('slick-initialized'))
// 				{
// 					$slider.slick('refresh');
// 				}
// 			});
// 		}
// 	});
// }

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


// var tippySettings = {
// 	allowHTML: true,
// 	interactive: true,
// 	placement: 'bottom-start',
// 	// showOnCreate: true,
// 	// trigger: 'manual',
// 	arrow: false,
// 	trigger: 'mouseenter focus',
// 	onCreate(instance) {
// 		// instance.show();
// 		if(!instance.popper.querySelector('.js-tippy-close')){
// 			if(instance.popper.querySelector('.footer__top__newsletter__popup')){
// 				var close = $('<span>')
// 					.addClass('close js-tippy-close')
// 					.html('&times;')[0];
	
// 				instance.popper.querySelector('.footer__top__newsletter__popup').appendChild(close);
// 			} else {
// 				instance.setContent(this.content + '<span class="close js-tippy-close">&times;</span>');
// 			}
// 		}

// 		instance.popper.querySelector('.js-tippy-close').addEventListener('click', () => {
// 			instance.hide();
// 		});
// 	},
// 	onShow(instance){
// 		setTimeout(() => {
// 			Sharer.init();

// 			$('.js-copy-to-clipboard').on('click', function(e)
// 			{
// 				var that = $(this);

// 				var valueToCopy = that.attr('data-to-copy');

// 				if(valueToCopy)
// 				{
// 					copyTextToClipboard(valueToCopy);
// 				}
// 			});
// 		}, 500)
// 	},
// 	// onTrigger(instance){

// 	// 	window.Sharer.init();
// 	// }
// };

// tippy('[data-tippy-content]', tippySettings);

// $(function()
// {
// 	AOS.init(
// 	{
// 		duration: 1500,
// 		once: true,
// 	});
// });

////////////////////////////////////////
// Lazyloading

var lazyBgAttr = 'data-lazy-bg';
var offsetTop = $(window).scrollTop();
var minScrollTime = 100;

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