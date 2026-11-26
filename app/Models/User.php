<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Order;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
    ];

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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function posOrders()
    {
        return $this->hasMany(PosOrder::class, 'user_id');
    }

    public function onlineOrders()
    {
        return $this->hasMany(Order::class, 'staff_id');
    }

    public function can_do(string $module, string $action = 'view'): bool
    {
        if (in_array($this->utype, ['CEO', 'ADM'])) {
            return true;
        }

        $permission = \Illuminate\Support\Facades\DB::table('permissions')
            ->where('user_id', $this->id)
            ->where('module', $module)
            ->first();

        if (!$permission) {
            return false;
        }

        return match ($action) {
            'view'   => (bool) $permission->can_view,
            'add'    => (bool) $permission->can_add,
            'edit'   => (bool) $permission->can_edit,
            'delete' => (bool) $permission->can_delete,
            default  => false,
        };
    }
}
