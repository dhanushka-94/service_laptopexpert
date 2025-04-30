<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceJobRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'technician_id' => 'nullable|exists:users,id',
            'device_type' => 'required|in:Laptop,Desktop,Other',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'reported_issues' => 'required|string',
            'accessories' => 'nullable|string',
            'status' => 'required|in:Pending,In Progress,Awaiting Parts,Repaired,Delivered,Canceled',
            'diagnosis' => 'nullable|string',
            'repair_notes' => 'nullable|string',
            'parts_used' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'final_cost' => 'nullable|numeric|min:0',
            'completion_date' => 'nullable|date',
        ];
    }
}
