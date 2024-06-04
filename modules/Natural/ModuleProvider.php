<?php
namespace Modules\Natural;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Helpers\SitemapHelper;
use Modules\ModuleServiceProvider;
use Modules\Natural\Models\Natural;
use Modules\User\Helpers\PermissionHelper;

class ModuleProvider extends ModuleServiceProvider
{
    public function boot(SitemapHelper $sitemapHelper)
    {
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        if(is_installed() and Natural::isEnable()){
            $sitemapHelper->add("natural",[app()->make(Natural::class),'getForSitemap']);
        }

        PermissionHelper::add([
            // Natural
            'natural_view',
            'natural_create',
            'natural_update',
            'natural_delete',
            'natural_manage_others',
            'natural_manage_attributes',
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

    public static function getBookableServices()
    {
        if(!Natural::isEnable()) return [];
        return [
            'natural' => Natural::class,
        ];
    }

    public static function getAdminMenu()
    {
        $res = [];
        if(Natural::isEnable()){
            $res['natural'] = [
                "position" => setMenuAdminPosition('natural'),
                'url'        => route('natural.admin.index'),
                'title'      => __('Manage Natural'),
                'icon'       => 'fa fa-image',
                'permission' => 'natural_view',
                'children'   => [
                    'natural_view'=>[
                        'url'        => route('natural.admin.index'),
                        'title'      => __('All Naturals'),
                        'permission' => 'natural_view',
                    ],
                    'natural_create'=>[
                        'url'        => route('natural.admin.create'),
                        'title'      => __("Add Natural"),
                        'permission' => 'natural_create',
                    ],
                    // 'natural_category'=>[
                    //     'url'        => route('natural.admin.category.index'),
                    //     'title'      => __('Categories'),
                    //     'permission' => 'natural_manage_others',
                    // ],
                    'natural_attribute'=>[
                        'url'        => route('natural.admin.attribute.index'),
                        'title'      => __('Attributes'),
                        'permission' => 'natural_manage_attributes',
                    ],
                    'natural_availability'=>[
                        'url'        => route('natural.admin.availability.index'),
                        'title'      => __('Availability'),
                        'permission' => 'natural_create',
                    ],
                    'natural_booking'=>[
                        'url'        => route('natural.admin.booking.index'),
                        'title'      => __('Booking Calendar'),
                        'permission' => 'natural_create',
                    ],
                    'recovery'=>[
                        'url'        => route('natural.admin.recovery'),
                        'title'      => __('Recovery'),
                        'permission' => 'natural_view',
                    ],
                ]
            ];
        }
        return $res;
    }


    public static function getUserMenu()
    {
        $res = [];
        if(Natural::isEnable()){
            $res['natural'] = [
                'url'   => route('natural.vendor.index'),
                'title'      => __('Manage Natural'),
                'icon'       => Natural::getServiceIconFeatured(),
                'permission' => 'natural_view',
                'position'   => 40,
                'children'   => [
                    [
                        'url'   => route('natural.vendor.index'),
                        'title' => __('All Naturals'),
                    ],
                    [
                        'url'        => route('natural.vendor.create'),
                        'title'      => __('Add new Natural'),
                        'permission' => 'natural_create',
                    ],
                    [
                        'url'        => route('natural.vendor.availability.index'),
                        'title'      => __("Availability"),
                        'permission' => 'natural_create',
                    ],
                    [
                        'url'   => route('natural.vendor.recovery'),
                        'title'      => __("Recovery"),
                        'permission' => 'natural_create',
                    ],
                ]
            ];
        }
        return $res;
    }

    public static function getMenuBuilderTypes()
    {
        if(!Natural::isEnable()) return [];

        return [
            [
                'class' => \Modules\Natural\Models\Natural::class,
                'name'  => __("Natural"),
                'items' => \Modules\Natural\Models\Natural::searchForMenu(),
                'position'=>20
            ],
            [
                'class' => \Modules\Natural\Models\NaturalCategory::class,
                'name'  => __('Natural category'),
                'items' => \Modules\Natural\Models\NaturalCategory::searchForMenu(),
                'position'=>30
            ],
        ];
    }

    public static function getTemplateBlocks(){
        if(!Natural::isEnable()) return [];

        return [
            'list_naturals'=>"\\Modules\\Natural\\Blocks\\ListNaturals",
            'form_search_natural'=>"\\Modules\\Natural\\Blocks\\FormSearchNatural",
            'box_category_natural'=>"\\Modules\\Natural\\Blocks\\BoxCategoryNatural",
        ];
    }
}
