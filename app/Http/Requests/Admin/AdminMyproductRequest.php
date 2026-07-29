<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use RealRashid\SweetAlert\Facades\Alert;

class AdminMyproductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|min:3',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'product_image' => 'nullable|image|file|max:2048',
            'product_description' => 'nullable|string',
            'old_product_image' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'product_name.required' => 'Nama produk wajib diisi',
            'product_name.min' => 'Nama produk minimal 3 karakter',
            'stock.required' => 'Stok wajib diisi',
            'stock.integer' => 'Stok harus berupa angka',
            'stock.min' => 'Stok minimal 0',
            'price.required' => 'Harga wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
            'product_image.image' => 'Foto wajib berjenis image',
            'product_image.file' => 'Foto wajib berupa file',
            'product_image.max' => 'Foto maksimal berukuran 2 Mb',
            'product_description.string' => 'Deskripsi harus berupa teks',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $allErrors = implode('<br>', $validator->errors()->all());

        Alert::html('Validasi Gagal!', $allErrors, 'error');

        throw new HttpResponseException(
            redirect()->back()->withInput()->withErrors($validator)
        );
    }
}
