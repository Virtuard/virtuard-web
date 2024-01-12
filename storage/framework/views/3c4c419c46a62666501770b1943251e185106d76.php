<?php
$menus = [
    'admin'=>[
        'url'   => route('admin.index'),
        'title' => __("Dashboard"),
        'icon'  => 'icon ion-ios-desktop',
        "position"=>0
    ],
    'menu'=>[
        "position"=>60,
        'url'        => route('core.admin.menu.index'),
        'title'      => __("Menu"),
        'icon'       => 'icon ion-ios-apps',
        'permission' => 'menu_view',
    ],
    'general'=>[
        "position"=>80,
        'url'        => route('core.admin.settings.index',['group'=>'general']),
        'title'      => __('Setting'),
        'icon'       => 'icon ion-ios-cog',
        'permission' => 'setting_update',
        'children'   => \Modules\Core\Models\Settings::getSettingPages(true)
    ],
    'tools'=>[
        "position"=>90,
        'url'      => route('core.admin.tool.index'),
        'title'    => __("Tools"),
        'icon'     => 'icon ion-ios-hammer',
        'children' => [
            'language'=>[
                'url'        => route('language.admin.index'),
                'title'      => __('Languages'),
                'icon'       => 'icon ion-ios-globe',
                'permission' => 'language_manage',
            ],
            'translation'=>[
                'url'        => route('language.admin.translations.index'),
                'title'      => __("Translation Manager"),
                'icon'       => 'icon ion-ios-globe',
                'permission' => 'language_translation',
            ],
            'logs'=>[
                'url'        => route('admin.logs'),
                'title'      => __("System Logs"),
                'icon'       => 'icon ion-ios-nuclear',
                'permission' => 'system_log_view',
            ],
        ]
    ],
];

// Modules
$custom_modules = \Modules\ServiceProvider::getActivatedModules();
if(!empty($custom_modules)){
    $custom_modules[] = [
        'id'=>'theme',
        'class'=>\Modules\Theme\ModuleProvider::class
    ];
    foreach($custom_modules as $moduleData){
        $module = $moduleData['id'];
        $moduleClass = $moduleData['class'];
        if(class_exists($moduleClass))
        {
            $menuConfig = call_user_func([$moduleClass,'getAdminMenu']);

            if(!empty($menuConfig)){
                $menus = array_merge($menus,$menuConfig);
            }

            $menuSubMenu = call_user_func([$moduleClass,'getAdminSubMenu']);

            if(!empty($menuSubMenu)){
                foreach($menuSubMenu as $k=>$submenu){
                    $submenu['id'] = $submenu['id'] ?? '_'.$k;

                    if(!empty($submenu['parent']) and isset($menus[$submenu['parent']])){
                        $menus[$submenu['parent']]['children'][$submenu['id']] = $submenu;
                        $menus[$submenu['parent']]['children'] = array_values(\Illuminate\Support\Arr::sort($menus[$submenu['parent']]['children'], function ($value) {
                            return $value['position'] ?? 100;
                        }));
                    }
                }

            }
        }

    }
}

// Plugins Menu
$plugins_modules = \Plugins\ServiceProvider::getModules();
if(!empty($plugins_modules)){
    foreach($plugins_modules as $module){
        $moduleClass = "\\Plugins\\".ucfirst($module)."\\ModuleProvider";
        if(class_exists($moduleClass))
        {
            $menuConfig = call_user_func([$moduleClass,'getAdminMenu']);
            if(!empty($menuConfig)){
                $menus = array_merge($menus,$menuConfig);
            }
            $menuSubMenu = call_user_func([$moduleClass,'getAdminSubMenu']);
            if(!empty($menuSubMenu)){
                foreach($menuSubMenu as $k=>$submenu){
                    $submenu['id'] = $submenu['id'] ?? '_'.$k;
                    if(!empty($submenu['parent']) and isset($menus[$submenu['parent']])){
                        $menus[$submenu['parent']]['children'][$submenu['id']] = $submenu;
                        $menus[$submenu['parent']]['children'] = array_values(\Illuminate\Support\Arr::sort($menus[$submenu['parent']]['children'], function ($value) {
                            return $value['position'] ?? 100;
                        }));
                    }
                }
            }
        }
    }
}

// Custom Menu
$custom_modules = \Custom\ServiceProvider::getModules();
if(!empty($custom_modules)){
    foreach($custom_modules as $module){
        $moduleClass = "\\Custom\\".ucfirst($module)."\\ModuleProvider";
        if(class_exists($moduleClass))
        {
            $menuConfig = call_user_func([$moduleClass,'getAdminMenu']);

            if(!empty($menuConfig)){
                $menus = array_merge($menus,$menuConfig);
            }

            $menuSubMenu = call_user_func([$moduleClass,'getAdminSubMenu']);

            if(!empty($menuSubMenu)){
                foreach($menuSubMenu as $k=>$submenu){
                    $submenu['id'] = $submenu['id'] ?? '_'.$k;
                    if(!empty($submenu['parent']) and isset($menus[$submenu['parent']])){
                        $menus[$submenu['parent']]['children'][$submenu['id']] = $submenu;
                        $menus[$submenu['parent']]['children'] = array_values(\Illuminate\Support\Arr::sort($menus[$submenu['parent']]['children'], function ($value) {
                            return $value['position'] ?? 100;
                        }));
                    }
                }

            }
        }

    }
}
$typeManager = app()->make(\Modules\Type\TypeManager::class);
$menuConfig = $typeManager->adminMenus();

$menus = array_merge($menus,$menuConfig);


$currentUrl = url(\Modules\Core\Walkers\MenuWalker::getActiveMenu());
$user = \Illuminate\Support\Facades\Auth::user();
if (!empty($menus)){
    foreach ($menus as $k => $menuItem) {

        if (!empty($menuItem['permission']) and !$user->hasPermission($menuItem['permission'])) {
            unset($menus[$k]);
            continue;
        }
        $menus[$k]['class'] = $currentUrl == url($menuItem['url']) ? 'active' : '';
        if (!empty($menuItem['children'])) {
            $menus[$k]['class'] .= ' has-children';
            foreach ($menuItem['children'] as $k2 => $menuItem2) {
                if (!empty($menuItem2['permission']) and !$user->hasPermission($menuItem2['permission'])) {
                    unset($menus[$k]['children'][$k2]);
                    continue;
                }
                $menus[$k]['children'][$k2]['class'] = $currentUrl == url($menuItem2['url']) ? 'active' : '';
            }
        }
    }

    //@todo Sort Menu by Position
    $menus = array_values(\Illuminate\Support\Arr::sort($menus, function ($value) {
        return $value['position'] ?? 100;
    }));

}
?>
<ul class="main-menu pb-5" style="background: #2b2b2b;">
    <li class="" position="9">
        <a href="/admin/virtuard-360">
            <span class="icon text-center"><i class="fa fa-camera"></i></span>
            Virtuard 360
        </a>
    </li>
    <?php
        $businessCategory = [
            "url" => "admin/add/category/product/business",
            "title" => "Add Category Business",
            "class" => ""
        ];

        $naturalCategory = [
            "url" => "admin/add/category/product/natural",
            "title" => "Add Category Natural",
            "class" => ""
        ];

        $propertyCategory = [
            "url" => "admin/add/category/product/property",
            "title" => "Add Category Property",
            "class" => ""
        ];

        $renderingArtCategory = [
            "url" => "admin/add/category/product/rendering-art",
            "title" => "Add Category Rendering",
            "class" => ""
        ];

        $AccomodationCategory = [
            "url" => "admin/add/category/product/accomodation",
            "title" => "Add Category Accomodation",
            "class" => ""
        ];

        $VehicleCategory = [
            "url" => "admin/add/category/product/vehicle",
            "title" => "Add Category Vehicles",
            "class" => ""
        ];

        $culturalCategory = [
            "url" => "admin/add/category/product/cultural",
            "title" => "Add Category Cultural",
            "class" => ""
        ];

        $menus[4]["children"][] = $businessCategory;
        $menus[5]["children"][] = $naturalCategory;
        $menus[6]["children"][] = $propertyCategory;
        $menus[7]["children"][] = $renderingArtCategory;
        $menus[8]["children"][] = $AccomodationCategory;
        $menus[9]["children"][] = $VehicleCategory;
        $menus[10]["children"][] = $culturalCategory;
    ?>
    <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $menuItem['class'] .= " ".str_ireplace("/","_",$menuItem['url']) ?>
        <li class="<?php echo e($menuItem['class']); ?>"><a href="<?php echo e(url($menuItem['url'])); ?>">
                <?php if(!empty($menuItem['icon'])): ?>
                    <?php
                        $iconMenu = $menuItem['icon'];

                        if($menuItem['icon'] === 'fa fa-building-o') {
                            $iconMenu = 'fa fa-shopping-bag';
                        }elseif($menuItem['icon'] === 'icon ion-md-umbrella') {
                            $iconMenu = 'fa fa-tree';
                        }elseif($menuItem['icon'] === 'ion ion-md-home') {
                            $iconMenu = 'fa fa-home';
                        }elseif($menuItem['icon'] === 'ion ion-md-airplane') {
                            $iconMenu = 'fa fa-laptop';
                        }elseif($menuItem['icon'] === 'ion-logo-model-s') {
                            $iconMenu = 'fa fa-industry';
                        }elseif($menuItem['icon'] === 'ion-ios-calendar') {
                            $iconMenu = 'fa fa-leaf';
                        }
                    ?>

                    <span class="icon text-center"><i class="<?php echo e($iconMenu); ?>"></i></span>
                <?php endif; ?>

                <?php
                $dataTitleName = $menuItem['title'];

                if($dataTitleName === 'Hotel') {
                    $dataTitleName = 'Business';
                }elseif($dataTitleName === 'Tour') {
                    $dataTitleName = 'Natural and Landscapes';
                }elseif($dataTitleName === 'Space') {
                    $dataTitleName = 'Property';
                }elseif($dataTitleName === 'Car') {
                    $dataTitleName = 'Accomodation';
                }elseif($dataTitleName === 'Event') {
                    $dataTitleName = 'Cultural Haritage and Public Works';
                }elseif($dataTitleName === 'Flight') {
                    $dataTitleName = 'Rendering and Art';
                }elseif($dataTitleName === 'Boat') {
                    $dataTitleName = 'Vehicles';
                }

                ?>

                <?php echo clean($dataTitleName,[
                    'Attr.AllowedClasses'=>null
                ]); ?>

            </a>
            <?php if(!empty($menuItem['children'])): ?>
                <span class="btn-toggle"><i class="fa fa-angle-left pull-right"></i></span>
                <ul class="children">
                    <?php $__currentLoopData = $menuItem['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuItem2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="<?php echo e($menuItem['class']); ?>"><a href="<?php echo e(url($menuItem2['url'])); ?>">
                                <?php if(!empty($menuItem2['icon'])): ?>
                                    <i class="<?php echo e($menuItem2['icon']); ?>"></i>
                                <?php endif; ?>
                                <?php echo clean($menuItem2['title'],[
                                    'Attr.AllowedClasses'=>null
                                ]); ?></a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
<?php /**PATH /home/buac2919/public_html/virtuard.buatpc.com/modules/Layout/admin/parts/sidebar.blade.php ENDPATH**/ ?>