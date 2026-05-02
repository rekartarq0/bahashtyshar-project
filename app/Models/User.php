<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getRoleNamesAttribute()
    {
        return $this->getRoleNames(); // This comes from Spatie's HasRoles trait
    }


    //relations 
    public function Category()
    {
        return $this->hasMany(Category::class);
    }
    public function Projects()
    {
        return $this->hasMany(Projects::class);
    }
    public function ProjectStage()
    {
        return $this->hasMany(ProjectStage::class);
    }
    public function InvoiceProjects()
    {
        return $this->hasMany(InvoiceProjects::class);
    }
    public function invoice()
    {
        return $this->hasMany(Invoices::class);
    }
    public function Mulk()
    {
        return $this->hasMany(Mulks::class);
    }
    public function Customer()
    {
        return $this->hasMany(Customers::class);
    }
    public function MulkStage()
    {
        return $this->hasMany(MulkStages::class);
    }
    public function invoiceAsaish()
    {
        return $this->hasMany(InvoicesAsaish::class);
    }
    //InvoiceNormal
    public function invoiceNormal()
    {
        return $this->hasMany(InvoiceNormal::class);
    }
}
