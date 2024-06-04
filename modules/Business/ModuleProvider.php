<?php
namespace Modules\Business;
use Modules\Core\Helpers\SitemapHelper;
use Modules\ModuleServiceProvider;
use Modules\Business\Models\Business;
use Modules\User\Helpers\PermissionHelper;

class ModuleProvider extends ModuleServiceProvider
{

    public function boot(SitemapHelper $sitemapHelper){

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        if(is_installed() and Business::isEnable()){
            $sitemapHelper->add("business",[app()->make(Business::class),'getForSitemap']);
        }

        PermissionHelper::add([
            // Business
            'business_view',
            'business_create',
            'business_update',
            'business_delete',
            'business_manage_others',
            'business_manage_attributes',
        ]);
    }
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouterServiceProvider::class);
    }

    public static function getAdminMenu()
    {
        if(!Business::isEnable()) return [];
        return [
            'business'=>[
                "position" => setMenuAdminPosition('business'),
                'url'        => route('business.admin.index'),
                'title'      => __('listing.business.manage'),
                'icon'       => 'fa fa-shopping-bag',
                'permission' => 'business_view',
                'children'   => [
                    'add'=>[
                        'url'        => route('business.admin.index'),
                        'title'      => __('All Businesss'),
                        'permission' => 'business_view',
                    ],
                    'create'=>[
                        'url'        => route('business.admin.create'),
                        'title'      => __('Add new Business'),
                        'permission' => 'business_create',
                    ],
                    // 'category'=>[
                    //     'url'        => route('business.admin.category.index'),
                    //     'title'      => __('Categories'),
                    //     'permission' => 'business_manage_others',
                    // ],
                    'attribute'=>[
                        'url'        => route('business.admin.attribute.index'),
                        'title'      => __('Attributes'),
                        'permission' => 'business_manage_attributes',
                    ],
                    'availability'=>[
                        'url'        => route('business.admin.availability.index'),
                        'title'      => __('Availability'),
                        'permission' => 'business_create',
                    ],
                    'recovery'=>[
                        'url'        => route('business.admin.recovery'),
                        'title'      => __('Recovery'),
                        'permission' => 'business_view',
                    ],

                ]
            ]
        ];
    }

    public static function getBookableServices()
    {
        if(!Business::isEnable()) return [];
        return [
            'business'=>Business::class
        ];
    }

    public static function getMenuBuilderTypes()
    {
        if(!Business::isEnable()) return [];
        return [
            'business'=>[
                'class' => Business::class,
                'name'  => __("Businesses"),
                'items' => Business::searchForMenu(),
                'position'=>41
            ]
        ];
    }

    public static function getUserMenu()
    {
        $res = [];
        if (Business::isEnable()) {
            $res['business'] = [
                'url'        => route('business.vendor.index'),
                'title'      => __('listing.business.manage'),
                'icon'       => Business::getServiceIconFeatured(),
                'position'   => 50,
                'permission' => 'business_view',
                'children'   => [
                    [
                        'url'   => route('business.vendor.index'),
                        'title' => __('listing.business.all'),
                    ],
                    [
                        'url'        => route('business.vendor.create'),
                        'title'      => __('listing.business.add'),
                        'permission' => 'business_create',
                    ],
                    [
                        'url'        => route('business.vendor.availability.index'),
                        'title'      => __("Availability"),
                        'permission' => 'business_create',
                    ],
                    [
                        'url'   => route('business.vendor.recovery'),
                        'title'      => __("Recovery"),
                        'permission' => 'business_create',
                    ],
                ]
            ];
        }
        return $res;
    }

    public static function getTemplateBlocks(){
        if(!Business::isEnable()) return [];
        return [
            'form_search_business'=>"\\Modules\\Business\\Blocks\\FormSearchBusiness",
            'list_business'=>"\\Modules\\Business\\Blocks\\ListBusiness",
            'business_term_featured_box'=>"\\Modules\\Business\\Blocks\\BusinessTermFeaturedBox",
        ];
    }
}
