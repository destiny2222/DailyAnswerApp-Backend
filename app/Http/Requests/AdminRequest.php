<?php

namespace App\Http\Requests;

use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'field'=>['required','string'],
            'password'=>['required']
        ];
    }
    public function authenticate()
    {
        $adminusers = Admin::where('email', $this->field)->orWhere('phone',$this->field)->get();
        foreach ($adminusers as $adminuser) {
            if (!$adminuser || Hash::check($this->password, $adminuser->password)) {
                Auth::guard('admin')->login($adminuser);
                return;
            }
            throw ValidationException::withMessages([
                'field'=>'The data does not match with what we have in our database'
            ]);
        }
        
    }
}
