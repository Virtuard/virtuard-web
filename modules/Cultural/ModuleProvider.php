<?php
namespace Modules\Cultural;
use Modules\Core\Helpers\SitemapHelper;
use Modules\Cultural\Models\Cultural;
use Modules\ModuleServiceProvider;
use Modules\News\Models\News;
use Modules\User\Helpers\PermissionHelper;

class ModuleProvider extends ModuleServiceProvider
{

    public function boot(SitemapHelper $sitemapHelper){

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        if(is_installed() and Cultural::isEnable()){

            $sitemapHelper->add("cultural",[app()->make(Cultural::class),'getForSitemap']);
        }
        PermissionHelper::add([
            // Cultural
            'cultural_view',
            'cultural_create',
            'cultural_update',
            'cultural_delete',
            'cultural_manage_others',
            'cultural_manage_attributes',
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
        if(!Cultural::isEnable()) return [];
        return [
            'cultural'=>[
                "position"=>12,
                'url'        => route('cultural.admin.index'),
                'title'      => __('listing.cultural.title'),
                'icon'       => 'fa fa-bank',
                'permission' => 'cultural_view',
                'children'   => [
                    'add'=>[
                        'url'        => route('cultural.admin.index'),
                        'title'      => __('All Culturals'),
                        'permission' => 'cultural_view',
                    ],
                    'create'=>[
                        'url'        => route('cultural.admin.create'),
                        'title'      => __('Add new Cultural'),
                        'permission' => 'cultural_create',
                    ],
                    'category'=>[
                        'url'        => route('cultural.admin.category.index'),
                        'title'      => __('Categories'),
                        'permission' => 'cultural_manage_others',
                    ],
                    'attribute'=>[
                        'url'        => route('cultural.admin.attribute.index'),
                        'title'      => __('Attributes'),
                        'permission' => 'cultural_manage_attributes',
                    ],
                    'availability'=>[
                        'url'        => route('cultural.admin.availability.index'),
                        'title'      => __('Availability'),
                        'permission' => 'cultural_create',
                    ],
                    'recovery'=>[
                        'url'        => route('cultural.admin.recovery'),
                        'title'      => __('Recovery'),
                        'permission' => 'cultural_view',
                    ],
                ]
            ]
        ];
    }

    public static function getBookableServices()
    {
        if(!Cultural::isEnable()) return [];
        return [
            'cultural'=>Cultural::class
        ];
    }

    public static function getMenuBuilderTypes()
    {
        if(!Cultural::isEnable()) return [];
        return [
            'cultural'=>[
                'class' => Cultural::class,
                'name'  => "Cultural",
                'items' => Cultural::searchForMenu(),
                'position'=>51
            ]
        ];
    }

    public static function getUserMenu()
    {
        if(!Cultural::isEnable()) return [];
        return [
            'cultural' => [
                'url'   => route('cultural.vendor.index'),
                'title'      => __('listing.cultural.sidebar_menu_title'),
                'icon'       => Cultural::getServiceIconFeatured(),
                'position'   => 80,
                'permission' => 'cultural_view',
                'children' => [
                    [
                        'url'   => route('cultural.vendor.index'),
                        'title'  => __('listing.cultural.all'),
                    ],
                    [
                        'url'   => route('cultural.vendor.create'),
                        'title'      => __('listing.cultural.add'),
                        'permission' => 'cultural_create',
                    ],
                    // 'availability'=>[
                    //     'url'        => route('cultural.vendor.availability.index'),
                    //     'title'      => __('Availability'),
                    //     'permission' => 'cultural_create',
                    // ],
                    // [
                    //     'url'   => route('cultural.vendor.recovery'),
                    //     'title'      => __("Recovery"),
                    //     'permission' => 'cultural_create',
                    // ],
                ]
            ],
        ];
    }

    public static function getTemplateBlocks(){
        if(!Cultural::isEnable()) return [];
        return [
            'form_search_cultural'=>"\\Modules\\Cultural\\Blocks\\FormSearchCultural",
            'list_cultural'=>"\\Modules\\Cultural\\Blocks\\ListCultural",
            'cultural_term_featured_box'=>"\\Modules\\Cultural\\Blocks\\CulturalTermFeaturedBox",
        ];
    }
}
