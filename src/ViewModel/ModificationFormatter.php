<?php

namespace App\ViewModel;

use App\Entity\User\User;

class ModificationFormatter
{
    const SECOND = 1;
    const MINUTE = self::SECOND * 60;
    const HOUR = self::MINUTE * 60;
    const DAY = self::HOUR * 24;
    const WEEK = self::DAY * 7;
    const MONTH = self::DAY * 30;
    const YEAR = self::DAY * 365;

    /**
     * @param \DateTime $modificationTime
     * @param User $modificationUser
     * @return string
     */
    public static function getModifiedHtml(\DateTimeInterface $modificationTime, User $modificationUser): string
    {
        $now = new \DateTime();
        $interval = $now->getTimestamp() - $modificationTime->getTimestamp();

        $minutes = (int)($interval / self::MINUTE);
        $hours = (int)($interval / self::HOUR);
        $days = (int)($interval / self::DAY);
        $weeks = (int)($interval / self::WEEK);
        $months = (int)($interval / self::MONTH);
        $years = (int)($interval / self::YEAR);

        if ($interval < self::MINUTE) {
            $phrase = "<span class=\"text-success font-weight-bold\">just now</span>";
        } elseif ($interval < self::HOUR) {
            $phrase = "<span class=\"text-success font-weight-bold\">$minutes minute(s)</span> ago";
        } elseif ($interval < self::HOUR * 12) {
            $phrase = "<span class=\"text-success font-weight-bold\">$hours hour(s)</span> ago";
        } elseif ($interval < self::DAY) {
            $phrase = "<span class=\"text-warning font-weight-bold\">$hours hour(s)</span> ago";
        } elseif ($interval < self::DAY * 2) {
            $phrase = "<span class=\"text-warning font-weight-bold\">$days day(s)</span> ago";
        } elseif ($interval < self::WEEK) {
            $phrase = "<span class=\"font-weight-bold\">$days day(s)</span> ago";
        } elseif ($interval < self::MONTH) {
            $phrase = "<span class=\"font-weight-bold\">$weeks week(s)</span> ago";
        } elseif ($interval < self::YEAR) {
            $phrase = "<span class=\"font-weight-bold\">$months month(s)</span> ago";
        } else {
            $phrase = "<span class=\"font-weight-bold\">$years year(s)</span> ago";
        }

        $nickname = $modificationUser->getNickname();

        return "$phrase<br>by $nickname";
    }
}
