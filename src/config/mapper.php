<?php

return [
    'transformation' => [
        /**
         * allow null: boolean(default: false)
         */
        'allow_null' => false,

        /**
         * case style: snake_case or camel_case
         */
        'case_style' => 'snake_case',
    ],
    'map_namespace' => '\App\Libraries\MapperV2\Maps',
    /**
     * Map mapping dto and entity
     */
    'maps' => [
        /**
         * example
         */
//        exampleMaps::class => [
//            'entity' => exampleEntity::class,
//            'dto' => exampleDto::class
//        ]
    ]
];
