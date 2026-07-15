<?php

return [
    'rules' => [
        'public' => ['home'],
        'admin' => [
            'admin.dashboard',
            'admin.companies.index','admin.companies.show','admin.companies.create','admin.companies.store',
            'admin.companies.edit','admin.companies.update','admin.companies.destroy',
            'admin.users.index','admin.users.show','admin.users.create','admin.users.store',
            'admin.users.edit','admin.users.update','admin.users.destroy',
            'admin.posts.index','admin.posts.show','admin.posts.create','admin.posts.store',
            'admin.posts.edit','admin.posts.update','admin.posts.destroy',
            'admin.products.index','admin.products.show','admin.products.create','admin.products.store',
            'admin.products.edit','admin.products.update','admin.products.destroy',
            'admin.plans.index','admin.plans.show','admin.plans.create','admin.plans.store',
            'admin.plans.edit','admin.plans.update','admin.plans.destroy',
            'admin.categories.index','admin.categories.show','admin.categories.create','admin.categories.store',
            'admin.categories.edit','admin.categories.update','admin.categories.destroy',
            'admin.leads.index','admin.leads.destroy',
            'admin.upload-image',
        ],
        'company' => [
            'company.dashboard','company.mycompany','company.mycompany.update',

            'company.products.index','company.products.create','company.products.store',
            'company.products.show','company.products.edit','company.products.update','company.products.destroy',

            'company.posts.index','company.posts.create','company.posts.store',
            'company.posts.show','company.posts.edit','company.posts.update','company.posts.destroy',

            'company.clients.index','company.clients.create','company.clients.store',
            'company.clients.show','company.clients.edit','company.clients.update','company.clients.destroy',

            'company.galleries.index','company.galleries.create','company.galleries.store',
            'company.galleries.show','company.galleries.edit','company.galleries.update','company.galleries.destroy',
            'company.galleries.upload','company.galleries.destroyImage',

            'company.files.index','company.files.create','company.files.store',
            'company.files.show','company.files.edit','company.files.update','company.files.destroy',

            'company.categories.index','company.categories.create','company.categories.store',
            'company.categories.show','company.categories.edit','company.categories.update','company.categories.destroy',

            'company.users.store','company.users.edit','company.users.update','company.users.destroy',
        ],
    ],
];
