<?php

namespace App\ViewModel;

use App\Entity\User\User;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

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
     * @var Package|null
     */
    private static $package;

    /**
     * @param \DateTime|null $modificationTime
     * @param User|null $modificationUser
     * @return string|null
     */
    public static function formatModification(?\DateTimeInterface $modificationTime, ?User $modificationUser): ?string
    {
        if ($modificationTime === null) {
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

        if ($modificationUser !== null) {
            $nickname = $modificationUser->getNickname();

            return $phrase . " by $nickname";
        } else {
            return $phrase;
        }
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

        $text = number_format($price, 0, null, ' ');

        return self::addIconToText($text, '/credit.svg');
    }

    /**
     * @param float|null $qty
     * @return string|null
     */
    public static function formatQty(?float $qty): ?string
    {
        if ($qty === null) {
            return null;
        }

        $roundedQty = round($qty, 1);

        if (abs(round($roundedQty) - $roundedQty) > 0) {
            return number_format($roundedQty, 1, '.', ' ');
        } else {
            return number_format($roundedQty, 0, null, ' ');
        }
    }

    /**
     * @param float|null $percent
     * @return string|null
     */
    public static function formatPercent(?float $percent): ?string
    {
        if ($percent === null) {
            return null;
        }

        return round($percent * 100) . '%';
    }

    /**
     * @param string|null $iconUrl
     * @return string|null
     */
    public static function getIcon(?string $iconUrl): ?string
    {
        if ($iconUrl === null) {
            return null;
        }

        $preparedIconUrl = self::getPackage()->getUrl($iconUrl);

        return "<img src=\"$preparedIconUrl\" style=\"height: 16px; margin-bottom: 2px\"/>";
    }

    /**
     * @param string|null $text
     * @param string|null $iconUrl
     * @return string|null
     */
    public static function addIconToText(?string $text, ?string $iconUrl): ?string
    {
        if ($text === null) {
            return null;
        }

        $icon = self::getIcon($iconUrl);

        return "$text $icon";
    }

    /**
     * @return Package
     */
    private static function getPackage(): Package
    {
        if (self::$package === null) {
            self::$package = new Package(new EmptyVersionStrategy());
        }

        return self::$package;
    }
}
