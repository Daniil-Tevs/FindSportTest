<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Response;

    use App\Http\Requests\Api\Booking\StoreRequest;
    use App\Http\Requests\Api\Booking\UpdateRequest;

    use App\Models\Booking;
    use App\Models\BookingSlot;

    use App\Services\BookingService;

    class BookingController extends Controller
    {
        public function index(Request $request)
        {
            $user = $request->user;
            return Response::json($user->bookings->load('slots'));
        }

        public function store(StoreRequest $request)
        {
            $data = $request->validated();
            $slots = array_map(fn($slot) => ['start_time' => Carbon::parse($slot['start_time']), 'end_time' => Carbon::parse($slot['end_time'])], $data['slots']);

            if(!BookingService::isValidSlotsDate($slots))
                return Response::json(['error' => 'Wrong date slots'], 422);

            if(!BookingService::isFreeSlotsDate($slots))
                return Response::json(['error' => 'Wrong date slots'], 422);

            $user = $request->user;
            $booking = $user->bookings()->create();

            $booking->slots()->createMany($slots);

            $booking->load('slots');

            return Response::json($booking);
        }

        public function update(UpdateRequest $request, Booking $booking, BookingSlot $slot)
        {

        }

        public function add(Request $request, Booking $booking)
        {

        }

        public function delete(Request $request, Booking $booking)
        {

        }
    }
