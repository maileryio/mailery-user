<?php

namespace Mailery\User\Field;

use Yiisoft\Translator\TranslatorInterface;

class UserStatus
{

    private const ACTIVE = 'active';
    private const DISABLED = 'disabled';

    /**
     * @var TranslatorInterface|null
     */
    private ?TranslatorInterface $translator = null;

    /**
     * @param string $value
     */
    private function __construct(
        private string $value
    ) {
        if (!in_array($value, $this->getValues())) {
            throw new \InvalidArgumentException('Invalid passed value: ' . $value);
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return static
     */
    public static function typecast(string $value): static
    {
        return new static($value);
    }

    /**
     * @return self
     */
    public static function asActive(): self
    {
        return new self(self::ACTIVE);
    }

    /**
     * @return self
     */
    public static function asDisabled(): self
    {
        return new self(self::DISABLED);
    }

    /**
     * @param TranslatorInterface $translator
     * @return self
     */
    public function withTranslator(TranslatorInterface $translator): self
    {
        $new = clone $this;
        $new->translator = $translator;

        return $new;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return [
            self::ACTIVE,
            self::DISABLED,
        ];
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        $fnTranslate = function (string $message) {
            if ($this->translator !== null) {
                return $this->translator->translate($message);
            }
            return $message;
        };

        return [
            self::ACTIVE => $fnTranslate('Active'),
            self::DISABLED => $fnTranslate('Disabled'),
        ][$this->value] ?? 'Unknown';
    }

    /**
     * @return string
     */
    public function getCssClass(): string
    {
        return [
            self::ACTIVE => 'badge-success',
            self::DISABLED => 'badge-danger',
        ][$this->value] ?? 'badge-secondary';
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getValue() === self::ACTIVE;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->getValue() === self::DISABLED;
    }

}
