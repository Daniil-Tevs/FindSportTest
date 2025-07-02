<?php

    use Illuminate\Support\Facades\Route;

    use App\Http\Controllers\Api\BookingController;

    Route::controller(BookingController::class)->prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', 'index')->name('index'); // список бронирований пользователя
        Route::post('/', 'store')->name('store'); // создать бронирование с несколькими слотами
        Route::patch('/{booking}/slots/{slot}', 'update')->name('update'); // обновить конкретный слот
        Route::post('/{booking}/slots', 'add')->name('add'); // добавить новый слот к существующему заказу
        Route::delete('/{booking}', 'delete')->name('delete'); // удалить весь заказ
    });
