<?php

/**
 * Peter Zieseniss
 * Copyright (C) 2022
 * 
 * Please consider making a donation using the button found on the main page of this module; it would help me greatly..
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 */

namespace humhub\modules\social_utils\controllers;

use Yii;
use yii\console\Controller;
use yii\web\Request;

use yii\db; 
use yii\db\Query; 
use yii\db\Command; 
/*
use humhub\modules\admin\permissions\ManageModules; 
if ((!Yii::$app->user->can(ManageModules::class))||($_SESSION['social_utils_sesh']!='MySocialUtilsSesh')) {
	return 'This is not the page you are looking for..'; 
	}
*/

class MainController extends \humhub\modules\admin\components\Controller{

	public function behaviors(){
		return [
			'acl' => [
				'class' => \humhub\components\behaviors\AccessControl::className(),
				'adminOnly' => true
				]
			];
		}
	
	
	public function actionIndex(){
		if(Yii::$app->request->post()||Yii::$app->request->get('InviteMembers')){$this->MyDataRequest(); }
		else{return $this->render('index'); }
		}
	
	
	public function MyDataRequest(){
		
		if(Yii::$app->request->post('ListAllSpaces')=='Yes'){
			$MyOptionPreOne="<option value='"; 
			$MyOptionPreTwo="'>"; 
			$MyOptionPost="</option>"; 
			$MySpacesOptionFull=''; 
			
			$MySpacesOptionFull=$MyOptionPreOne."' selected>Select a Space".$MyOptionPost; 
			$ListAllSpaces_cmd=Yii::$app->db->createCommand("SELECT id,name 
				FROM space;")->queryAll(); 
			foreach($ListAllSpaces_cmd as $ListAllSpaces_row){
				$MySpacesOptionFull.=$MyOptionPreOne.$ListAllSpaces_row['id'].$MyOptionPreTwo.$ListAllSpaces_row['name'].$MyOptionPost; 
				}
			echo $MySpacesOptionFull; 
			exit;
			}
		
		elseif(Yii::$app->request->post('ListAllGroups')=='Yes'){
			$MyOptionPreOne="<option value='"; 
			$MyOptionPreTwo="'>"; 
			$MyOptionPost="</option>"; 
			$MyGroupsOptionFull=''; 
			
			$MyGroupsOptionFull=$MyOptionPreOne."' selected>Select a Group".$MyOptionPost; 
			$ListAllGroup_cmd=Yii::$app->db->createCommand("SELECT id,name 
				FROM `group` 
				WHERE ((name != 'Administrators')AND(name != 'Users'));")->queryAll(); 
			foreach($ListAllGroup_cmd as $ListAllGroup_row){
				$MyGroupsOptionFull.=$MyOptionPreOne.$ListAllGroup_row['id'].$MyOptionPreTwo.$ListAllGroup_row['name'].$MyOptionPost; 
				}
			echo $MyGroupsOptionFull; 
			exit;
			}
		
		elseif(Yii::$app->request->post('GetGroupSpaceMembers')=='Yes'){
			$TheSpacesSelectVal=Yii::$app->request->post('TheSpacesSelectVal'); 
			$TheGroupSelectVal=Yii::$app->request->post('TheGroupSelectVal'); 
			
			$jsonResponse['GroupPopulation']=Yii::$app->db->createCommand("SELECT COUNT(group_user.user_id) 
				FROM group_user
				WHERE group_user.group_id = $TheGroupSelectVal;")->queryScalar(); 
			
			$jsonResponse['NotYetMembers']=Yii::$app->db->createCommand("SELECT COUNT(group_user.user_id) 
				FROM group_user
				WHERE group_user.group_id = $TheGroupSelectVal 
					AND NOT EXISTS(
						SELECT null FROM space_membership 
						WHERE space_membership.user_id = group_user.user_id 
						AND space_membership.space_id = $TheSpacesSelectVal
						);")->queryScalar(); 
			
			$jsonResponse['GroupSpaceMembers']=Yii::$app->db->createCommand("SELECT COUNT(space_membership.user_id) 
				FROM space_membership 
				JOIN (
					SELECT group_user.user_id, group_user.group_id FROM group_user WHERE group_user.group_id = $TheGroupSelectVal
					) AS joinGroupUsers ON space_membership.user_id = joinGroupUsers.user_id 
				WHERE 
					space_membership.space_id = $TheSpacesSelectVal 
					AND joinGroupUsers.group_id = $TheGroupSelectVal 
					AND space_membership.status = 3;")->queryScalar(); 
			
			$jsonResponse['GroupSpaceInvites']=Yii::$app->db->createCommand("SELECT COUNT(space_membership.user_id) 
				FROM space_membership 
				JOIN (
					SELECT group_user.user_id, group_user.group_id FROM group_user WHERE group_user.group_id = $TheGroupSelectVal
					) AS joinGroupUsers ON space_membership.user_id = joinGroupUsers.user_id 
				WHERE 
					space_membership.space_id = $TheSpacesSelectVal 
					AND joinGroupUsers.group_id = $TheGroupSelectVal 
					AND space_membership.status = 1;")->queryScalar(); 
			
			$jsonResponse['GroupSpaceTotal']=Yii::$app->db->createCommand("SELECT COUNT(space_membership.user_id) 
				FROM space_membership 
				JOIN (
					SELECT group_user.user_id, group_user.group_id FROM group_user WHERE group_user.group_id = $TheGroupSelectVal
					) AS joinGroupUsers ON space_membership.user_id = joinGroupUsers.user_id 
				WHERE 
					space_membership.space_id = $TheSpacesSelectVal 
					AND joinGroupUsers.group_id = $TheGroupSelectVal ;")->queryScalar(); 
			echo json_encode($jsonResponse,JSON_FORCE_OBJECT);  
			exit;
			}
		/**/
		elseif(Yii::$app->request->post('InviteOrAddMembers')=='Yes'){
			$SpaceID=Yii::$app->request->post('TheSpacesSelectVal'); 
			$GroupID=Yii::$app->request->post('TheGroupSelectVal'); 
			$TheAction=Yii::$app->request->post('IoM'); 
			$UsersAffected=0; 
			if($TheAction==3){$ActionTaken='added as Members'; }
			elseif($TheAction==1){$ActionTaken='Invited'; }
			else{die('Error!! - TheAction'); }; 
			
			$TodaysDate=date("Y-m-d H:i:s"); 
			
			$CheckSpaceMembership_cmd=Yii::$app->db->createCommand("SELECT COUNT(user_id) 
				FROM space_membership 
				WHERE (user_id=:UID AND space_id=$SpaceID);"); 
			
			$CheckSpaceMembershipStatus_cmd=Yii::$app->db->createCommand("SELECT COUNT(user_id) 
				FROM space_membership 
				WHERE (user_id=:UID AND status=$TheAction AND space_id=$SpaceID);"); 
			
			$AddSpaceMembership_cmd=Yii::$app->db->createCommand("INSERT INTO space_membership 
				(space_id,user_id,status,created_at,created_by,updated_at,updated_by,group_id,show_at_dashboard,can_cancel_membership,send_notifications) 
				VALUES 
				($SpaceID,:UID,$TheAction,'$TodaysDate',1,'$TodaysDate',1,'member',1,1,0);"); 
			
			$UpdateSpaceMembership_cmd=Yii::$app->db->createCommand("UPDATE space_membership SET 
				status=$TheAction 
				WHERE (user_id=:UID AND space_id=$SpaceID);"); 
			
			$GetIDsFromGroup_cmd=Yii::$app->db->createCommand("SELECT user_id AS UserID, group_id 
				FROM group_user 
				WHERE (group_id=$GroupID);")->queryAll(); 
			foreach($GetIDsFromGroup_cmd as $GetIDsFromGroup_row){
				$EachUser=$GetIDsFromGroup_row['UserID']; 
				if($CheckSpaceMembership_cmd->bindValue(':UID', $EachUser)->queryScalar()==0){
					$AddSpaceMembership_cmd->bindValue(':UID', $EachUser)->queryAll(); 
					$UsersAffected++; 
					}
				elseif($CheckSpaceMembership_cmd->bindValue(':UID', $EachUser)->queryScalar()==1){
					if($CheckSpaceMembershipStatus_cmd->bindValue(':UID', $EachUser)->queryScalar()==1){
						//do nothing, right?
						}
					elseif($CheckSpaceMembershipStatus_cmd->bindValue(':UID', $EachUser)->queryScalar()==0 && $TheAction==1){
						//do nothing, right?
						//because member > invite
						}
					elseif($CheckSpaceMembershipStatus_cmd->bindValue(':UID', $EachUser)->queryScalar()==0 && $TheAction==3){
						$UpdateSpaceMembership_cmd->bindValue(':UID', $EachUser)->queryAll(); 
						$UsersAffected++; 
						}
					else{die('Error!! - CheckSpaceMembershipStatus_cmd'); }; 
					}
				else{die('Error!! - CheckSpaceMembership_cmd'); }; 
				}
			
			$jsonResponse['UsersAffected']=$UsersAffected; 
			$jsonResponse['ActionTaken']=$ActionTaken; 
			echo json_encode($jsonResponse,JSON_FORCE_OBJECT);  
			exit;
			}
		
		elseif(Yii::$app->request->post('KickThemAll')=='Yes'){
			$SpaceID=Yii::$app->request->post('TheSpacesSelectVal'); 
			$GroupID=Yii::$app->request->post('TheGroupSelectVal'); 
			$KickConf=Yii::$app->request->post('KickConf'); 
			$UsersAffected=0; 
			if($KickConf!=1){die('Error!! - TheAction'); }; 
			
			$KickEmAll_cmd=Yii::$app->db->createCommand("DELETE FROM space_membership 
				WHERE (user_id=:UID AND space_id=$SpaceID);"); 
			
			$GetIDsFromGroup_cmd=Yii::$app->db->createCommand("SELECT user_id AS UserID, group_id 
				FROM group_user 
				WHERE (group_id=$GroupID);")->queryAll(); 
			foreach($GetIDsFromGroup_cmd as $GetIDsFromGroup_row){
				$EachUser=$GetIDsFromGroup_row['UserID']; 
				$KickEmAll_cmd->bindValue(':UID', $EachUser)->queryAll(); 
				$UsersAffected++; 
				}
			$jsonResponse['UsersAffected']=$UsersAffected; 
			echo json_encode($jsonResponse,JSON_FORCE_OBJECT);  
			exit;
			}
		
		else{
			echo 'oops'; 
			exit;
			}
		
		}
	
	}
