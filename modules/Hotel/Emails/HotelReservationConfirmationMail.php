<?php

namespace Modules\Hotel\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Correo de confirmación de reserva enviado al huésped tras reservar en la web.
 *
 * Actúa como comprobante de la reserva: incluye el código, la habitación, las
 * fechas, los huéspedes y el total estimado.
 */
class HotelReservationConfirmationMail extends Mailable
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

        return $this->subject('Confirmación de tu reserva ' . $this->reservation['code'] . ' · ' . $hotelName)
                    ->from(config('mail.username'), $hotelName)
                    ->view('hotel::emails.reservation-confirmation');
    }
}
