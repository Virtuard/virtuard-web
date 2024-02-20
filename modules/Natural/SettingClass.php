<?php

namespace  Modules\Natural;

use Modules\Core\Abstracts\BaseSettingsClass;
use Modules\Core\Models\Settings;

class SettingClass extends BaseSettingsClass
{
    public static function getSettingPages()
    {
        return [
            [
                'id'   => 'natural',
                'title' => __("Natural Settings"),
                'position'=>20,
                'view'=>"Natural::admin.settings.natural",
                "keys"=>[
                    'natural_disable',
                    'natural_page_search_title',
                    'natural_page_search_banner',
                    'natural_layout_search',
                    'natural_location_search_style',
                    'natural_page_limit_item',

                    'natural_enable_review',
                    'natural_review_approved',
                    'natural_enable_review_after_booking',
                    'natural_review_number_per_page',
                    'natural_review_stats',

                    'natural_page_list_seo_title',
                    'natural_page_list_seo_desc',
                    'natural_page_list_seo_image',
                    'natural_page_list_seo_share',

                    'natural_booking_buyer_fees',
                    'natural_vendor_create_service_must_approved_by_admin',
                    'natural_allow_vendor_can_change_their_booking_status',
                    'natural_allow_vendor_can_change_paid_amount',
                    'natural_allow_vendor_can_add_service_fee',
                    'natural_search_fields',
                    'natural_map_search_fields',

                    'natural_allow_review_after_making_completed_booking',
                    'natural_deposit_enable',
                    'natural_deposit_type',
                    'natural_deposit_amount',
                    'natural_deposit_fomular',

                    'natural_layout_map_option',

                    'natural_booking_type',
                    'natural_icon_marker_map',

                    'natural_map_lat_default',
                    'natural_map_lng_default',
                    'natural_map_zoom_default',

                    'natural_location_search_value',
                    'natural_location_search_style',
                ],
                'html_keys'=>[

                ]
            ]
        ];
    }
}
