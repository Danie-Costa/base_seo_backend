<?php

return [
    'rules' => [
        'public' => [
            'home',
        ],
        'admin' => [
            'admin.dashboard',

            'admin.companies.index',
            'admin.companies.show',
            'admin.companies.create',
            'admin.companies.store',
            'admin.companies.edit',
            'admin.companies.update',
            'admin.companies.destroy',

            'admin.users.index',
            'admin.users.show',
            'admin.users.create',
            'admin.users.store',
            'admin.users.edit',
            'admin.users.update',
            'admin.users.destroy'
        ],
       
    ],

];
