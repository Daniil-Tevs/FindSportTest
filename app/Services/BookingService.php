<?php

    namespace App\Services;

    use App\Models\Booking;
    use App\Models\BookingSlot;

    class BookingService
    {
        public static function isValidSlotsDate($slots): bool
        {
            foreach ($slots as $slot)
                if ($slot['start_time'] > $slot['end_time'])
                    return false;

            $amount = count($slots);

            usort($slots, fn($a, $b) => $a['start_time']->lt($b['start_time']) ? -1 : 1);

            for ($i = 0; $i < $amount - 1; $i++)
                if ($slots[$i]['end_time'] > $slots[$i + 1]['start_time'])
                    return false;

//            for ($i = 0; $i < $amount - 1; $i++) {
//                for ($j = $i + 1; $j < $amount; $j++) {
//                    if (
//                        ($slots[$i]['start_time'] < $slots[$j]['start_time'] && $slots[$j]['start_time'] < $slots[$i]['end_time'])
//                        || ($slots[$i]['start_time'] < $slots[$j]['end_time'] && $slots[$j]['end_time'] < $slots[$i]['end_time'])
//                        || ($slots[$j]['start_time'] < $slots[$i]['start_time'] && $slots[$i]['start_time'] < $slots[$j]['end_time'])
//                        || ($slots[$j]['start_time'] < $slots[$i]['end_time'] && $slots[$i]['end_time'] < $slots[$j]['end_time'])
//                    ) {
//                        return false;
//                    }
//                }
//            }

            return true;
        }

        public static function isFreeSlotsDate($slots): bool
        {
            $query = BookingSlot::query();

            $query->where(function ($q) use ($slots) {
                foreach ($slots as $slot) {
                    $q->orWhereBetween('start_time', [$slot['start_time'], $slot['end_time']]);
                    $q->orWhereBetween('end_time',[ $slot['start_time'], $slot['end_time']]);
                }
            });

            return !$query->exists();
        }
    }
