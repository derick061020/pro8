#!/usr/bin/env bash
# Comandos para correr las migraciones NUEVAS del update, una por una, en orden.
# Generado el 2026-07-03. Ejecutalos revisando la salida de cada uno.
# Requiere: haber hecho backup de la BD de sistema y de cada tenant.
# APP_ENV=production -> los comandos ya llevan --force.

set -e   # detente si una migracion falla (quitalo si quieres continuar igual)

############################################################
# 1) MIGRACIONES DE SISTEMA (BD de sistema)  -- 27
############################################################
php artisan migrate --force --path=database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php
php artisan migrate --force --path=database/migrations/2023_11_14_114156_add_field_to_configurations.php
php artisan migrate --force --path=database/migrations/2024_08_14_095113_add_contaweb_url_access_enabled_to_clients.php
php artisan migrate --force --path=database/migrations/2024_08_14_131618_add_token_api_contaweb_to_configurations.php
php artisan migrate --force --path=database/migrations/2026_03_21_174315_tenant_change_qr_api_msg_to_configurations.php
php artisan migrate --force --path=database/migrations/2026_03_23_144143_add_credentials_izipay_to_configurations.php
php artisan migrate --force --path=database/migrations/2026_03_27_100000_add_reseller_fields_to_users_table.php
php artisan migrate --force --path=database/migrations/2026_03_27_120000_add_module_permissions_to_system_users_table.php
php artisan migrate --force --path=database/migrations/2026_03_31_000001_add_created_by_user_id_to_clients_table.php
php artisan migrate --force --path=database/migrations/2026_04_02_000001_create_reseller_admin_clients_table.php
php artisan migrate --force --path=database/migrations/2026_04_02_000002_add_can_create_clients_to_users_table.php
php artisan migrate --force --path=database/migrations/2026_04_02_120000_add_is_master_to_system_users_table.php
php artisan migrate --force --path=database/migrations/2026_04_03_145858_register_module_claims_book_to_modules.php
php artisan migrate --force --path=database/migrations/2026_04_14_170000_create_public_search_customizations_table.php
php artisan migrate --force --path=database/migrations/2026_04_14_190000_add_background_image_path_to_public_search_customizations_table.php
php artisan migrate --force --path=database/migrations/2026_04_17_161400_add_module_permissions_to_plans_table.php
php artisan migrate --force --path=database/migrations/2026_04_22_000001_add_module_levels_for_full_suscription.php
php artisan migrate --force --path=database/migrations/2026_04_28_000001_create_system_skins_table.php
php artisan migrate --force --path=database/migrations/2026_04_28_000002_add_custom_filename_to_system_skins_table.php
php artisan migrate --force --path=database/migrations/2026_04_28_000003_add_is_tenant_default_to_system_skins_table.php
php artisan migrate --force --path=database/migrations/2026_04_29_000001_add_is_forced_to_system_skins_table.php
php artisan migrate --force --path=database/migrations/2026_04_30_164222_add_uuid_to_payment_orders_table.php
php artisan migrate --force --path=database/migrations/2026_05_09_000001_add_is_visible_to_clients_to_system_skins_table.php
php artisan migrate --force --path=database/migrations/2026_05_11_000001_create_column_visibility_configs_table.php
php artisan migrate --force --path=database/migrations/2026_05_20_120000_add_plan_id_to_payment_orders_table.php
php artisan migrate --force --path=database/migrations/2026_05_20_130000_add_is_popular_to_plans_table.php
php artisan migrate --force --path=database/migrations/2026_05_26_111004_add_guest_register_plan_id_to_configurations_table.php

############################################################
# 2) MIGRACIONES DE TENANT (se aplican a TODOS los tenants) -- 77
#    Para un solo tenant agrega:  --website_id=ID
############################################################
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_05_123310_tenant_modify_name_length_in_items_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_08_093555_tenant_add_enable_weigth_in_dispatches_to_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_19_000000_insert_cat_periods_options.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_19_000002_add_unlimited_to_suscription_plans.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_19_000003_add_apply_in_period_to_item_rel_suscription_plans.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_19_000005_add_trial_fields_to_user_rel_suscription_plans.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_20_100455_tenant_add_list_price_to_person_types.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_21_150039_tenant_add_culqi_to_payment_configuration.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_22_114843_tenant_add_credentials_izipay_checkout_to_payment_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_23_154705_tenant_add_enabled_description_to_person_types.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_23_182449_add_pages_fields_to_configuration_ecommerce_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_26_173911_add_district_180107_san_antonio_to_districts_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_30_164701_create_discount_coupons_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_30_170000_create_discount_coupon_usages_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_03_30_170100_add_discount_fields_to_orders_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_03_150745_register_module_claims_book_to_modules.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_03_200000_create_status_claims_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_03_200001_create_claim_channels_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_03_200002_create_claims_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_07_000001_create_delivery_zones_and_seed_data.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_09_100000_update_claims_and_status_claims_tables.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_13_000000_add_closed_at_to_claims_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_14_160000_tenant_add_public_search_bg_color_to_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_14_200000_add_public_search_bg_image_path_to_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_15_000001_create_printers_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_15_000002_add_printer_config_to_restaurant_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_16_000001_add_printer_name_documents_to_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_16_234432_tenant_add_publicidad_to_configuration_ecommerce.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_17_000001_add_print_local_to_restaurant_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_17_100000_add_claims_widget_custom_url_to_companies_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_17_110000_add_claims_widget_custom_url_active_to_companies_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_20_000000_create_claim_settings_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_21_000001_add_description_to_claim_channels_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_21_180347_update_ticket_single_shipment_default_in_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_22_000001_add_printer_areas_enabled_to_restaurant_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_22_000001_tenant_add_auto_send_pdf_email_to_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_22_000001_tenant_create_suscription_orders_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_22_000002_tenant_create_suscription_payment_reminders_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_22_000003_tenant_add_message_notify_to_suscription_orders_to_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_22_000004_tenant_add_status_and_no_send_link_to_user_rel_suscription_plans.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_22_000005_tenant_add_module_levels_for_full_suscription.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_22_000006_tenant_add_orders_created_to_user_rel_suscription_plans.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_23_000001_add_enable_electronic_documents_to_configuration_ecommerce.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_24_000001_tenant_add_type_to_suscription_orders_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_24_000002_add_payment_methods_to_configuration_ecommerce.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_27_000001_add_printer_per_area_enabled_to_restaurant_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_28_000001_add_print_destination_to_restaurant_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_04_28_000002_tenant_add_is_system_to_skins_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_04_000001_add_show_item_discounts_charges_attributes_to_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120001_tenant_add_accounting_number_to_bank_accounts.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120002_tenant_add_is_sunat_to_cat_document_types.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120003_tenant_add_accounting_fields_to_categories.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120004_tenant_add_pse_qr_support_to_companies.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120005_tenant_add_flags_to_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120006_tenant_add_quantity_factor_to_document_items.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120007_tenant_add_subscription_and_collect_api_to_documents.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120008_tenant_add_quantity_factor_to_order_note_items.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120009_tenant_add_doc_type_series_number_to_order_notes.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120010_tenant_add_text_filter_to_persons.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120011_tenant_add_update_purchase_price_to_purchase_items.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120012_tenant_add_fields_to_purchase_order_items.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120013_tenant_add_doc_type_series_to_purchase_orders.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120014_tenant_add_soap_shipping_response_to_purchase_settlements.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120015_tenant_add_fields_to_purchases.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120016_tenant_add_quantity_factor_to_quotation_items.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120017_tenant_add_doc_type_series_to_quotations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120018_tenant_add_quantity_factor_to_sale_note_items.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120019_tenant_add_fields_to_sale_notes.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_07_120020_tenant_add_permissions_to_users.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_19_120000_tenant_add_global_igv_handling_to_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_19_175432_tenant_active_by_default_exact_discount_to_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_20_120000_tenant_create_item_form_layouts_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_26_161340_add_show_unify_amount_items_to_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_27_000000_tenant_add_date_time_format_to_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_05_28_120000_add_is_default_to_price_labels_table.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_06_04_104234_tenant_add_enabled_guarantee_fund_to_configurations.php
php artisan tenancy:migrate --force --path=database/migrations/tenant/2026_06_19_174950_tenant_create_ejb_report_configurations_table.php
