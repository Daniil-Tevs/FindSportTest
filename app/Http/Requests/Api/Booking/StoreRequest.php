<?php

    namespace App\Http\Requests\Api\Booking;

    use Illuminate\Foundation\Http\FormRequest;

    class StoreRequest extends FormRequest
    {
        public function authorize(): bool
        {
            return !empty($this->user);
        }

        public function rules(): array
        {
            return [
                'slots' => 'required|array',
                'slots.*.start_time' => ['required', 'date'],
                'slots.*.end_time' => ['required', 'date'],
            ];
        }
    }
