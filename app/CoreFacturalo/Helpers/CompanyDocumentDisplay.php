<?php

namespace App\CoreFacturalo\Helpers;

/**
 * Nombre comercial vs razón social en PDFs/tickets (objetos con name y trade_name).
 */
class CompanyDocumentDisplay
{
    public static function normalized(string $value): string
    {
        return mb_strtolower(trim($value));
    }

    public static function commercialLine(object $company): string
    {
        $legal = trim((string) ($company->name ?? ''));
        $trade = trim((string) ($company->trade_name ?? ''));

        return $trade !== '' ? $trade : $legal;
    }

    public static function legalLine(object $company): ?string
    {
        if (self::namesAreSame($company)) {
            return null;
        }

        $legal = trim((string) ($company->name ?? ''));

        return $legal !== '' ? $legal : null;
    }

    public static function namesAreSame(object $company): bool
    {
        $legal = trim((string) ($company->name ?? ''));
        $trade = trim((string) ($company->trade_name ?? ''));

        if ($trade === '') {
            return true;
        }

        return self::normalized($legal) === self::normalized($trade);
    }

    public static function logoAlt(object $company): string
    {
        return self::commercialLine($company);
    }
}
