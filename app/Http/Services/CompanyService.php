<?php

namespace App\Http\Services;

use App\Models\Company;
use Exception;

class CompanyService
{
    private Company $myCompany;

    public function __construct()
    {
        $company = Company::find(auth()->user()->company_id);

        if (!$company) {
            throw new Exception('Empresa não encontrada.');
        }

        $this->myCompany = $company;
    }

    public function myCompany(array $relations = []): Company
    {
        $company = Company::with($relations)
            ->find(auth()->user()->company_id);

        if (!$company) {
            throw new Exception('Empresa não encontrada.');
        }

        return $company;
    }
}