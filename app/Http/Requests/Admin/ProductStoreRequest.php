<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProductStoreRequest extends FormRequest
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
        return [
            'name' => 'required|max:30',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Please enter the product name.',
            'name.max' => 'Product name cannot exceed 30 characters.',
            'description.required' => 'Please enter a description.',
            'description.string' => 'The description must be a valid string.',
            'price.required' => 'Please enter the product price.',
            'price.numeric' => 'Please enter a valid number for the price.',
            'status.required' => 'Please select the product status.',
            'status.in' => 'The status must be either active or inactive.',
            'images.required' => 'Please upload at least one image.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg.',
            'images.*.max' => 'Each image must not exceed 2048 kilobytes.',
        ];
    }
}
