<?php

use common\models\User;

return [
    [
        'id' => 1,
        'username' => 'user-1',
        'auth_key' => 'user-1-auth-key',
        'password' => '$2y$13$90kDTNrheUbenbIyNAxww.m7B9TBL2KjnR7w2.RDbIdL9Z4dtqXkC',
        'created_at' => '1392559490',
        'updated_at' => '1392559490',
        'access_token' => 'test-access-token',
        'user_type' => User::TYPE_ADMIN,
    ],
    [
        'username' => 'erau',
        'auth_key' => 'tUu1qHcde0diwUol3xeI-18MuHkkprQI',
        'password' => '$2y$13$nJ1WDlBaGcbCdbNC5.5l4.sgy.OMEKCqtDQOdQ2OWpgiKRWYyzzne',
        // password_0
//        'password_hash' => '$2y$13$nJ1WDlBaGcbCdbNC5.5l4.sgy.OMEKCqtDQOdQ2OWpgiKRWYyzzne',
//        'password_reset_token' => 'RkD_Jw0_8HEedzLk7MM-ZKEFfYR7VbMr_1392559490',
        'created_at' => '1392559490',
        'updated_at' => '1392559490',
        'access_token' => 'test-access-token',
        'user_type' => User::TYPE_ADMIN,
//        'email' => 'sfriesen@jenkins.info',
    ],
];
