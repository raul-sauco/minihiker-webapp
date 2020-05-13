<?php

$adminItems = [
    'deleteClient',
    'deleteFamily',
    'deleteLocation',
    'deleteProgram',
    'deleteSupplier',
    'listUsers','viewUser','updateUsers','createUser', 'deleteUser',
    'user'
];

$userItems = [
    'listClients','viewClient','createClient','updateClient','deleteClient',
    'listFamilies','viewFamily','createFamily','updateFamily',
    'listPrograms','viewProgram','createProgram','updateProgram',
];

$clientItems = [
    'createClient',
    'userAndClientAreFamily',
    'userBelongsToFamily',
    'userIsThisClient'
];

$items = [];

foreach ($adminItems as $adminItem) {
    $items[] = [
        'parent' => 'admin',
        'child' => $adminItem
    ];
}

foreach ($userItems as $userItem) {
    $items[] = [
        'parent' => 'user',
        'child' => $userItem
    ];
}

foreach ($clientItems as $clientItem) {
    $items[] = [
        'parent' => 'client',
        'child' => $clientItem
    ];
}

return array_merge($items, [

    /* ********************************
     * ************ RULES *************
     * ********************************
     */
    [
        'parent' => 'userAndClientAreFamily',
        'child' => 'deleteClient'
    ],
    [
        'parent' => 'userAndClientAreFamily',
        'child' => 'updateClient'
    ],
    [
        'parent' => 'userAndClientAreFamily',
        'child' => 'viewClient'
    ],
    [
        'parent' => 'userBelongsToFamily',
        'child' => 'updateFamily'
    ],
    [
        'parent' => 'userBelongsToFamily',
        'child' => 'viewFamily'
    ],
    [
        'parent' => 'userIsThisClient',
        'child' => 'updateClient'
    ],
    [
        'parent' => 'userIsThisClient',
        'child' => 'viewClient'
    ]
]);
