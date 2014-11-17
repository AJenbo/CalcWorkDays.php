<?php
/**
 * Contains the class calcWorkDays
 *
 * PHP version 5.3
 *
 * @category Invoices
 * @package  CalcWorkDays
 * @author   Anders Jenbo <anders@jenbo.dk>
 * @license  LGPL http://www.gnu.org/licenses/lgpl.html
 * @link     http://anders.jenbo.dk/
 */

/**
 * Class for calculating work dayes
 *
 * This class works with Unix timestamps and as such is limited to years
 * beetwean 1970 and 2037
 *
 * @category Invoices
 * @package  CalcWorkDays
 * @author   Anders Jenbo <anders@jenbo.dk>
 * @license  LGPL http://www.gnu.org/licenses/lgpl.html
 * @link     http://anders.jenbo.dk/
 */
class CalcWorkDays
{
    // Country code from ISO 3166-2 to generate holidays for
    static public $country = 'DK';

    const ONEDAY = 86400;

    // Cache calculated holidaies
    static private $_holidays = [];

    // Keep track of cache
    static private $_cachedYears = [];

    /**
     * Add workdays to a time
     *
     * @param int $days Number of workdays to add/substract
     * @param int $time The timestamp which is used as a base for the calculation
     *
     * @return int New timestamp
     */
    static public function addWorkDays($days, $time = null)
    {
        $time = is_null($time) ? time() : $time;
        $days = (int) $days;
        $_direction = $days == 0 ? 0 : $days / abs($days);
        $_days = 0;

        while ($_days !== $days) {
            $time += $_direction * self::ONEDAY;
            if (self::isWorkDay($time)) {
                $_days += $_direction;
            }
        }

        return $time;
    }

    /**
     * Calculate the number of workdays between two dates
     *
     * @param int $start Starting timestamp for the calculation
     * @param int $end   Ending timestamp for the calculation
     *
     * @return int Number of workdays
     */
    static public function workDaysBetween($start, $end)
    {
        $start = (int) $start;
        $end = (int) $end;
        $days = 0;

        if ($start > $end) {
            $tmp = $start;
            $start = $end;
            $end = $tmp;
        }

        while ($start <= $end) {
            if (self::isWorkDay($start)) {
                $days++;
            }
            $start += self::ONEDAY;
        }

        return $days;
    }

    /**
     * Test if a given time is on a workday
     *
     * @param int $time The timestamp to test
     *
     * @return bool True if timestamp is in a workday
     */
    static public function isWorkDay($time = null)
    {
        $time = is_null($time) ? time() : $time;

        $dow = date('w', $time);
        if ($dow == 0 || $dow == 6) {
            return false;
        }

        self::_cacheHolidays(date('Y', $time));
        if (isset(self::$_holidays[self::$country][date('Y-m-d', $time)])) {
            return false;
        }

        return true;
    }

    /**
     * Generate cache of all holidays in a given year
     *
     * @param int $year Year to generate cache for
     *
     * @return null
     */
    static private function _cacheHolidays($year)
    {
        if (isset(self::$_cachedYears[self::$country][$year])) {
            return;
        }

        switch (self::$country) {
            case 'DE':
                // Holidays with a fixed date
                self::$_holidays[self::$country][$year . '-01-01'] = true; // New Year's Day
                self::$_holidays[self::$country][$year . '-05-01'] = true; // May Day
                self::$_holidays[self::$country][$year . '-10-03'] = true; // Day of German Unity
                self::$_holidays[self::$country][$year . '-12-25'] = true; // Christmas Day
                self::$_holidays[self::$country][$year . '-12-26'] = true; // Boxing Day

                // Holidays that depends on easter
                $easter = easter_date($year);

                self::$_holidays[self::$country][date('Y-m-d', $easter - self::ONEDAY * 2)] = true; // Good Friday
                self::$_holidays[self::$country][date('Y-m-d', $easter + self::ONEDAY)] = true; // Easter Monday
                self::$_holidays[self::$country][date('Y-m-d', $easter + self::ONEDAY * 39)] = true; // Ascension Day
                self::$_holidays[self::$country][date('Y-m-d', $easter + self::ONEDAY * 50)] = true; // Whit Monday
                break;
            case 'DK':
                // Holidays with a fixed date
                self::$_holidays[self::$country][$year . '-01-01'] = true; // Nytårsdag
                self::$_holidays[self::$country][$year . '-06-05'] = true; // Grundlovsdag
                self::$_holidays[self::$country][$year . '-12-24'] = true; // Juleaften
                self::$_holidays[self::$country][$year . '-12-25'] = true; // Juledag
                self::$_holidays[self::$country][$year . '-12-26'] = true; // 2. Juledag

                // Holidays that depends on easter
                $easter = easter_date($year);

                self::$_holidays[self::$country][date('Y-m-d', $easter - self::ONEDAY * 3)] = true; // Skærtorsdag
                self::$_holidays[self::$country][date('Y-m-d', $easter - self::ONEDAY * 2)] = true; // Langfredag
                self::$_holidays[self::$country][date('Y-m-d', $easter)] = true; // Påskedag
                self::$_holidays[self::$country][date('Y-m-d', $easter + self::ONEDAY)] = true; // 2. Påskedag
                self::$_holidays[self::$country][date('Y-m-d', $easter + self::ONEDAY * 26)] = true; // Store Bededag
                self::$_holidays[self::$country][date('Y-m-d', $easter + self::ONEDAY * 39)] = true; // Kr. Himmelfart
                self::$_holidays[self::$country][date('Y-m-d', $easter + self::ONEDAY * 49)] = true; // Pinsedag
                self::$_holidays[self::$country][date('Y-m-d', $easter + self::ONEDAY * 50)] = true; // 2. Pinsedag
                break;
            case 'SE':
                // Holidays with a fixed date
                self::$_holidays[self::$country][$year . '-01-01'] = true; // New Year's Day
                self::$_holidays[self::$country][$year . '-01-06'] = true; // Epiphany
                self::$_holidays[self::$country][$year . '-05-01'] = true; // May Day
                self::$_holidays[self::$country][$year . '-06-06'] = true; // National day
                self::$_holidays[self::$country][$year . '-12-25'] = true; // Christmas Day
                self::$_holidays[self::$country][$year . '-12-26'] = true; // Boxing Day

                // Midsummer day
                $offset = strtotime($year . '-06-20');
                $midsummerDay = $offset + self::ONEDAY * (6 - date('w', $offset));
                self::$_holidays[self::$country][date('Y-m-d', $midsummerDay)] = true;

                // All Saints' Day
                $offset = strtotime($year . '-10-31');
                $allSaintsDay = $offset + self::ONEDAY * (6 - date('w', $offset));
                self::$_holidays[self::$country][date('Y-m-d', $allSaintsDay)] = true;

                // Holidays that depends on easter
                $easter = easter_date($year);

                self::$_holidays[self::$country][date('Y-m-d', $easter - self::ONEDAY * 2)] = true; // Good Friday
                self::$_holidays[self::$country][date('Y-m-d', $easter)] = true; // Easter
                self::$_holidays[self::$country][date('Y-m-d', $easter + self::ONEDAY)] = true; // Easter Monday
                self::$_holidays[self::$country][date('Y-m-d', $easter + self::ONEDAY * 39)] = true; // Ascension Day
                self::$_holidays[self::$country][date('Y-m-d', $easter + self::ONEDAY * 49)] = true; // Whit Monday
                break;
            default:
                throw new Exception('Unsupported language');
                break;
        }

        self::$_cachedYears[self::$country][$year] = true;
    }
}
