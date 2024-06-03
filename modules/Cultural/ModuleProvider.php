<?php
namespace Modules\Cultural;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Helpers\SitemapHelper;
use Modules\ModuleServiceProvider;
use Modules\Cultural\Models\Cultural;
use Modules\User\Helpers\PermissionHelper;

class ModuleProvider extends ModuleServiceProvider
{
    public function boot(SitemapHelper $sitemapHelper)
    {
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        if(is_installed() and Cultural::isEnable()){
            $sitemapHelper->add("Cultural",[app()->make(Cultural::class),'getForSitemap']);
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

    public static function getBookableServices()
    {
        if(!Cultural::isEnable()) return [];
        return [
            'cultural' => Cultural::class,
        ];
    }

    public static function getAdminMenu()
    {
        $res = [];
        if(Cultural::isEnable()){
            $res['cultural'] = [
                "position"=>11,
                'url'        => route('cultural.admin.index'),
                'title'      => __('Cultural'),
                'icon'       => 'fa fa-tree',
                'permission' => 'cultural_view',
                'children'   => [
                    'cultural_view'=>[
                        'url'        => route('cultural.admin.index'),
                        'title'      => __('All Culturals'),
                        'permission' => 'cultural_view',
                    ],
                    'cultural_create'=>[
                        'url'        => route('cultural.admin.create'),
                        'title'      => __("Add Cultural"),
                        'permission' => 'cultural_create',
                    ],
                    'cultural_category'=>[
                        'url'        => route('cultural.admin.category.index'),
                        'title'      => __('Categories'),
                        'permission' => 'cultural_manage_others',
                    ],
                    'cultural_attribute'=>[
                        'url'        => route('cultural.admin.attribute.index'),
                        'title'      => __('Attributes'),
                        'permission' => 'cultural_manage_attributes',
                    ],
                    'cultural_availability'=>[
                        'url'        => route('cultural.admin.availability.index'),
                        'title'      => __('Availability'),
                        'permission' => 'cultural_create',
                    ],
                    'cultural_booking'=>[
                        'url'        => route('cultural.admin.booking.index'),
                        'title'      => __('Booking Calendar'),
                        'permission' => 'cultural_create',
                    ],
                    'recovery'=>[
                        'url'        => route('cultural.admin.recovery'),
                        'title'      => __('Recovery'),
                        'permission' => 'cultural_view',
                    ],
                ]
            ];
        }
        return $res;
    }


    public static function getUserMenu()
    {
        $res = [];
        if(Cultural::isEnable()){
            $res['cultural'] = [
                'url'   => route('cultural.vendor.index'),
                'title'      => __('Cultural'),
                'icon'       => Cultural::getServiceIconFeatured(),
                'permission' => 'cultural_view',
                'position'   => 40,
                'children'   => [
                    [
                        'url'   => route('cultural.vendor.index'),
                        'title' => __('All Culturals'),
                    ],
                    [
                        'url'        => route('cultural.vendor.create'),
                        'title'      => __('Add new Cultural'),
                        'permission' => 'cultural_create',
                    ],
                    [
                        'url'        => route('cultural.vendor.availability.index'),
                        'title'      => __("Availability"),
                        'permission' => 'cultural_create',
                    ],
                    [
                        'url'   => route('cultural.vendor.recovery'),
                        'title'      => __("Recovery"),
                        'permission' => 'cultural_create',
                    ],
                ]
            ];
        }
        return $res;
    }

    public static function getMenuBuilderTypes()
    {
        if(!Cultural::isEnable()) return [];

        return [
            [
                'class' => \Modules\Cultural\Models\Cultural::class,
                'name'  => __("Cultural"),
                'items' => \Modules\Cultural\Models\Cultural::searchForMenu(),
                'position'=>20
            ],
            [
                'class' => \Modules\Cultural\Models\CulturalCategory::class,
                'name'  => __('Cultural category'),
                'items' => \Modules\Cultural\Models\CulturalCategory::searchForMenu(),
                'position'=>30
            ],
        ];
    }

    public static function getTemplateBlocks(){
        if(!Cultural::isEnable()) return [];

        return [
            'list_culturals'=>"\\Modules\\Cultural\\Blocks\\ListCulturals",
            'form_search_cultural'=>"\\Modules\\Cultural\\Blocks\\FormSearchCultural",
            'box_category_cultural'=>"\\Modules\\Cultural\\Blocks\\BoxCategoryCultural",
        ];
    }
}
