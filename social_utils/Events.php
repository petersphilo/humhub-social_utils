<?php

/**
 * Peter Zieseniss
 * Copyright (C) 2022
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 */

namespace humhub\modules\social_utils;

use Yii;
use yii\helpers\Url;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\admin\widgets\AdminMenu;
use humhub\modules\admin\permissions\ManageModules;
use yii\db; 
use yii\db\Connection; 
use yii\db\Query; 
use yii\db\Command; 


class Events extends \yii\base\BaseObject{
	
	public static function onAdminMenuInit($event){
		
		if (!Yii::$app->user->can(ManageModules::class)) {
			return;
			}
		
		/** @var AdminMenu $menu */
		$menu = $event->sender;
		$menu->addEntry(new MenuLink([
			'label' => Yii::t('SocialUtilsModule.base', 'Social Utils'),
			'url' => Url::to(['/social_utils/main/index']),
			//'group' => 'manage',
			'icon' => 'ge',
			'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'social_utils' && Yii::$app->controller->id == 'admin'),
			'sortOrder' => 700,
			]));
		
		}
	
	public static function onDailyCronRun(){
		
		$TodaysDate=date("Y-m-d"); 
		}
	
	}




