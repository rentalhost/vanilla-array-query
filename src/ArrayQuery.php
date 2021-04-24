<?php

declare(strict_types = 1);

namespace Rentalhost\Vanilla\ArrayQuery;

class ArrayQuery
{
    public static function query(?array $source, ?array $query): ?array
    {
        if ($query === null) {
            return null;
        }

        if ($source === null ||
            $query === []) {
            return $source;
        }

        $output = [];

        foreach ($query as $queryKeyGroup => $queryKeyName) {
            if (is_int($queryKeyGroup)) {
                if ($queryKeyName !== null) {
                    // Eg. [ 'users' => [ [ 'id' ] ] ]
                    if (is_array($queryKeyName)) {
                        foreach ($source as $sourceKey => $sourceValue) {
                            $output[$sourceKey] = self::query($sourceValue, $queryKeyName);
                        }
                    }
                    // Eg. [ function (array $self) { ... } ]
                    else if (is_callable($queryKeyName)) {
                        foreach ($queryKeyName($source) as $callableKey => $callableValue) {
                            $source[$callableKey] = $output[$callableKey] = $callableValue;
                        }
                    }
                    // Eg. [ 'user' ]
                    else if (array_key_exists($queryKeyName, $source)) {
                        $output[$queryKeyName] = $source[$queryKeyName];
                    }
                }
            }
            // Eg. [ 'user' => function (array $self) { ... } ]
            else if (is_callable($queryKeyName)) {
                $source[$queryKeyGroup] = $output[$queryKeyGroup] = $queryKeyName($source);
            }
            // Eg. [ 'user' => ... ]
            else if (array_key_exists($queryKeyGroup, $source)) {
                $output[$queryKeyGroup] = self::query($source[$queryKeyGroup], $queryKeyName);
            }
        }

        return $output;
    }
}
