<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Models\Appointment::class => \App\Policies\AppointmentPolicy::class,
        \App\Models\MedicalRecord::class => \App\Policies\MedicalRecordPolicy::class,
        \App\Models\File::class => \App\Policies\FilePolicy::class,
            \App\Models\Patient::class => \App\Policies\PatientPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
