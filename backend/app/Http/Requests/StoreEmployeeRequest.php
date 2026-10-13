<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert permissions JSON string to array if it's a string
        if ($this->has('permissions') && is_string($this->permissions)) {
            $decoded = json_decode($this->permissions, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->merge([
                    'permissions' => $decoded
                ]);
            } else {
                // If JSON decode fails, set to empty array
                $this->merge([
                    'permissions' => []
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                   => 'required|string|max:255',
            'last_name'              => 'nullable|string|max:255',
            'employee_id'            => 'required|string|max:100',
            'phone'                  => 'required|string|max:20',
            'email'                  => 'required|email|max:255',
            'joining_date'           => 'required|date',
            'staff_role'             => 'nullable|string|max:255',
            'emp_type'               => 'nullable|string|max:255',
            'profile_image'          => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',
            'department'             => 'nullable|string|max:255',
            'designation'            => 'nullable|string|max:255',
            'exlude_ip_restriction' => 'nullable|boolean',
            'exclude_round_robin'    => 'nullable|boolean',
            'user_box_timing'        => 'nullable|boolean',
            'working_days'           => 'nullable|array',
            'working_days.*'         => 'string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'day_start_time'         => 'nullable',
            'day_end_time'           => 'nullable',
            'allowed_ips'            => 'nullable|array',
            'allowed_ips.*'          => 'nullable|ip',
            'permissions'            => 'nullable|array',
            'permissions.*'          => 'integer|exists:permissions,id',
        ];
    }
}
