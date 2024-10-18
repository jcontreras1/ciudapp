<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

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
        'email_verified_at',
        'two_factor_confirmed_at',
        'current_team_id',
        'created_at',
        'updated_at',
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

    public function isAdmin()
    {
        return boolval($this->is_admin);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function quiere($ability){
        //Escanear entre las preferencias del usuario
        $preferecnes = $this->preferecnes;
        if(count($preferecnes) == 0){
            return true;
        }

        $res= in_array($ability, $preferecnes->map(function($item){
            return $item->code;
        })->toArray());

        return "hol:" . boolval($res);

    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function commentLikes()
    {
        return $this->hasMany(PostCommentLike::class);
    }

    public function institutions()
    {
        return $this->belongsToMany(Institution::class, 'user_institution')->withPivot('is_admin');
    }

    public function regionSubcategory(){
        return $this->belongsToMany(RegionSubcategory::class, 'user_region_subcategory')->withPivot('id');
    }

    public function userRegionSubcategory(){
        return $this->hasMany(UserRegionSubcategory::class);
    }

    public function preferecnes(){
        return $this->belongsToMany(Preference::class,'user_preference')->withPivot(['id', 'value']);
    }
}
