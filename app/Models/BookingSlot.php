<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class BookingSlot extends Model
    {
        protected $fillable = [
            'start_time',
            'end_time'
        ];

        protected $hidden = [
            'booking_id',
            'created_at',
            'updated_at'
        ];
    }
