<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreateController extends Controller
{
    public function index()
    {
        $menus = [];
        $modules = \Modules\ServiceProvider::getModules();
        if (!empty($modules)) {
            foreach ($modules as $module) {
                $moduleClass = '\\Modules\\' . ucfirst($module) . '\\ModuleProvider';
                if (class_exists($moduleClass)) {
                    $menuConfig = call_user_func([$moduleClass, 'getUserMenu']);
                    if (!empty($menuConfig)) {
                        $menus = array_merge($menus, $menuConfig);
                    }
                    $menuSubMenu = call_user_func([$moduleClass, 'getUserSubMenu']);
                    if (!empty($menuSubMenu)) {
                        foreach ($menuSubMenu as $k => $submenu) {
                            $submenu['id'] = $submenu['id'] ?? '_' . $k;
                            if (!empty($submenu['parent']) and isset($menus[$submenu['parent']])) {
                                $menus[$submenu['parent']]['children'][$submenu['id']] = $submenu;
                                $menus[$submenu['parent']]['children'] = array_values(
                                    \Illuminate\Support\Arr::sort($menus[$submenu['parent']]['children'], function ($value) {
                                        return $value['position'] ?? 100;
                                    }),
                                );
                            }
                        }
                    }
                }
            }
        }

        // check menu listing
        foreach ($menus as $key => $menu) {
            $menus[$key]['id'] = $key;
            unset($menus[$key]['children']);

            if (!in_array($key, menu_listing())) {
                unset($menus[$key]);
            } else {
                $menus[$key]['position'] = setMenuPosition($menus[$key]);
                
                if ( auth()->check() && auth()->user()->role->name == 'administrator') {
                    $menus[$key]['url'] = route($key . '.admin.create');
                } else {
                    $menus[$key]['url'] = route($key . '.vendor.create');
                }
            }
        }

        usort($menus, function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        $dataView = [
            'menus' => $menus,
        ];

        return view('create.index', $dataView);
    }
}
