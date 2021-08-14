<?php

namespace App\ViewModel;

use App\Entity\User\User;

class Formatter
{
    private const SECOND = 1;
    private const MINUTE = self::SECOND * 60;
    private const HOUR = self::MINUTE * 60;
    private const DAY = self::HOUR * 24;
    private const WEEK = self::DAY * 7;
    private const MONTH = self::DAY * 30;
    private const YEAR = self::DAY * 365;

    /**
     * @param \DateTime|null $modificationTime
     * @param User|null $modificationUser
     * @return string|null
     */
    public static function formatModification(?\DateTimeInterface $modificationTime, ?User $modificationUser): ?string
    {
        if ($modificationTime === null || $modificationUser === null) {
            return null;
        }

        $now = new \DateTime();
        $interval = $now->getTimestamp() - $modificationTime->getTimestamp();

        $minutes = (int)($interval / self::MINUTE);
        $hours = (int)($interval / self::HOUR);
        $days = (int)($interval / self::DAY);
        $weeks = (int)($interval / self::WEEK);
        $months = (int)($interval / self::MONTH);
        $years = (int)($interval / self::YEAR);

        if ($interval < self::MINUTE) {
            $phrase = "<span class=\"text-success font-weight-bold\">just now</span><br>";
        } elseif ($interval < self::HOUR) {
            $phrase = "<span class=\"text-success font-weight-bold\">$minutes minute(s)</span><br> ago";
        } elseif ($interval < self::HOUR * 12) {
            $phrase = "<span class=\"text-success font-weight-bold\">$hours hour(s)</span><br> ago";
        } elseif ($interval < self::DAY) {
            $phrase = "<span class=\"text-warning font-weight-bold\">$hours hour(s)</span><br> ago";
        } elseif ($interval < self::DAY * 2) {
            $phrase = "<span class=\"text-warning font-weight-bold\">$days day(s)</span><br> ago";
        } elseif ($interval < self::WEEK) {
            $phrase = "<span class=\"font-weight-bold\">$days day(s)</span><br> ago";
        } elseif ($interval < self::MONTH) {
            $phrase = "<span class=\"font-weight-bold\">$weeks week(s)</span><br> ago";
        } elseif ($interval < self::YEAR) {
            $phrase = "<span class=\"font-weight-bold\">$months month(s)</span><br> ago";
        } else {
            $phrase = "<span class=\"font-weight-bold\">$years year(s)</span><br> ago";
        }

        $nickname = $modificationUser->getNickname();

        return $phrase . ' by ' .  $nickname;
    }

    /**
     * @param int|null $price
     * @return string|null
     */
    public static function formatPrice(?int $price): ?string
    {
        if ($price === null) {
            return null;
        }

        return number_format($price, 0, ',', ' ')/* . ' C'*/;
    }
}
