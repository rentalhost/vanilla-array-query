<?php

declare(strict_types = 1);

namespace Rentalhost\Vanilla\ArrayQuery\Tests;

use PHPUnit\Framework\TestCase;
use Rentalhost\Vanilla\ArrayQuery\ArrayQuery;

class ArrayQueryTest
    extends TestCase
{
    public function dataProviderQueryMethod(): array
    {
        return [
            [
                [ 1, 2, 3 ],
                null,
                null,
                'Null query should results in null output.',
            ],

            [
                [ 1, 2, 3 ],
                [],
                [ 1, 2, 3 ],
                'Empty query array should results in source as output.',
            ],

            [
                [ 1, 2, 3 ],
                [ null ],
                [],
                'Null key name should discards all root childs.',
            ],

            [
                [ 'user' => 'John Doe', 'age' => 18 ],
                [ 'user' ],
                [ 'user' => 'John Doe' ],
                'Extracts only user key.',
            ],

            [
                [ 'user' => 'John Doe', 'age' => 18 ],
                [ 'user', 'age' ],
                [ 'user' => 'John Doe', 'age' => 18 ],
                'Extracts both user and age keys.',
            ],

            [
                [ 'user' => 'John Doe', 'age' => 18 ],
                [ 'age', 'user' ],
                [ 'age' => 18, 'user' => 'John Doe' ],
                'Extracts both user and age keys (explicitly reversed).',
            ],

            [
                [ 'user' => [ 'firstname' => 'John', 'lastname' => 'Doe' ] ],
                [ 'user' ],
                [ 'user' => [ 'firstname' => 'John', 'lastname' => 'Doe' ] ],
                'Extracts entire user key and contents.',
            ],

            [
                [ 'user' => [ 'firstname' => 'John', 'lastname' => 'Doe' ], 'age' => 18 ],
                [ 'user' => [ 'firstname' ], 'age' ],
                [ 'user' => [ 'firstname' => 'John' ], 'age' => 18 ],
                'Extracts only user key, limited by firstname contents, and age.',
            ],

            [
                [ 'userIds' => [ 1, 2, 3 ] ],
                [ 'userIds' ],
                [ 'userIds' => [ 1, 2, 3 ] ],
                'Extracts userIds as-is.',
            ],

            [
                [ 'userIds' => [ 1, 2, 3 ] ],
                [ 'userIds' => [] ],
                [ 'userIds' => [ 1, 2, 3 ] ],
                'Extracts userIds as-is.',
            ],

            [
                [ 'userIds' => [ 1, 2, 3 ] ],
                [ 'userIds' => [ null ] ],
                [ 'userIds' => [] ],
                'Extracts userIds, but ignoring all common elements.',
            ],

            [
                [ 'users' => [ [ 'id' => 1, 'name' => 'John' ], [ 'id' => 2, 'name' => 'Doe' ] ] ],
                [ 'users' => [ [ 'id' ] ] ],
                [ 'users' => [ [ 'id' => 1 ], [ 'id' => 2 ] ] ],
                'Extracts users ids in a multidimensional array.',
            ],

            [
                [ 'user' => [ 'id' => 123 ] ],
                [
                    'userId' => static function (array $self) {
                        return $self['user']['id'];
                    },
                ],
                [ 'userId' => 123 ],
                'Transforms user.id into userId directly.',
            ],

            [
                [ 'users' => [ [ 'id' => 1 ], [ 'id' => 2 ], [ 'id' => 3 ] ] ],
                [
                    'usersIds' => static function (array $self) {
                        return array_column($self['users'], 'id');
                    },
                ],
                [ 'usersIds' => [ 1, 2, 3 ] ],
                'Transforms users.*.id into usersIds directly.',
            ],

            [
                [ 'users' => [ 'ids' => [ [ 'id' => 1 ], [ 'id' => 2 ], [ 'id' => 3 ] ] ] ],
                [
                    'users' => [
                        'ids' => static function (array $self) {
                            return array_column($self['ids'], 'id');
                        },
                    ],
                ],
                [ 'users' => [ 'ids' => [ 1, 2, 3 ] ] ],
                'Transforms multidimensional users.ids.* into users.ids directly.',
            ],

            [
                [ 'test' => [ 'PhpToken' => 1, 'tokenize' => 2 ] ],
                [ 'test' => [ 'PhpToken', 'tokenize' ] ],
                [ 'test' => [ 'PhpToken' => 1, 'tokenize' => 2 ] ],
                'Ensure that PhpToken::tokenize() will never be called, because it is_callable().',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderQueryMethod
     */
    public function testQueryMethod(?array $source, ?array $query, ?array $expected, ?string $message = null): void
    {
        self::assertSame($expected, ArrayQuery::query($source, $query), (string) $message);
    }
}
