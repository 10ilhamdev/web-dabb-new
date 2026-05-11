<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordANRI;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public static function getEnumValues(string $table, string $column): array
    {
        try {
            $result = \Illuminate\Support\Facades\DB::select(
                "SHOW COLUMNS FROM {$table} WHERE Field = ?",
                [$column]
            );

            if (empty($result)) {
                return [];
            }

            $type = $result[0]->Type;
            preg_match('/^enum\((.*)\)$/', $type, $matches);

            if (empty($matches)) {
                return [];
            }

            $enum = [];
            foreach (explode(',', $matches[1]) as $value) {
                $enum[] = trim($value, "'");
            }
            return $enum;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'photo',
        'google_id',
        'email_verified_at',
    ];

    /**
     * Get the role model for this user.
     */
    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }

    /**
     * Get UI labels for each role (dynamic from roles table).
     */
    public static function roleLabels(): array
    {
        return Role::roleLabels();
    }

    /**
     * Get profile columns from role_columns for a given role.
     */
    public static function roleProfileColumns(string $role): \Illuminate\Database\Eloquent\Collection
    {
        return Role::profileColumnsFor($role);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordANRI($token));
    }

    /* Relationships */
    public function getProfileAttribute()
    {
        $role = Role::where('name', $this->role)->first();

        if (! $role || ! $role->relation_name) {
            return null;
        }

        $relation = $role->relation_name;

        if (! method_exists($this, $relation)) {
            return null;
        }

        return $this->{$relation};
    }

    public function userUmum()
    {
        return $this->hasOne(UserUmum::class);
    }

    public function userPelajar()
    {
        return $this->hasOne(UserPelajar::class);
    }

    public function userInstansi()
    {
        return $this->hasOne(UserInstansiSwasta::class);
    }

    public function userAdmin()
    {
        return $this->hasOne(UserAdmin::class);
    }

    public function userPegawai()
    {
        return $this->hasOne(UserPegawai::class);
    }

    /**
     * Handle dynamic relation calls for role profiles.
     */
    public function __call($method, $parameters)
    {
        // Check if this is a dynamic role relation
        $role = Role::where('relation_name', $method)->first();
        if ($role) {
            $modelClass = 'App\\Models\\' . ucfirst($method);
            if (class_exists($modelClass)) {
                return $this->hasOne($modelClass);
            }
        }

        return parent::__call($method, $parameters);
    }
}
