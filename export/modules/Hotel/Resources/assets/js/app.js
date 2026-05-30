// Definir componente global de forma segura
window.HotelReservationCalendarComponent = {
    template: `<div>Cargando calendario...</div>`,
    data() {
        return {
            message: 'Componente Hotel'
        }
    }
};

// Registrar cuando Vue esté disponible
if (typeof Vue !== 'undefined') {
    Vue.component('hotel-reservation-calendar', window.HotelReservationCalendarComponent);
}
