<?php

namespace Database\Seeders\Libs\Migration;

use App\Models\Tenant\BankAccount;
use App\Models\Tenant\Cash;
use App\Models\Tenant\Company;
use App\Models\Tenant\Dispatch;
use App\Models\Tenant\Document;
use App\Models\Tenant\DocumentPayment;
use App\Models\Tenant\Inventory as TenantInventory;
use App\Models\Tenant\Item;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\PurchaseItem;
use App\Models\Tenant\PurchasePayment;
use App\Models\Tenant\PurchaseSettlement;
use App\Models\Tenant\PurchaseSettlementPayment;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\SaleNotePayment;
use App\Models\Tenant\TransferAccountPayment;
use App\Models\Tenant\User;
use Modules\Expense\Models\BankLoanPayment;
use Modules\Expense\Models\ExpensePayment;
use Modules\Finance\Models\BankAccountIncome;
use Modules\Finance\Models\IncomePayment;
use Modules\Hotel\Models\HotelRentItemPayment;
use Modules\Inventory\Models\Devolution;
use Modules\Inventory\Models\Inventory;
use Modules\Order\Models\OrderNote;
use Modules\Pos\Models\CashTransaction;
use Modules\Sale\Models\ContractPayment;
use Modules\Sale\Models\QuotationPayment;
use Modules\Sale\Models\TechnicalServicePayment;

/**
 * Mapa de relaciones polimórficas detectadas en las migraciones del tenant.
 *
 * Cada `case` representa un par (tabla → nombre del morph). El valor del case
 * es el nombre del morph tal como se almacena en las columnas `*_type`/`*_id`.
 *
 * Los métodos auxiliares devuelven:
 *  - table():   la tabla que contiene las columnas polimórficas.
 *  - morph():   el nombre base del morph (prefijo de `_type` / `_id`).
 *  - models():  los FQCN de los modelos concretos que pueden guardarse en `*_type`.
 */
enum PolymorphicRelation: string
{
    case GLOBAL_PAYMENTS_DESTINATION       = 'global_payments.destination';
    case GLOBAL_PAYMENTS_PAYMENT           = 'global_payments.payment';
    case SYSTEM_ACTIVITY_LOGS_ORIGIN       = 'system_activity_logs.origin';
    case TRANSFER_ACCOUNT_PAYMENTS_ORIGIN  = 'transfer_account_payments.origin';
    case TRANSFER_ACCOUNT_PAYMENTS_DESTINY = 'transfer_account_payments.destiny';
    case INVENTORY_KARDEX                  = 'inventory_kardex.inventory_kardexable';
    case TIPS_ORIGIN                       = 'tips.origin';
    case ITEM_LOTS                         = 'item_lots.item_loteable';
    case PAYMENT_FILES_PAYMENT             = 'payment_files.payment';
    case PAYMENT_LINKS_PAYMENT             = 'payment_links.payment';
    case GUIDES_GUIDEABLE                  = 'guides.guideable';
    case WEIGHTED_AVERAGE_COSTS_ORIGIN     = 'weighted_average_costs.origin';
    case SUSCRIPTION_ORDERS_DOCUMENTABLE   = 'suscription_orders.documentable';

    public function table(): string
    {
        return explode('.', $this->value, 2)[0];
    }

    public function morph(): string
    {
        return explode('.', $this->value, 2)[1];
    }

    /**
     * @return array<int, string>
     */
    public function models(): array
    {
        return match ($this) {
            self::GLOBAL_PAYMENTS_DESTINATION => [
                "Modules\\Cash\\Models\\Cash" => Cash::class,
                "Modules\\Bank\\Models\\BankAccount" => BankAccount::class,
            ],

            self::GLOBAL_PAYMENTS_PAYMENT => [
                "Modules\\Document\\Models\\DocumentPayment" => DocumentPayment::class,
                "Modules\\SaleNote\\Models\\SaleNotePayment" => SaleNotePayment::class,
                "Modules\\Quotation\\Models\\QuotationPayment" => QuotationPayment::class,
                "Modules\\Sale\\Models\\ContractPayment" => ContractPayment::class,
                "Modules\\Purchase\\Models\\PurchasePayment" => PurchasePayment::class,
                "Modules\\Purchase\\Models\\PurchaseSettlementPayment" => PurchaseSettlementPayment::class,
                "Modules\\Expense\\Models\\ExpensePayment" => ExpensePayment::class,
                "Modules\\Expense\\Models\\BankLoanPayment" => BankLoanPayment::class,
                "Modules\\Finance\\Models\\IncomePayment" => IncomePayment::class,
                "Modules\\Finance\\Models\\BankAccountIncome" => BankAccountIncome::class,
                "Modules\\Pos\\Models\\CashTransaction" => CashTransaction::class,
                "Modules\\TechnicalService\\Models\\TechnicalServicePayment" => TechnicalServicePayment::class,
                "Modules\\Hotel\\Models\\HotelRentItemPayment" => HotelRentItemPayment::class,
                "App\\Models\\Tenant\\TransferAccountPayment" => TransferAccountPayment::class,
            ],

            self::SYSTEM_ACTIVITY_LOGS_ORIGIN => [
                "Modules\\User\\Models\\User" => User::class,
                "Modules\\Company\\Models\\Company" => Company::class,
            ],

            self::TRANSFER_ACCOUNT_PAYMENTS_ORIGIN,
            self::TRANSFER_ACCOUNT_PAYMENTS_DESTINY => [
                "Modules\\Cash\\Models\\Cash" => Cash::class,
                "Modules\\Bank\\Models\\BankAccount" => BankAccount::class,
            ],

            self::INVENTORY_KARDEX => [
                "Modules\\Document\\Models\\Document" => Document::class,
                "Modules\\Purchase\\Models\\Purchase" => Purchase::class,
                "Modules\\Purchase\\Models\\PurchaseSettlement" => PurchaseSettlement::class,
                "Modules\\SaleNote\\Models\\SaleNote" => SaleNote::class,
                "Modules\\OrderNote\\Models\\OrderNote" => OrderNote::class,
                "Modules\\Dispatch\\Models\\Dispatch" => Dispatch::class,
                "Modules\\Inventory\\Models\\Inventory" => Inventory::class,
                "Modules\\Inventory\\Models\\Devolution" => Devolution::class,
                "App\\Models\\Tenant\\Inventory" => TenantInventory::class,
            ],

            self::TIPS_ORIGIN => [
                "Modules\\Document\\Models\\Document" => Document::class,
                "Modules\\SaleNote\\Models\\SaleNote" => SaleNote::class,
            ],

            self::ITEM_LOTS => [
                "Modules\\Item\\Models\\Item" => Item::class,
                "Modules\\Inventory\\Models\\Inventory" => Inventory::class,
                "Modules\\Purchase\\Models\\PurchaseItem" => PurchaseItem::class,
            ],

            self::PAYMENT_FILES_PAYMENT => [
                "Modules\\Document\\Models\\DocumentPayment" => DocumentPayment::class,
                "Modules\\SaleNote\\Models\\SaleNotePayment" => SaleNotePayment::class,
                "Modules\\Quotation\\Models\\QuotationPayment" => QuotationPayment::class,
                "Modules\\Purchase\\Models\\PurchasePayment" => PurchasePayment::class,
                "Modules\\Expense\\Models\\ExpensePayment" => ExpensePayment::class,
            ],

            self::PAYMENT_LINKS_PAYMENT => [
                "Modules\\Document\\Models\\DocumentPayment" => DocumentPayment::class,
            ],

            // Columnas existentes en la tabla `guides` pero sin morphMany/morphOne declarado.
            self::GUIDES_GUIDEABLE => [],

            self::WEIGHTED_AVERAGE_COSTS_ORIGIN => [
                "Modules\\Item\\Models\\Item" => Item::class,
                "Modules\\Purchase\\Models\\PurchaseItem" => PurchaseItem::class,
            ],

            // `documentable_type`/`documentable_id` declarados en SuscriptionOrder (morphTo)
            // pero ningún modelo padre declara morphMany/morphOne con este alias.
            self::SUSCRIPTION_ORDERS_DOCUMENTABLE => [],
        };
    }

    /**
     * Devuelve el mapa completo (tabla.morph => [modelos]).
     *
     * @return array<string, array<int, string>>
     */
    public static function all(): array
    {
        $map = [];
        foreach (self::cases() as $case) {
            $map[$case->value] = $case->models();
        }
        return $map;
    }
}
