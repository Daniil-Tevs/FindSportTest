<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Services\SlotService;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Support\Facades\Response;

    use App\Http\Requests\Api\Booking\StoreRequest;
    use App\Http\Requests\Api\Booking\UpdateRequest;

    use App\Models\Booking;
    use App\Models\BookingSlot;

    use App\Services\BookingService;

    class BookingController extends Controller
    {
        public function index(Request $request): JsonResponse
        {
            $user = $request->user;
            return Response::json($user->bookings->load('slots'));
        }

        public function store(StoreRequest $request): JsonResponse
        {
            $data = $request->validated();
            $slots = array_map(fn($slot) => ['start_time' => Carbon::parse($slot['start_time']), 'end_time' => Carbon::parse($slot['end_time'])], $data['slots']);

            if (!BookingService::isValidSlotsDate($slots))
                return Response::json(['error' => "Slot's dates are wrong"], 422);

            if (!BookingService::isFreeSlotsDate($slots))
                return Response::json(['error' => 'Choosed dates for slots are not free'], 422);

            $user = $request->user;
            $booking = $user->bookings()->create();

            $booking->slots()->createMany($slots);

            $booking->load('slots');

            return Response::json($booking);
        }

        public function update(UpdateRequest $request, Booking $booking, BookingSlot $slot): JsonResponse
        {
            $data = SlotService::formatData($request->validated());

            if ($error = SlotService::isNotValid($data, $slot))
                return Response::json(['error' => $error], 422);

            if (!$slot->update($data))
                return Response::json(['error' => 'Slot is not updated'], 422);

            return Response::json($slot);
        }

        public function add(UpdateRequest $request, Booking $booking): JsonResponse
        {
            $data = SlotService::formatData($request->validated());

            if ($error = SlotService::isNotValid($data))
                return Response::json(['error' => $error], 422);

            if (!$booking->slots()->create($data))
                return Response::json(['error' => 'Slot is not added'], 422);

            $booking->load('slots');

            return Response::json($booking);
        }

        public function delete(Request $request, Booking $booking): JsonResponse
        {
            if (!$booking->delete())
                return Response::json(['error' => 'Booking is not deleted'], 422);

            return Response::json(null, 204);
        }
    }
