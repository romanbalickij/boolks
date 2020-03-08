<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $reservations = Reservation::with('room', 'room.hotel')
            ->where('user_id', 1)
            ->orderBy('arrival', 'asc')
            ->get();
    return view('dashboard.reservations')->with('reservations', $reservations);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($hotel_id)
    {
        $hotelInfo = Hotel::with('rooms')->get()->find($hotel_id);
        return view('dashboard.reservationCreate', compact('hotelInfo'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->request->add(['user_id' => 1]);
        Reservation::create($request->all());

        return redirect('dashboard/reservations')->with('success', 'Reservation created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Reservation $reservation)
    {
        $reservation = Reservation::with('room', 'room.hotel')
            ->get()
            ->find($reservation->id);

        if ($reservation->user_id == 1) {
            $hotel_id = $reservation->room->hotel_id;
            $hotelInfo = Hotel::with('rooms')->get()->find($hotel_id);

            return view('dashboard.reservationSingle', compact('reservation', 'hotelInfo'));
        } else
            return redirect('dashboard/reservations')->with('error', 'You are not authorized to see that.');
    }

    public function edit(Reservation $reservation)
    {
        $reservation = Reservation::with('room', 'room.hotel')
            ->get()
            ->find($reservation->id);

        if ($reservation->user_id == 1) {
            $hotel_id = $reservation->room->hotel_id;
            $hotelInfo = Hotel::with('rooms')->get()->find($hotel_id);

            return view('dashboard.reservationEdit', compact('reservation', 'hotelInfo'));
        } else
            return redirect('dashboard/reservations')->with('error', 'You are not authorized to do that');
    }

    public function update(Request $request, Reservation $reservation)
    {
        $reservation->user_id = 1;
        $reservation->num_of_guests = $request->num_of_guests;
        $reservation->arrival = $request->arrival;
        $reservation->departure = $request->departure;
        $reservation->room_id = $request->room_id;
        $reservation->save();
        return redirect('dashboard/reservations')->with('success', 'Successfully updated your reservation!');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation = Reservation::find($reservation->id);
        $reservation->delete();

        return redirect('dashboard/reservations')->with('success', 'Successfully deleted your reservation!');
    }
}
