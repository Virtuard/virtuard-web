<?php

namespace  Modules\Business;

use Modules\Core\Abstracts\BaseSettingsClass;
use Modules\Core\Models\Settings;

class SettingClass extends BaseSettingsClass
{
    public static function getSettingPages()
    {
        $configs = [
            'business' => [
                'id' => 'business',
                'title' => __("Business Settings"),
                'position' => 20,
                'view' => "Business::admin.settings.business",
                "keys" => [
                    'business_disable',
                    'business_page_search_title',
                    'business_page_search_banner',
                    'business_layout_search',
                    'business_location_search_style',
                    'business_page_limit_item',

                    'business_enable_review',
                    'business_review_approved',
                    'business_enable_review_after_booking',
                    'business_review_number_per_page',
                    'business_review_stats',

                    'business_page_list_seo_title',
                    'business_page_list_seo_desc',
                    'business_page_list_seo_image',
                    'business_page_list_seo_share',

                    'business_booking_buyer_fees',
                    'business_vendor_create_service_must_approved_by_admin',
                    'business_allow_vendor_can_change_their_booking_status',
                    'business_allow_vendor_can_change_paid_amount',
                    'business_allow_vendor_can_add_service_fee',
                    'business_search_fields',
                    'business_map_search_fields',

                    'business_allow_review_after_making_completed_booking',
                    'business_deposit_enable',
                    'business_deposit_type',
                    'business_deposit_amount',
                    'business_deposit_fomular',

                    'business_layout_map_option',
                    'business_icon_marker_map',
                    'business_booking_type',

                    'business_map_lat_default',
                    'business_map_lng_default',
                    'business_map_zoom_default',

                    'business_location_radius_value',
                    'business_location_radius_type',
                ],
                'html_keys' => [

                ],
                'filter_demo_mode' => [
                ]
            ]
        ];
        return apply_filters(Hook::BUSINESS_SETTING_CONFIG,$configs);
    }
}
