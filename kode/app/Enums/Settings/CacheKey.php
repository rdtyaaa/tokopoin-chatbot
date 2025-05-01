<?php

namespace App\Enums\Settings;

use App\Enums\EnumTrait;

enum CacheKey: string
{
    use EnumTrait;

    case SITE_SETTINGS          = "site_settings";
    case CURRENCIES             = "currencies";
    case DEFAULT_CURRENCY       = "default_currency";
    case CURRENCY               = "currency";
    case API_CURRENCY           = "api_currency";
    case SUBSCRIBER             = "subscriber";
    case MENU                   = "menu";
    case FAQ                    = "faq";
    case LANGUAGE               = "language";
    case FRONTEND               = "frontend";
    case TOP_CATEGORIES         = "top_categories";
    case TOP_BRANDS         = "top_brands";
    case FRONTEND_CATEGORIES         = "frontend_categories";
    case BANNERS                = "banners";
    case PAGES                  = "pages";
    case SELLER_NEW_DIGITAL_PRODUCT                  = "seller_new_digital_product_count";
    case SELLER_NEW_PHYSICAL_PRODUCT                  = "seller_new_physical_product_count";
    case PHYSICAL_ORDER_COUNT                        = "physical_order_count";
    case PHYSICAL_SELLER_ORDER_COUNT                        = "physical_seller_order_count";
    case WITHDRAW_PENDING_LOG_COUNT                        = "withdraw_pending_log_count";
    case RUNNING_TICKET                        = "running_ticket";
    case FRONTEND_NEW_PRODUCTS                        = "frontend_new_products";
    case FRONTEND_TODAYS_DEAL_PRODUCTS                        = "frontend_todays_deal_products";
    case FRONTEND_DIGITAL_PRODUCTS                        = "frontend_digital_products";
    case FRONTEND_BEST_SELLING_PRODUCTS                        = "frontend_best_selling_products";
    case FRONTEND_TOP_PRODUCTS                        = "frontend_top_products";
    case FRONTEND_BEST_SELLER                        = "frontend_best_products";
    case TESTIMONIAL                        = "testimonial";
    case MENU_CATEGORY                        = "menu_category";
    case ALL_CATEGORIES                        = "all_categories";
    case ALL_BRANDS                        = "all_brands";
    case PRODUCT_ATTRIBUTE                 = "product_attribute";



    
}