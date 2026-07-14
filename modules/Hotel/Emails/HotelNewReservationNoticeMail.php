<?php

namespace Modules\Hotel\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Aviso interno enviado al hotel (correo del establecimiento) cuando entra una
 * nueva reserva desde la web pública.
 */
class HotelNewReservationNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array Datos de la reserva ya formateados para la vista. */
    public $reservation;

    /** @var array Datos de branding/contacto del hotel. */
    public $hotel;

    public function __construct(array $reservation, array $hotel)
    {
        $this->reservation = $reservation;
        $this->hotel       = $hotel;
    }

    public function build()
    {
        $hotelName = $this->hotel['name'] ?? 'Hotel';

        return $this->subject('Nueva reserva web ' . $this->reservation['code'] . ' · ' . $this->reservation['room'])
                    ->from(config('mail.username'), $hotelName)
                    ->view('hotel::emails.reservation-notice');
    }
}
