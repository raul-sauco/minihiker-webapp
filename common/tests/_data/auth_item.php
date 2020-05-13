<?php

$roles = ['admin','user','client'];

$permissions = [
    'listClients', 'viewClient', 'createClient', 'updateClient', 'deleteClient',
    'listFamilies', 'viewFamily', 'createFamily', 'updateFamily', 'deleteFamily',
    'listPrograms', 'viewProgram', 'createProgram', 'updateProgram', 'deleteProgram',
];

$items = [];

foreach ($roles as $role) {
    $items[] = [
        'name' => $role,
        'type' => 1,
        'description' => '',
        'data' => null,
        'created_at' => 1577161056,
        'updated_at' => 1577161056
    ];
}

foreach ($permissions as $permission) {
    $items[] = [
        'name' => $permission,
        'type' => 2,
        'description' => '',
        'data' => null,
        'created_at' => 1577161056,
        'updated_at' => 1577161056
    ];
}

return array_merge($items, [

    /* ********** FAMILY ********** */
    [
        'name' => 'userAndClientAreFamily',
        'type' => 2,
        'description' => 'Determine if the current user application and the client belong to the same family',
        'rule_name' => 'userAndClientAreFamilyRule',
        'data' => null,
        'created_at' => 1577161056,
        'updated_at' => 1577161056
    ],
    [
        'name' => 'userBelongsToFamily',
        'type' => 2,
        'description' => 'Determine if the current application user belongs to the family',
        'rule_name' => 'userBelongsToFamilyRule',
        'data' => null,
        'created_at' => 1577161056,
        'updated_at' => 1577161056
    ],

    [
        'name' => 'userIsThisClient',
        'type' => 2,
        'description' => 'Determine if the current application user is this client',
        'rule_name' => 'userIsThisClient',
        'data' => null,
        'created_at' => 1577161056,
        'updated_at' => 1577161056
    ],
]);
