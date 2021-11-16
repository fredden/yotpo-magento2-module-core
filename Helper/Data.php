<?php

namespace Yotpo\Core\Helper;

use Magento\Framework\Stdlib\DateTime\Timezone;

/**
 * Class Data for helper functions
 */
class Data
{
    /**
     * @var Timezone
     */
    private $timezone;

    /**
     * Data constructor.
     * @param Timezone $timezone
     */
    public function __construct(
        Timezone $timezone
    ) {
        $this->timezone = $timezone;
    }

    /**
     * @param string $number
     * @param string $countryCode
     * @return string|null
     */
    public function formatPhoneNumber($number, $countryCode)
    {
        if (!$number) {
            return $number;
        }
        $number = str_replace(' ', '', $number);
        $number = str_replace('-', '', $number);
        $number = str_replace('(', '', $number);
        $number = str_replace(')', '', $number);
        $number = ltrim($number, '0');

        if (!empty($countryCode)) {
            $prefix = $this->getCountryDigitPrefixFromArray($countryCode);
            if (!empty($prefix)) {
                $numberCheck = ltrim($number, '+');
                $numberCheck = ltrim($numberCheck, '0');
                $prefixCheck = ltrim($prefix, '+');

                $formattedNumber = null;

                /* Discard invalid phone numbers */
                if (strlen($numberCheck) < 7 || strlen($numberCheck) > 15) {
                    return null;
                }
                if (is_numeric($number)) {
                    if ((strpos($number, '+') === 0 || strpos($number, '00') === 0)
                        && strpos($numberCheck, $prefixCheck) === 0
                    ) {
                        $formattedNumber = '+' . $numberCheck;
                    } elseif ((strpos($number, '+') === 0 || strpos($number, '00') === 0)
                        && strpos($numberCheck, $prefixCheck) !== 0
                    ) {
                        $formattedNumber = '+' . $prefixCheck . $numberCheck;
                    } elseif (strpos($numberCheck, $prefixCheck) !== 0
                        || (strtolower($countryCode) == 'no' && strlen($numberCheck) <= 8)
                    ) {
                        $formattedNumber = '+' . $prefix . $numberCheck;
                    } elseif (strpos($numberCheck, $prefixCheck) === 0
                        && strpos($numberCheck, '0') === strlen($prefixCheck)
                    ) {
                        $formattedNumber = ltrim($numberCheck, $prefix);
                        $formattedNumber = ltrim($formattedNumber, '0');
                        $formattedNumber = '+' . $prefixCheck . $formattedNumber;
                    } elseif ((strpos($number, '+') !== 0) && (strpos($number, '00') !== 0)
                        && strpos($numberCheck, $prefixCheck) === 0
                    ) {
                        $formattedNumber = '+' . $number;
                    } else {
                        $formattedNumber = null;
                    }
                }
                return $formattedNumber;
            } else {
                return $number;
            }
        }
        return $number;
    }

    /**
     * Format date
     *
     * @param \DateTimeInterface|string|null $date
     * @return false|string|null
     */
    public function formatDate($date)
    {
        if ($date) {
            $date = $this->timezone->formatDateTime(
                $date,
                \IntlDateFormatter::SHORT,
                \IntlDateFormatter::SHORT,
                null,
                null,
                "yyyy-MM-dd HH:mm:ss"
            );
        }
        $time = $date ? strtotime($date) : null;
        return $time ? date("Y-m-d\TH:i:s\Z", $time) : null;
    }

    /**
     * Format date
     *
     * @param string|null $date
     * @return false|string|null
     */
    public function formatAdminConfigDate($date)
    {
        $time = $date ? strtotime($date) : null;
        return $time ? date("d M Y H:i:s", $time) : null;
    }

    /**
     * Format date
     *
     * @param string|null $date
     * @return false|string|null
     */
    public function formatOrderItemDate($date)
    {
        $time = $date ? strtotime($date) : null;
        return $time ? date("Y-m-d H:i:s", $time) : null;
    }

    /**
     * @param string $countryCode
     * @return string
     */
    public function getCountryDigitPrefixFromArray($countryCode)
    {
        $phoneCodes = PhoneCodes::$phoneCodes;
        $prefix = array_key_exists($countryCode, $phoneCodes) ? $phoneCodes[$countryCode] : '';
        return $prefix;
    }
}
