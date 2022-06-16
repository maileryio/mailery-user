<?php

namespace Mailery\User\Model;

use DateTime;
use DateTimeZone;

class Timezones
{

    /**
     * @var bool
     */
    private bool $groups = false;

    /**
     * @var bool
     */
    private bool $offsets = true;

    /**
     * @var string|null
     */
    private ?string $countryCode = null;

    /**
     * @param bool $groups
     * @return self
     */
    public function withGroups(bool $groups): self
    {
        $new = clone $this;
        $new->groups = $groups;

        return $new;
    }

    /**
     * @param bool $offsets
     * @return self
     */
    public function withOffsets(bool $offsets): self
    {
        $new = clone $this;
        $new->offsets = $offsets;

        return $new;
    }

    /**
     * @param string $countryCode
     * @return self
     */
    public function withNearestBy(string $countryCode): self
    {
        $new = clone $this;
        $new->countryCode = $countryCode;

        return $new;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $nearest = $this->getNearest();

        $identifiers = \DateTimeZone::listIdentifiers();
        $identifiers = $this->getWithOffsets(array_combine($identifiers, $identifiers));

        if ($this->groups === false) {
            $nearest = $nearest;
            foreach ($identifiers as $area => $label) {
                $nearest[$area] = $label;
            }
            return $nearest;
        }

        $groups = [
            'Nearest' => $nearest,
        ];

        foreach ($identifiers as $identifier => $label) {
            $parts = explode('/', $identifier);

            if (in_array($parts[0], $this->getDesiredGroups())) {
                $groups[$parts[0]][$identifier] = $label;
            }
        }

        return $groups;
    }

    /**
     * @return array
     */
    public function getNearest(): array
    {
        if ($this->countryCode === null) {
            return [];
        }

        $identifiers = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $this->countryCode);

        return $this->getWithOffsets(array_combine($identifiers, $identifiers));
    }

    /**
     * @param array $identifiers
     * @return array
     */
    private function getWithOffsets(array $identifiers): array
    {
        if ($this->offsets === false) {
            return $identifiers;
        }

        foreach ($identifiers as $area => $label) {
            $timeInArea = new DateTime('now', new DateTimeZone($area));
            $timeDiff = $timeInArea->format('P');
            $identifiers[$area] = sprintf('(GMT %s) %s', $timeDiff, $label);
        }

        return $identifiers;
    }

    /**
     * @return array
     */
    private function getDesiredGroups(): array
    {
        return [
            'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific',
        ];
    }

}
