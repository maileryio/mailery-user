<?php

namespace Mailery\User\Setting;

use Mailery\Setting\Model\SettingGroup;
use Mailery\Setting\Model\SettingInterface;

class UserSettingGroup extends SettingGroup
{
    public const PARAM_DEFAULT_COUNTRY = 'default-country';
    public const PARAM_DEFAULT_TIMEZONE = 'default-timezone';
    public const PARAM_DEFAULT_DATE_TIME_FORMAT = 'default-date-time-format';

    /**
     * @return SettingInterface|null
     */
    public function getDefaultCountry(): ?SettingInterface
    {
        return $this->get(self::PARAM_DEFAULT_COUNTRY);
    }

    /**
     * @return SettingInterface|null
     */
    public function getDefaultTimezone(): ?SettingInterface
    {
        return $this->get(self::PARAM_DEFAULT_TIMEZONE);
    }

    /**
     * @return SettingInterface|null
     */
    public function getDefaultDateTimeFormat(): ?SettingInterface
    {
        return $this->get(self::PARAM_DEFAULT_DATE_TIME_FORMAT);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return 'User';
    }
}
