<?php
function dateDefaultLang()
{
	$supportedLangs = dateSupportedLangs();

	return reset($supportedLangs);
}

function dateSupportedLangs()
{
	return [
		'pl',
		'en',
	];
}

function dateGetLang($args)
{
	if(isset($args['lang'])
	&& ! empty($args['lang'])
	&& in_array($args['lang'], dateSupportedLangs()))
	{
		return $args['lang'];
	}
	else
	{
		return dateDefaultLang();
	}
}

function getMonthName($monthNumber, $locale = 'pl', $monthTextBaseForm = false)
{
	$monthNumber = intval($monthNumber);

	$months = [
		'en' => [
			1 => ['january', 'of january'],
			2 => ['february', 'of february'],
			3 => ['march', 'of march'],
			4 => ['april', 'of april'],
			5 => ['may', 'of may'],
			6 => ['june', 'of june'],
			7 => ['july', 'of july'],
			8 => ['august', 'of august'],
			9 => ['september', 'of september'],
			10 => ['october', 'of october'],
			11 => ['november', 'of november'],
			12 => ['december', 'of december']
		],
		'pl' => [
			1 => ['styczeń', 'stycznia'],
			2 => ['luty', 'lutego'],
			3 => ['marzec', 'marca'],
			4 => ['kwiecień', 'kwietnia'],
			5 => ['maj', 'maja'],
			6 => ['czerwiec', 'czerwca'],
			7 => ['lipiec', 'lipca'],
			8 => ['sierpień', 'sierpnia'],
			9 => ['wrzesień', 'września'],
			10 => ['październik', 'października'],
			11 => ['listopad', 'listopada'],
			12 => ['grudzień', 'grudnia']
		]
	];

	$monthNames = $months[$locale][$monthNumber];

	if($monthTextBaseForm)
	{
		return $monthNames[0]; // base full form
	}
	else
	{
		return $monthNames[1]; // form "something of month"
	}
}

function getDayName($dayNumber, $locale = 'pl')
{
	$dayNumber = intval($dayNumber);

	$days =
	[
		'en' =>
		[
			0 => 'sunday',
			1 => 'monday',
			2 => 'tuesday',
			3 => 'wednesday',
			4 => 'thursday',
			5 => 'friday',
			6 => 'saturday',
			7 => 'sunday',
		],
		'pl' =>
		[
			0 => 'niedziela',
			1 => 'poniedziałek',
			2 => 'wtorek',
			3 => 'środa',
			4 => 'czwartek',
			5 => 'piątek',
			6 => 'sobota',
			7 => 'niedziela',
		]
	];

	return $days[$locale][$dayNumber];
}

/**
 * @param array $args
 * $args['pre_start'] - date interwal start to merget with date
 * $args['pre_end'] - date interwal end to merget with date
 * $args['date_start'] - start of main date
 * $args['date_end'] - end of main date
 * $args['input_format'] - date format to convert
 * $args['day_as_text'] - change in date day to word
 * $args['month_as_text'] - change in date month to word
 * $args['append_start_hour'] - append to start date it happen at hour
 * $args['append_end_hour'] - append to end date it happen at hour
 */
function dateIntervalText($args = [])
{
	$dates = [];

	$inputFormat = 'd-m-Y H:i:s';

	if(isset($args['input_format']))
	{
		$inputFormat = $args['input_format'];
	}

	if(isset($args['pre_start'])
	&& ! empty($args['pre_start']))
	{
		$exhibitionStart = $args['pre_start'];
		$exhibitionStart = DateTime::createFromFormat($inputFormat, $exhibitionStart);
		$dates['pre_start'] = $exhibitionStart;
	}

	if(isset($args['pre_end'])
	&& ! empty($args['pre_end']))
	{
		$exhibitionEnd = $args['pre_end'];
		$exhibitionEnd = DateTime::createFromFormat($inputFormat, $exhibitionEnd);
		$dates['pre_end'] = $exhibitionEnd;
	}

	if(isset($args['date_start'])
	&& ! empty($args['date_start']))
	{
		$dateStart = $args['date_start'];
		$dateStart = DateTime::createFromFormat($inputFormat, $dateStart);
		$dates['date_start'] = $dateStart;
	}

	if(isset($args['date_end'])
	&& ! empty($args['date_end']))
	{
		$dateEnd = $args['date_end'];
		$dateEnd = DateTime::createFromFormat($inputFormat, $dateEnd);
		$dates['date_end'] = $dateEnd;
	}

	$dayAsText = false;
	$monthAsText = false;
	$appendStartAtHour = false;
	$appendEndAtHour = false;

	$lang = dateGetLang($args);

	if(isset($args['day_as_text']))
	{
		$dayAsText = $args['day_as_text'];
	}

	if(isset($args['month_as_text']))
	{
		$monthAsText = $args['month_as_text'];
	}

	if(isset($args['append_start_hour']))
	{
		$appendStartAtHour = $args['append_start_hour'];
	}

	if(isset($args['append_end_hour']))
	{
		$appendEndAtHour = $args['append_end_hour'];
	}

	$dateExcerpt = '';

	if(count($dates))
	{
		$start = min($dates);
		$end = max($dates);

		$sameDayMonthYear = ($start->format('d-m-Y') == $end->format('d-m-Y'));
		$sameMonthAndYear = ($start->format('m-Y') == $end->format('m-Y'));
		$sameYear = ($start->format('Y') == $end->format('Y'));

		$startDay = intval($start->format('d'));
		$startDayOfWeek = intval($start->format('w'));
		$endDay = intval($end->format('d'));
		$endDayOfWeek = intval($end->format('w'));

		$startMonth = $start->format('m');
		$endMonth = $end->format('m');

		$startYear = $start->format('Y');
		$endYear = $end->format('Y');

		$startHour = $start->format('H:i');
		$endHour = $end->format('H:i');

		if($sameDayMonthYear)
		{
			//add start day
			if($dayAsText)
			{
				$dateExcerpt .= getDayName($startDayOfWeek, $lang);
			}
			else
			{
				$dateExcerpt .= $startDay;
			}

			$dateExcerpt .= ' ';

			// add start month
			if($monthAsText)
			{
				$dateExcerpt .= getMonthName($startMonth, $lang, true);
			}
			else
			{
				$dateExcerpt .= $startMonth;
			}

			$dateExcerpt .= ' ';

			// add start year
			$dateExcerpt .= $startYear;

			if($appendEndAtHour)
			{
				$dateExcerpt .= ', ' . getAtHour($lang) . ' ' . $endHour;
			}
			else if($appendStartAtHour)
			{
				$dateExcerpt .= ', ' . getAtHour($lang) . ' ' . $startHour;
			}
		}
		else if($sameMonthAndYear)
		{
			//add start day
			if($dayAsText)
			{
				$dateExcerpt .= getDayName($startDayOfWeek, $lang);
			}
			else
			{
				$dateExcerpt .= $startDay;
			}

			if($appendStartAtHour)
			{
				$dateExcerpt .= ', ' . getAtHour($lang) . ' ' . $startHour;
			}

			$dateExcerpt .= ' - ';

			//add end day
			if($dayAsText)
			{
				$dateExcerpt .= getDayName($endDayOfWeek, $lang);
			}
			else
			{
				$dateExcerpt .= $endDay;
			}

			$dateExcerpt .= ' ';

			// add start month
			if($monthAsText)
			{
				$dateExcerpt .= getMonthName($startMonth, $lang, true);
			}
			else
			{
				$dateExcerpt .= $startMonth;
			}

			$dateExcerpt .= ' ';

			// add start year
			$dateExcerpt .= $startYear;

			if($appendEndAtHour)
			{
				$dateExcerpt .= ', ' . getAtHour($lang) . ' ' . $endHour;
			}
		}
		else if($sameYear)
		{
			//add start day
			if($dayAsText)
			{
				$dateExcerpt .= getDayName($startDayOfWeek, $lang);
			}
			else
			{
				$dateExcerpt .= $startDay;
			}

			$dateExcerpt .= ' ';

			// add start month
			if($monthAsText)
			{
				$dateExcerpt .= getMonthName($startMonth, $lang, true);
			}
			else
			{
				$dateExcerpt .= $startMonth;
			}

			if($appendStartAtHour)
			{
				$dateExcerpt .= ', ' . getAtHour($lang) . ' ' . $startHour;
			}

			$dateExcerpt .= ' - ';

			//add end day
			if($dayAsText)
			{
				$dateExcerpt .= getDayName($endDayOfWeek, $lang);
			}
			else
			{
				$dateExcerpt .= $endDay;
			}

			$dateExcerpt .= ' ';

			// add end month
			if($monthAsText)
			{
				$dateExcerpt .= getMonthName($endMonth, $lang, true);
			}
			else
			{
				$dateExcerpt .= $endMonth;
			}

			$dateExcerpt .= ' ';

			// add end year
			$dateExcerpt .= $endYear;

			if($appendEndAtHour)
			{
				$dateExcerpt .= ', ' . getAtHour($lang) . ' ' . $endHour;
			}
		}
		else
		{
			//add start day
			if($dayAsText)
			{
				$dateExcerpt .= getDayName($startDayOfWeek, $lang);
			}
			else
			{
				$dateExcerpt .= $startDay;
			}

			$dateExcerpt .= ' ';

			// add start month
			if($monthAsText)
			{
				$dateExcerpt .= getMonthName($startMonth, $lang, true);
			}
			else
			{
				$dateExcerpt .= $startMonth;
			}

			$dateExcerpt .= ' ';

			// add start year
			$dateExcerpt .= $startYear;

			if($appendStartAtHour)
			{
				$dateExcerpt .= ', ' . getAtHour($lang) . ' ' . $startHour;
			}

			$dateExcerpt .= ' - ';

			//add end day
			if($dayAsText)
			{
				$dateExcerpt .= getDayName($endDayOfWeek, $lang);
			}
			else
			{
				$dateExcerpt .= $endDay;
			}

			$dateExcerpt .= ' ';

			// add end month
			if($monthAsText)
			{
				$dateExcerpt .= getMonthName($endMonth, $lang, true);
			}
			else
			{
				$dateExcerpt .= $endMonth;
			}

			$dateExcerpt .= ' ';

			// add end year
			$dateExcerpt .= $endYear;

			if($appendEndAtHour)
			{
				$dateExcerpt .= ', ' . getAtHour($lang) . ' ' . $endHour;
			}
		}
	}

	return $dateExcerpt;
}

/**
 * @param string $date - date to convert
 * @param array $args - configuration
 * 
 * $args['input_format'] - $date format to convert
 * $args['prepend_day_of_week'] - add name of day in week before date
 * $args['day_as_text'] - change in date day to word
 * $args['month_as_text'] - change in date month to word
 * $args['append_at_hour'] - add 'at hour / o godz.' with hour
 * $args['output_template'] - final format to use if specified
 * $args['time_format'] - '12' or '24' for time format
 */
function dateText($date = '', $args = [])
{
	$inputFormat = 'Y-m-d H:i:s';

	$prependDayOfWeek = false;
	$dayAsText = false;
	$monthAsText = false;
	$appendAtHour = false;
	$monthTextBaseForm = false;
	$outputTemplate = null;
	$timeFormat = 24;

	if(isset($args['input_format']))
	{
		$inputFormat = $args['input_format'];
	}

	if(isset($args['prepend_day_of_week']))
	{
		$prependDayOfWeek = $args['prepend_day_of_week'];
	}

	if(isset($args['day_as_text']))
	{
		$dayAsText = $args['day_as_text'];
	}

	if(isset($args['month_as_text']))
	{
		$monthAsText = $args['month_as_text'];
	}

	if(isset($args['month_text_base_form']))
	{
		$monthTextBaseForm = $args['month_text_base_form'];
	}

	if(isset($args['append_at_hour']))
	{
		$appendAtHour = $args['append_at_hour'];
	}

	if(isset($args['output_template']))
	{
		$outputTemplate = $args['output_template'];
	}

	if(isset($args['time_format'])
	&& in_array($args['time_format'], ['12', '24']))
	{
		$timeFormat = $args['time_format'];
	}

	$date = DateTime::createFromFormat($inputFormat, $date);

	if(! $date)
	{
		return '';
	}

	$lang = dateGetLang($args);

	if($outputTemplate)
	{
		$outputTemplate = str_replace(
			['{day}', '{month}', '{year}', '{hour}', '{day_of_week}', '{at_hour}'],
			[
				($dayAsText) ? getDayAsWord(intval($date->format('d')), $lang) : $date->format('d'),

				($monthAsText) ? getMonthName(intval($date->format('m')), $lang, $monthTextBaseForm) : $date->format('m'),

				$date->format('Y'),

				($timeFormat == '12') ? $date->format('h:i A') : $date->format('H:i'), // 12 vs 24

				($prependDayOfWeek) ? getDayName(intval($date->format('w')), $lang) : '',

				($appendAtHour) ? getAtHour($lang) . ' ' . $date->format('H:i') : '',
			],
			$outputTemplate
		);

		return trim(preg_replace('/\s+/', ' ', $outputTemplate)); // clean extra spaces
	}

	$dateDay = intval($date->format('d'));
	$dateDayOfWeek = intval($date->format('w'));
	$dateMonth = intval($date->format('m'));
	$dateYear = $date->format('Y');
	$dateHour = $timeFormat === '12' ? $date->format('h:i A') : $date->format('H:i'); // 12-hour vs 24-hour


	$dateText = '';

	if($prependDayOfWeek)
	{
		$dateText .= getDayName($dateDayOfWeek, $lang) . ', ';
	}

	if($dayAsText)
	{
		$dateText .= getDayAsWord($dateDay, $lang);
	}
	else
	{
		$dateText .= $dateDay;
	}

	$dateText .= ' ';

	if($monthAsText)
	{
		$dateText .= getMonthName($dateMonth, $lang, $monthTextBaseForm);
	}
	else
	{
		$dateText .= str_pad($dateMonth, 2, '0', STR_PAD_LEFT);
	}

	$dateText .= ' ';

	$dateText .= $dateYear;

	if($appendAtHour)
	{
		$dateText .= ', ' . getAtHour($lang) . ' ' . $dateHour;
	}

	return $dateText;
}

function getDayAsWord($day, $lang)
{
	$dayWords = [
		'en' => ['first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth', 'ninth', 'tenth', 'eleventh', 'twelfth', 'thirteenth', 'fourteenth', 'fifteenth', 'sixteenth', 'seventeenth', 'eighteenth', 'nineteenth', 'twentieth', 'twenty-first', 'twenty-second', 'twenty-third', 'twenty-fourth', 'twenty-fifth', 'twenty-sixth', 'twenty-seventh', 'twenty-eighth', 'twenty-ninth', 'thirtieth', 'thirty-first'],
		'pl' => ['pierwszy', 'drugi', 'trzeci', 'czwarty', 'piąty', 'szósty', 'siódmy', 'ósmy', 'dziewiąty', 'dziesiąty', 'jedenasty', 'dwunasty', 'trzynasty', 'czternasty', 'piętnasty', 'szesnasty', 'siedemnasty', 'osiemnasty', 'dziewiętnasty', 'dwudziesty', 'dwudziesty pierwszy', 'dwudziesty drugi', 'dwudziesty trzeci', 'dwudziesty czwarty', 'dwudziesty piąty', 'dwudziesty szósty', 'dwudziesty siódmy', 'dwudziesty ósmy', 'dwudziesty dziewiąty', 'trzydziesty', 'trzydziesty pierwszy'],
	];

	return $dayWords[$lang][$day - 1] ?? $day;
}

function getAtHour($lang)
{
	$atHourText = [
		'pl' => 'o godz.',
		'en' => 'at hour',
	];

	$result = '';

	foreach($atHourText as $textLang => $text)
	{
		if($textLang == $lang)
		{
			$result = $text;
			break;
		}
	}

	if(empty($result))
	{
		$result = reset($atHourText);
	}

	return $result;
}