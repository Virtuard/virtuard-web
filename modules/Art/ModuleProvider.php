<?php
namespace Modules\Art;
use Modules\Core\Helpers\SitemapHelper;
use Modules\Art\Models\Art;
use Modules\ModuleServiceProvider;
use Modules\News\Models\News;
use Modules\User\Helpers\PermissionHelper;

class ModuleProvider extends ModuleServiceProvider
{

    public function boot(SitemapHelper $sitemapHelper){

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        if(is_installed() and Art::isEnable()){

            $sitemapHelper->add("art",[app()->make(Art::class),'getForSitemap']);
        }
        PermissionHelper::add([
            // Art
            'art_view',
            'art_create',
            'art_update',
            'art_delete',
            'art_manage_others',
            'art_manage_attributes',
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
        if(!Art::isEnable()) return [];
        return [
            'art'=>[
                "position"=>13,
                'url'        => route('art.admin.index'),
                'title'      => __('listing.art.title'),
                'icon'       => 'fa fa-laptop',
                'permission' => 'art_view',
                'children'   => [
                    'add'=>[
                        'url'        => route('art.admin.index'),
                        'title'      => __('All Arts'),
                        'permission' => 'art_view',
                    ],
                    'create'=>[
                        'url'        => route('art.admin.create'),
                        'title'      => __('Add new Art'),
                        'permission' => 'art_create',
                    ],
                    'category'=>[
                        'url'        => route('art.admin.category.index'),
                        'title'      => __('Categories'),
                        'permission' => 'art_manage_others',
                    ],
                    'attribute'=>[
                        'url'        => route('art.admin.attribute.index'),
                        'title'      => __('Attributes'),
                        'permission' => 'art_manage_attributes',
                    ],
                    'availability'=>[
                        'url'        => route('art.admin.availability.index'),
                        'title'      => __('Availability'),
                        'permission' => 'art_create',
                    ],
                    'recovery'=>[
                        'url'        => route('art.admin.recovery'),
                        'title'      => __('Recovery'),
                        'permission' => 'art_view',
                    ],
                ]
            ]
        ];
    }

    public static function getBookableServices()
    {
        if(!Art::isEnable()) return [];
        return [
            'art'=>Art::class
        ];
    }

    public static function getMenuBuilderTypes()
    {
        if(!Art::isEnable()) return [];
        return [
            'art'=>[
                'class' => Art::class,
                'name'  => __("Art"),
                'items' => Art::searchForMenu(),
                'position'=>51
            ]
        ];
    }

    public static function getUserMenu()
    {
        if(!Art::isEnable()) return [];
        return [
            'art' => [
                'url'   => route('art.vendor.index'),
                'title'      => __('listing.art.title'),
                'icon'       => Art::getServiceIconFeatured(),
                'position'   => 80,
                'permission' => 'art_view',
                'children' => [
                    [
                        'url'   => route('art.vendor.index'),
                        'title'  => __('listing.art.all'),
                    ],
                    [
                        'url'   => route('art.vendor.create'),
                        'title'      => __('listing.art.add'),
                        'permission' => 'art_create',
                    ],
                    'availability'=>[
                        'url'        => route('art.vendor.availability.index'),
                        'title'      => __('Availability'),
                        'permission' => 'art_create',
                    ],
                    [
                        'url'   => route('art.vendor.recovery'),
                        'title'      => __("Recovery"),
                        'permission' => 'art_create',
                    ],
                ]
            ],
        ];
    }

    public static function getTemplateBlocks(){
        if(!Art::isEnable()) return [];
        return [
            'form_search_art'=>"\\Modules\\Art\\Blocks\\FormSearchArt",
            'list_art'=>"\\Modules\\Art\\Blocks\\ListArt",
            'art_term_featured_box'=>"\\Modules\\Art\\Blocks\\ArtTermFeaturedBox",
        ];
    }
}
