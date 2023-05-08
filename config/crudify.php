<?php

return [
    'stubs' => [
        'views' => [
            'index' => null, // [null = default|patch]
            'create' => null,
            'edit' => null,
            'show' => null,
        ]
    ],
    'theme' => 'bootstrap', // [bootstrap|tailwind]
    'blueprint_types' => [
        'increments', 'integerIncrements', 'tinyIncrements', 'smallIncrements', 'mediumIncrements', 'bigIncrements',
        'char', 'string', 'text', 'mediumText', 'longText',
        'integer',
        'unsignedInteger', 'unsignedTinyInteger', 'unsignedSmallInteger', 'unsignedMediumInteger', 'unsignedBigInteger',
        'float',
        'boolean',
        'enum', 'set',
        'json', 'jsonb',
        'date', 'datetime', 'dateTimeTz',
        'time', 'timestamp',
        'timestamps',
        'year',
        'binary',
        'uuid',
        'ipAddress',
        'macAddress',
        'geometry', 'point', 'lineString', 'polygon',
        'computed',
        'morphs', 'nullableMorphs', 'uuidMorphs', 'nullableUuidMorphs',
        'rememberToken',
        'foreign', 'foreignId', 'foreignIdFor', 'foreignUuid'
    ],

    'string_types' => [
        'char', 'string', 'text', 'mediumText', 'longText', 'json', 'jsonb',
    ],

    'integer_types' => [
        'increments', 'integerIncrements', 'tinyIncrements', 'smallIncrements', 'mediumIncrements', 'bigIncrements',
        'integer', 'tinyInteger', 'smallInteger', 'mediumInteger', 'bigInteger',
        'unsignedInteger', 'unsignedTinyInteger', 'unsignedSmallInteger', 'unsignedMediumInteger', 'unsignedBigInteger',
    ],

    'float_types' => [
        'float', 'double', 'decimal', 'unsignedDecimal',
    ],

    'date_types' => [
        'date', 'datetime', 'dateTimeTz',
        'time', 'timestamp',
        'timestamps', 'timestampsTz', 'softDeletes', 'softDeletesTz',
        'year'
    ],

    'foreign_types' => [
        'foreign', 'foreignId', 'foreignIdFor', 'foreignUuid',
    ],
];
