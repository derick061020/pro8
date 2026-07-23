
<article class="auth__image side">
    {{-- @if ($use_login_global && ($login->image ?? false))
        @if ($login->position_logo != 'none')
            <img class="auth__logo {{ $login->position_logo }}" src="{{ $login->image }}" alt="Logo" />
        @endif
    @else
        <img class="auth__logo {{ $login->position_logo }}" src="{{ asset('logo/tulogo.png') }}" alt="Logo" />
    @endif --}}

    <system-guest-register-plan-panel
        :plans="{{ json_encode($plans) }}"
        :plan-default="{{ json_encode($plan_default) }}"
    ></system-guest-register-plan-panel>

</article>
