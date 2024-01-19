<?php

namespace  Modules\Art;

use Modules\Core\Abstracts\BaseSettingsClass;
use Modules\Core\Models\Settings;

class SettingClass extends BaseSettingsClass
{
    public static function getSettingPages()
    {
        return [
            [
                'id'   => 'art',
                'title' => __("Art Settings"),
                'position'=>20,
                'view'=>"Art::admin.settings.art",
                "keys"=>[
                    'art_disable',
                    'art_page_search_title',
                    'art_page_search_banner',
                    'art_layout_search',
                    'art_location_search_style',
                    'art_page_limit_item',

                    'art_enable_review',
                    'art_review_approved',
                    'art_enable_review_after_booking',
                    'art_review_number_per_page',
                    'art_review_stats',

                    'art_page_list_seo_title',
                    'art_page_list_seo_desc',
                    'art_page_list_seo_image',
                    'art_page_list_seo_share',

                    'art_booking_buyer_fees',
                    'art_vendor_create_service_must_approved_by_admin',
                    'art_allow_vendor_can_change_their_booking_status',
                    'art_allow_vendor_can_change_paid_amount',
                    'art_allow_vendor_can_add_service_fee',
                    'art_search_fields',
                    'art_map_search_fields',

                    'art_allow_review_after_making_completed_booking',
                    'art_deposit_enable',
                    'art_deposit_type',
                    'art_deposit_amount',
                    'art_deposit_fomular',

                    'art_layout_map_option',

                    'art_booking_type',
                    'art_icon_marker_map',

                    'art_map_lat_default',
                    'art_map_lng_default',
                    'art_map_zoom_default',

                    'art_location_search_value',
                    'art_location_search_style',
                ],
                'html_keys'=>[

                ]
            ]
        ];
    }
}
