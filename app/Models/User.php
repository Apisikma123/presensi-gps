<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */


    protected $fillable = [
        'name',
        'username',
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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi dengan Cabang (Many to Many)
    public function cabangs()
    {
        return $this->belongsToMany(Cabang::class, 'user_cabang_access', 'user_id', 'kode_cabang', 'id', 'kode_cabang');
    }

    // Relasi dengan Departemen (Many to Many)
    public function departemens()
    {
        return $this->belongsToMany(Departemen::class, 'user_departemen_access', 'user_id', 'kode_dept', 'id', 'kode_dept');
    }

    protected ?bool $isSuperAdminMemo = null;
    protected ?array $cabangCodesMemo = null;
    protected ?array $departemenCodesMemo = null;
    protected $cabangMemo = null;
    protected $departemenMemo = null;

    /**
     * Cek apakah user adalah super admin
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        if ($this->isSuperAdminMemo !== null) {
            return $this->isSuperAdminMemo;
        }
        return $this->isSuperAdminMemo = $this->roles->contains(function ($role) {
            return strtolower($role->name) === 'super admin';
        });
    }

    /**
     * Get cabang yang dapat diakses user
     * Super admin mendapatkan semua cabang, user biasa hanya cabang yang diakses
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Cabang[]
     */
    public function getCabang()
    {
        if ($this->cabangMemo !== null) {
            return $this->cabangMemo;
        }
        if ($this->isSuperAdmin()) {
            return $this->cabangMemo = Cabang::orderBy('kode_cabang')->get();
        }

        $userCabangs = $this->cabangs->pluck('kode_cabang')->toArray();
        if (!empty($userCabangs)) {
            return $this->cabangMemo = Cabang::whereIn('kode_cabang', $userCabangs)->orderBy('kode_cabang')->get();
        }

        return $this->cabangMemo = collect(); // Empty collection jika tidak ada akses
    }

    /**
     * Get departemen yang dapat diakses user
     * Super admin mendapatkan semua departemen, user biasa hanya departemen yang diakses
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Departemen[]
     */
    public function getDepartemen()
    {
        if ($this->departemenMemo !== null) {
            return $this->departemenMemo;
        }
        if ($this->isSuperAdmin()) {
            return $this->departemenMemo = Departemen::orderBy('kode_dept')->get();
        }

        $userDepartemens = $this->departemens->pluck('kode_dept')->toArray();
        if (!empty($userDepartemens)) {
            return $this->departemenMemo = Departemen::whereIn('kode_dept', $userDepartemens)->orderBy('kode_dept')->get();
        }

        return $this->departemenMemo = collect(); // Empty collection jika tidak ada akses
    }

    /**
     * Get array kode cabang yang dapat diakses user
     *
     * @return array<string>
     */
    public function getCabangCodes(): array
    {
        if ($this->cabangCodesMemo !== null) {
            return $this->cabangCodesMemo;
        }
        if ($this->isSuperAdmin()) {
            return $this->cabangCodesMemo = Cabang::pluck('kode_cabang')->toArray();
        }

        return $this->cabangCodesMemo = $this->cabangs->pluck('kode_cabang')->toArray();
    }

    /**
     * Get array kode departemen yang dapat diakses user
     *
     * @return array<string>
     */
    public function getDepartemenCodes(): array
    {
        if ($this->departemenCodesMemo !== null) {
            return $this->departemenCodesMemo;
        }
        if ($this->isSuperAdmin()) {
            return $this->departemenCodesMemo = Departemen::pluck('kode_dept')->toArray();
        }

        return $this->departemenCodesMemo = $this->departemens->pluck('kode_dept')->toArray();
    }

    /**
     * Relasi ke Userkaryawan (jika user ini adalah karyawan)
     */
    public function userkaryawan()
    {
        return $this->hasOne(Userkaryawan::class, 'id_user');
    }

    /**
     * Get linked approval admin user (jika user ini karyawan dan punya approval admin)
     *
     * @return User|null
     */
    public function getApprovalAdmin()
    {
        $uk = $this->userkaryawan;
        if ($uk && $uk->approval_admin_id) {
            return User::find($uk->approval_admin_id);
        }
        return null;
    }
}
