<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Carbon\Carbon;

class DocumentoValidate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // dd(Carbon::now()->addDay()->format('Y-m-d'));
        return [
            'ruc' => 'required|max:11',
            'serie' => 'required|max:5',
            'numero' => 'required|max:12',
            'empresa' => 'required',
            'file' => 'required|mimes:pdf',
            'fecha_emision' => 'required|before:'.Carbon::now()->addDay()->format('Y-m-d'),
            'monto' => 'required|numeric|min:1',
            'moneda' => 'required|max:10',
        ];
    }
    
    protected function failedValidation(Validator $validator) {
        //extraer array
        $sin_array=str_replace(["[","]"], "",json_encode($validator->errors()));
        
        throw new HttpResponseException(response()->json([
            "status" => "VALIDATION",
            "data"   =>  json_decode($sin_array)
        ], 200));
    }
}
