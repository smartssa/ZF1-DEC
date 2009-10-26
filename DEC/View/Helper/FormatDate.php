<?php
/*
 *
 * @version     $Id: FormatDate.php 1508 2008-10-20 21:19:47Z dclarke $
 * @link
 * @since
 */
require_once "DEC/View/Helper/Helper.php";
class DEC_View_Helper_FormatDate extends DEC_View_Helper_Helper
{
    const MYSQL = 1;
    const UNIX  = 2;

    public function FormatDate ($timeStamp, $format = 'l, F jS, Y \a\t g:i a T', $type = self::MYSQL)
    {
        switch ($type) {
            case self::MYSQL:
                // convert mysql typestamp to sexydate
                $newDate = self::mysqlToEpoch($timeStamp);
                $sexyDate = date($format, $newDate);
                break;
            case self::UNIX:
                // convert unix timestamp to sexydate
                $sexyDate = date($format, $timeStamp);
                break;
            default:
                $sexyDate = $timeStamp;
                break;
        }
        return $sexyDate;
    }
    static function mysqlToEpoch ($mysqlDate)
    {
        list ($year, $month, $day, $hour, $minute, $second) = split("([^0-9])", $mysqlDate);
        return date("U", mktime((int) $hour, (int) $minute, (int) $second, (int) $month, (int) $day, (int) $year));
    }
}
