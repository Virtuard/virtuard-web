<?php
namespace Modules\Natural;
use Modules\Core\Helpers\SitemapHelper;
use Modules\Natural\Models\Natural;
use Modules\ModuleServiceProvider;
use Modules\News\Models\News;
use Modules\User\Helpers\PermissionHelper;

class ModuleProvider extends ModuleServiceProvider
{

    public function boot(SitemapHelper $sitemapHelper){

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

    public static function getAdminMenu()
    {
        if(!Natural::isEnable()) return [];
        return [
            'natural'=>[
                "position"=>12,
                'url'        => route('natural.admin.index'),
                'title'      => __('listing.natural.title'),
                'icon'       => 'fa fa-image',
                'permission' => 'natural_view',
                'children'   => [
                    'add'=>[
                        'url'        => route('natural.admin.index'),
                        'title'      => __('All Naturals'),
                        'permission' => 'natural_view',
                    ],
                    'create'=>[
                        'url'        => route('natural.admin.create'),
                        'title'      => __('Add new Natural'),
                        'permission' => 'natural_create',
                    ],
                    'category'=>[
                        'url'        => route('natural.admin.category.index'),
                        'title'      => __('Categories'),
                        'permission' => 'natural_manage_others',
                    ],
                    'attribute'=>[
                        'url'        => route('natural.admin.attribute.index'),
                        'title'      => __('Attributes'),
                        'permission' => 'natural_manage_attributes',
                    ],
                    'availability'=>[
                        'url'        => route('natural.admin.availability.index'),
                        'title'      => __('Availability'),
                        'permission' => 'natural_create',
                    ],
                    'recovery'=>[
                        'url'        => route('natural.admin.recovery'),
                        'title'      => __('Recovery'),
                        'permission' => 'natural_view',
                    ],
                ]
            ]
        ];
    }

    public static function getBookableServices()
    {
        if(!Natural::isEnable()) return [];
        return [
            'natural'=>Natural::class
        ];
    }

    public static function getMenuBuilderTypes()
    {
        if(!Natural::isEnable()) return [];
        return [
            'natural'=>[
                'class' => Natural::class,
                'name'  => "Natural",
                'items' => Natural::searchForMenu(),
                'position'=>51
            ]
        ];
    }

    public static function getUserMenu()
    {
        if(!Natural::isEnable()) return [];
        return [
            'natural' => [
                'url'   => route('natural.vendor.index'),
                'title'      => __('listing.natural.title'),
                'icon'       => Natural::getServiceIconFeatured(),
                'position'   => 80,
                'permission' => 'natural_view',
                'children' => [
                    [
                        'url'   => route('natural.vendor.index'),
                        'title'  => __('listing.natural.all'),
                    ],
                    [
                        'url'   => route('natural.vendor.create'),
                        'title'      => __('listing.natural.add'),
                        'permission' => 'natural_create',
                    ],
                    // 'availability'=>[
                    //     'url'        => route('natural.vendor.availability.index'),
                    //     'title'      => __('Availability'),
                    //     'permission' => 'natural_create',
                    // ],
                    // [
                    //     'url'   => route('natural.vendor.recovery'),
                    //     'title'      => __("Recovery"),
                    //     'permission' => 'natural_create',
                    // ],
                ]
            ],
        ];
    }

    public static function getTemplateBlocks(){
        if(!Natural::isEnable()) return [];
        return [
            'form_search_natural'=>"\\Modules\\Natural\\Blocks\\FormSearchNatural",
            'list_natural'=>"\\Modules\\Natural\\Blocks\\ListNatural",
            'natural_term_featured_box'=>"\\Modules\\Natural\\Blocks\\NaturalTermFeaturedBox",
        ];
    }
}
