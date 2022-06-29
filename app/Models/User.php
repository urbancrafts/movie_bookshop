<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
//Models Classes
use App\Models\BankAccountDetail;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'ng_wallet',
        'is_verified',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    } 
    

    public function bank_account_detail()
    {
        return $this->hasMany(BankAccountDetail::class);
    }

    public function notification()
    {
        return $this->hasMany(notification::class);
    }

    public function support()
    {
        return $this->hasMany(support::class);
    }
    


    
    public static function get_all_user_assoc_data($user_id){
        $data = self::where('id', $user_id)->get();
        $array_data = array();
        if(count($data) > 0){
           
           $result['profile'] = self::where('id', $data[0]->id)->get();
           $result['bank_account_detail'] = $this->bank_account_detail()->where($data[0]->id);
           array_push($array_data, $result);
           
            return $array_data; 
        }else{
            return false;
        }
    }

    

}