<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 7/2/2019
 * Time: 10:26 AM
 */
namespace  Modules\Cultural;

use Modules\Core\Abstracts\BaseSettingsClass;

class SettingClass extends BaseSettingsClass
{
    public static function getSettingPages()
    {
        return [
            [
                'id'   => 'cultural',
                'title' => __("Cultural Settings"),
                'position'=>20,
                'view'=>"Cultural::admin.settings.cultural",
                "keys"=>[
                    'cultural_disable',
                    'cultural_page_search_title',
                    'cultural_page_search_banner',
                    'cultural_layout_search',
                    'cultural_location_search_style',
                    'cultural_page_limit_item',

                    'cultural_enable_review',
                    'cultural_review_approved',
                    'cultural_enable_review_after_booking',
                    'cultural_review_number_per_page',
                    'cultural_review_stats',
                    'cultural_page_list_seo_title',
                    'cultural_page_list_seo_desc',
                    'cultural_page_list_seo_image',
                    'cultural_page_list_seo_share',
                    'cultural_booking_buyer_fees',
                    'cultural_vendor_create_service_must_approved_by_admin',
                    'cultural_allow_vendor_can_change_their_booking_status',
                    'cultural_allow_vendor_can_change_paid_amount',
                    'cultural_allow_vendor_can_add_service_fee',
                    'cultural_search_fields',
                    'cultural_map_search_fields',

                    'cultural_allow_review_after_making_completed_booking',
                    'cultural_deposit_enable',
                    'cultural_deposit_type',
                    'cultural_deposit_amount',
                    'cultural_deposit_fomular',

                    'cultural_layout_map_option',
                    'cultural_icon_marker_map',

                    'cultural_map_lat_default',
                    'cultural_map_lng_default',
                    'cultural_map_zoom_default',

                    'cultural_location_radius_value',
                    'cultural_location_radius_type',
                ],
                'html_keys'=>[

                ]
            ]
        ];
    }
}
