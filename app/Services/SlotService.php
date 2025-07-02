<?php

    namespace App\Services;

    use App\Models\BookingSlot;
    use Illuminate\Support\Carbon;

    class SlotService
    {
        public static function formatData(array $slot): array
        {
            return [
                'start_time' => Carbon::parse($slot['start_time']),
                'end_time' => Carbon::parse($slot['end_time']),
            ];
        }

        public static function isNotValid(array $data, BookingSlot $slot = null): string|false
        {
            $data = [$data];

            if (!BookingService::isValidSlotsDate($data))
                return "Slot's dates are wrong";

            if (!BookingService::isFreeSlotsDate($data, $slot ? [$slot->id] : []))
                return "Choosed dates for slots are not free";

            return false;
        }
    }
