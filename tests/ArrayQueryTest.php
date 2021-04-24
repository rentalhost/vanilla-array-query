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

            [
                [ 'userIds' => [ 1, 2, 3, 4, 5 ] ],
                [
                    'countBefore' => static function (array $self) {
                        return count($self['userIds']);
                    },
                    'userIds'     => static function (array $self) {
                        return array_values(array_filter($self['userIds'], static function (int $number) {
                            return $number >= 3;
                        }));
                    },
                    'countAfter'  => static function (array $self) {
                        return count($self['userIds']);
                    },
                ],
                [ 'countBefore' => 5, 'userIds' => [ 3, 4, 5 ], 'countAfter' => 3 ],
                'Transforms userIds, returning only when it is greater than 3, so countBefore must be 5, but countAfter must be 3.',
            ],

            [
                [ 'users' => [ 1, 2, 3 ] ],
                [
                    'users' => static function (array $self) {
                        return array_map(static function (int $id) {
                            return [ 'id' => $id ];
                        }, $self['users']);
                    },
                ],
                [ 'users' => [ [ 'id' => 1 ], [ 'id' => 2 ], [ 'id' => 3 ] ] ],
                'Transforms users on-the-fly, but wrapping values into id array.',
            ],

            [
                [ 'users' => [ [ 'id' => 1, 'age' => 25 ], [ 'id' => 2, 'age' => 30 ] ] ],
                [
                    'users' => static function (array $self) {
                        return ArrayQuery::query(array_filter($self['users'], static function (array $users) {
                            return $users['age'] === 25;
                        }), [ [ 'id' ] ]);
                    },
                ],
                [ 'users' => [ [ 'id' => 1 ] ] ],
                'Transforms users, filtering by age === 25 with only id, but keeping the array structure.',
            ],

            [
                [ 'users' => [ [ 'id' => 1 ], [ 'id' => 2 ], [ 'id' => 3 ] ] ],
                [
                    static function (array $self) {
                        return [ 'userIds' => array_column($self['users'], 'id') ];
                    },
                    'countIds' => static function (array $self) {
                        return count($self['userIds']);
                    },
                ],
                [ 'userIds' => [ 1, 2, 3 ], 'countIds' => 3 ],
                'Transforms users, so it will contains only the ids. Count must be 3',
            ],

            [
                [ 'name' => 'John Doe', 'age' => 30 ],
                [ 'age' ],
                [ 'age' => 30 ],
                'Readme example: installation.',
            ],

            [
                [ 'route' => '/home', 'title' => 'Home', 'description' => 'Initial page', ],
                [ 'route', 'title' ],
                [ 'route' => '/home', 'title' => 'Home', ],
                'Readme example: simple extraction.',
            ],

            [
                [ 'header' => [ 'title' => 'Home', 'description' => 'Initial page' ] ],
                [ 'header' => [ 'title' ] ],
                [ 'header' => [ 'title' => 'Home' ] ],
                'Readme example: bidimensional extraction.',
            ],

            [
                [ [ 'route' => '/home', 'title' => 'Home' ], [ 'route' => '/admin', 'title' => 'Administrative' ] ],
                [ [ 'route' ] ],
                [ [ 'route' => '/home' ], [ 'route' => '/admin' ] ],
                'Readme example: multidimensional extraction.',
            ],

            [
                [ 'header' => [ 'title' => 'Home', 'description' => 'Initial page' ] ],
                [
                    'title' => static function (array $page) {
                        return $page['header']['title'];
                    },
                ],
                [ 'title' => 'Home' ],
                'Readme example: customized extraction #1.',
            ],

            [
                [ 'route' => '/home' ],
                [
                    'route' => static function (array $page) {
                        return 'https://...' . $page['route'];
                    },
                ],
                [ 'route' => 'https://.../home' ],
                'Readme example: customized extraction #2.',
            ],

            [
                [ [ 'route' => '/home', 'title' => 'Home' ], [ 'route' => '/admin', 'title' => 'Administrative' ] ],
                [
                    static function (array $page) {
                        return array_combine(
                            array_column($page, 'route'),
                            array_column($page, 'title'),
                        );
                    },
                ],
                [ '/home' => 'Home', '/admin' => 'Administrative' ],
                'Readme example: advanced extraction.',
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
