<?php

namespace App\Generators;

use App\Contracts\Engine\StringGeneratorContract;
use App\Enums\Generator\StringPattern;
use Exception;

class StringGenerator implements StringGeneratorContract
{
    protected StringPattern $pattern;

    protected array $mapping = [
        'a' => [
            'alpha'
        ],
        'n' => [
            'numeric'
        ],
        'x' => [
            'alpha',
            'numeric'
        ],
        'y' => 'y',
        '-' => '-'
    ];

    protected array $alpha = [
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
    ];

    protected array $numeric = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
    ];


    /**
     * @param  StringPattern  $pattern
     */
    public function __construct(StringPattern $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function generate(): string
    {
        $result = '';

        foreach (str_split($this->pattern->value) as $index => $character) {
            if (!isset($this->mapping[strtolower($character)])) {
                throw new Exception("Character $character can not be mapped");
            }

            if (in_array($character, ['-', 'y', 'Y'])) {
                $result .= $character;
                continue;
            }

            $elements = [];

            foreach ($this->mapping[strtolower($character)] as $set) {
                $setElements = match ($set) {
                    'alpha' => $this->alpha,
                    'numeric' => $this->numeric,
                };

                $elements = array_merge($elements, $setElements);
            }

            $element = $elements[random_int(0, count($elements) - 1)];

            if ($character !== strtolower($character)) {
                $element = strtoupper($element);
            }

            $result .= $element;
        }

        $result = str_replace('yyyy', now()->format('Y'), $result);
        $result = str_replace('YYYY', now()->format('Y'), $result);

        return $result;
    }
}
