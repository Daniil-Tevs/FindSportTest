<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\HasMany;

    class Booking extends Model
    {
        protected $hidden = [
            'user_id',
            'created_at',
            'updated_at'
        ];

        public function slots(): HasMany
        {
            return $this->hasMany(BookingSlot::class);
        }
    }
