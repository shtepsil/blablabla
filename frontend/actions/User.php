<?php

namespace frontend\actions;

use common\components\Debugger as d;
use common\models\Items;
use common\models\Orders;
use common\models\User as ModelUser;
use Yii;
use yii\db\ActiveQuery;

class User
{

    public $post = [];
    public $user_table;

    public function run()
    {
        $this->user_table = preg_replace('~[^a-z]~', '', ModelUser::tableName());
        $this->post = d::post();
        switch ($this->post['type']) {
            case 'get_user':
                $response = $this->getUser();
                break;
            case 'get_users':
                $response = $this->getUsers();
                break;
            case 'set_wholesale':
                $response = $this->setWholesale();
                break;
            case 'set_deleted':
                $response = $this->setDeleted();
                break;
            case 'update_origin':
                $response = $this->updateOrigin();
                break;
            default:
                $response = $this->testDuplicates();
//                $response = 'User->run()->switch:default';
        }
        return $response;
    }

    public function testDuplicates()
    {
        $users_duplicate = $this->getDuplicates();
//        d::ajax(count($users_duplicate));
//        d::ajax($users_duplicate);

        // ==============================================
        $duplicates_ids = [];
        // Собираем массив всех дублирующихся аккаунтов
        foreach($users_duplicate as $orig_id){
            $duplicates_ids[] = $orig_id['id'];
        }

        // Получить всех, где deleted = 2
        $all_duplicates = [];
//        $all_duplicates = ModelUser::find()->where(['in', 'id', $duplicates_ids])->andWhere(['deleted' => '3'])->all();
//        d::ajax(count($all_duplicates));

        $res_u = [];
        $res_count = [];
        if(count($all_duplicates) > 0){
            foreach($all_duplicates as $a_d){
                if($a_d->email AND $a_d->email != '' AND $a_d->password_hash == ''){
                    $res_count[] = $a_d;
                    $res_u[] = [
                        'id' => $a_d->id,
                        'email' => $a_d->email,
                        'password_hash' => $a_d->password_hash,
                        'auth_key' => $a_d->auth_key,
                        'bonus' => $a_d->bonus,
                        'order_sum' => $a_d->order_sum
                    ];
                }
            }
//        d::ajax(count($res_count));
//        d::ajax($res_u);
        }

        // ==============================================

        // Группируем все копии по ключу(номер телефона)
        $users = [];
        foreach($users_duplicate as $u){
            $users[$u['phone']][] = $u;
        }
//        d::ajax($users);

        // Убираем всё лишнее, и оставляем только нужное.
        $new_users = [];
        foreach($users as $phone => $user_rows){
            $user_filter = [];
            foreach($user_rows as $user_row){
                $id = '';
                foreach($user_row as $fd => $u_value){
                    if($fd == 'id'){
                        $user_filter[$fd] = $u_value;
                        $user_filter['ids'][] = $u_value;
                        $id = $u_value;
                    }
                    if($fd == 'username' and $u_value != 'Быстрый заказ'){
                        $user_filter[$fd] = 'ID: ' . $id . ' - ' . $u_value;
                    }
                    // Если поле email и поле email не пусто
                    if($fd == 'email' AND $u_value != ''){ //isset($user_row['email']) AND $user_row['email'] != ''
                        $user_filter[$fd] = 'ID: ' . $id . ' - ' . $u_value;
                    }
                    if($fd == 'bonus'){
                        if(!isset($user_filter[$fd])){
                            $user_filter[$fd] = $u_value;
                        }else{
                            $user_filter[$fd] += $u_value;
                        }
                    }
                    if($fd == 'order_sum'){
                        $user_filter[$fd] = 'ID: ' . $id . ' - ' . $u_value;
                    }
                    if($fd == 'password_hash' AND isset($user_row['email']) AND $user_row['email'] != ''){
                        $user_filter[$fd] = 'Password_hash: ' . $id . ' - ' . $u_value;
                    }
                }
            }
            $new_users[$phone] = $user_filter;
        }
//        d::ajax(count($new_users));
        d::ajax($new_users);

        $empty_element = [];
        foreach($new_users as $phone => $n_u){
            if(
                isset($n_u['email'])
                AND $n_u['email'] != ''
                AND isset($n_u['password_hash'])
                AND $n_u['password_hash'] == ''
            ){
                $empty_element[$phone] = $n_u;
            }
        }
//        d::ajax($empty_element);
        d::ajax(count($empty_element));

        return $new_users;
    }// testDuplicates

    public function setDeleted()
    {
        $users_duplicate = $this->getDuplicates();

        /**
         * Получение ID всех дубликатов
         * ============================
         * Вот у этих всех аккаунтов, поле deleted нужно установить как "удалён"
         */
        $duplicates_ids = [];
        foreach($users_duplicate as $orig_id){
            $duplicates_ids[] = $orig_id['id'];
        }

        ModelUser::updateAll(['status' => '0'], ['in', 'id', $duplicates_ids]);
        return 'Всем дубликатам установлено status = 0';
    }

    public function updateOrigin()
    {
        $users_duplicate = $this->getDuplicates();

        $new_users = $this->getNewUsers($users_duplicate);

        $orig_users_key_id = [];
        foreach($new_users as $new_user){
            $orig_users_key_id[$new_user['id']] = $new_user;
        }

//        $all_original = ModelUser::find()->where(['in', 'id', array_keys($orig_users_key_id)])->andWhere(['deleted' => '1'])->all();
//        $all_original = ModelUser::find()->where(['in', 'deleted', ['1', '3']])->all();
//        d::ajax(count($all_original));

        $update_deleted_query = "UPDATE `" . $this->user_table . "` SET ";

        // =====================================================
        $update_deleted_query .= " `username`= CASE `id`";
        foreach($orig_users_key_id as $new_users_id => $user_item){
            $update_deleted_query .=
                " WHEN '" . $new_users_id . "' THEN '" . $user_item['username'] . "'";
        }
        $update_deleted_query .= " ELSE `username` END,";
        // =====================================================

        // =====================================================
        $update_deleted_query .= " `email`= CASE `id`";
        foreach($orig_users_key_id as $new_users_id => $user_item){
            if(isset($user_item['email'])){
                $update_deleted_query .=
                    " WHEN '" . $new_users_id . "' THEN '" . $user_item['email'] . "'";
            }
        }
        $update_deleted_query .= " ELSE `email` END,";
        // =====================================================

        // =====================================================
        $update_deleted_query .= " `bonus`= CASE `id`";
        foreach($orig_users_key_id as $new_users_id => $user_item){
            $update_deleted_query .=
                " WHEN '" . $new_users_id . "' THEN '" . $user_item['bonus'] . "'";
        }
        $update_deleted_query .= " ELSE `bonus` END,";
        // =====================================================

        // =====================================================
        $update_deleted_query .= " `password_hash`= CASE `id`";
        foreach($orig_users_key_id as $new_users_id => $user_item){
            if(isset($user_item['password_hash'])) {
                $update_deleted_query .=
                    " WHEN '" . $new_users_id . "' THEN '" . $user_item['password_hash'] . "'";
            }
        }
        $update_deleted_query .= " ELSE `password_hash` END,";
        // =====================================================

        // =====================================================
        $update_deleted_query .= " `order_sum`= CASE `id`";
        foreach($orig_users_key_id as $new_users_id => $user_item){
            $update_deleted_query .=
                " WHEN '" . $new_users_id . "' THEN '" . $user_item['order_sum'] . "'";
        }
        $update_deleted_query .= " ELSE `order_sum` END,";
        // =====================================================

        // =====================================================
        $update_deleted_query .= " `status`= CASE `id`";
        foreach($orig_users_key_id as $new_users_id => $user_item){
            $update_deleted_query .=
                " WHEN '" . $new_users_id . "' THEN '10'";
        }
        $update_deleted_query .= " ELSE `status` END";
        // =====================================================

        $update_deleted_query .=
            " WHERE `id` IN('" . implode("','", array_keys($orig_users_key_id)) . "')";
//        d::pex($update_deleted_query);

        // Запускаем скрипт обновления
        $update_deleted_command = Yii::$app->db->createCommand($update_deleted_query);

        try {
            $update_deleted_command->execute();
            $result = 'Оригиналы обновлены!';
        }catch (\Exception $e){
            $result = $e->getMessage();
        }

        return $result;

    }

    public function getNewUsers($users_duplicate)
    {
        // Группируем все копии по ключу(номер телефона)
        $users = [];
        foreach($users_duplicate as $u){
            $users[$u['phone']][] = $u;
        }

        // Убираем всё лишнее, и оставляем только нужное.
        $new_users = [];
        foreach($users as $phone => $user_rows){
            $user_filter = [];
            foreach($user_rows as $user_row){
                foreach($user_row as $fd => $u_value){
                    if($fd == 'id'){
                        $user_filter[$fd] = $u_value;
                    }
                    if($fd == 'username' and $u_value != 'Быстрый заказ'){
                        $user_filter[$fd] = $u_value;
                    }
                    // Если поле email и поле email не пусто
                    if($fd == 'email' AND $u_value != ''){
                        $user_filter[$fd] = $u_value;
                    }
                    if($fd == 'bonus'){
                        if(!isset($user_filter[$fd])){
                            $user_filter[$fd] = $u_value;
                        }else{
                            $user_filter[$fd] += $u_value;
                        }
                    }
                    if($fd == 'order_sum'){
                        $user_filter[$fd] = $u_value;
                    }
                    if(
                        $fd == 'password_hash'
                        AND isset($user_row['password_hash'])
                        AND $user_row['password_hash'] != ''
                        AND isset($user_row['email'])
                        AND $user_row['email'] != ''
                    ){
                        $user_filter[$fd] = $u_value;
                    }
                }
            }
            $new_users[$phone] = $user_filter;
            // 549 2777 6691
        }
//        d::ajax($new_users);

        // ===============================================
        // DEBUG
//        $res_count = [];
//        foreach($new_users as $phone => $u){
////            if(isset($u['email']) AND $u['email'] != '' AND isset($u['password_hash']) AND $u['password_hash'] == ''){
//            if(isset($u['email']) AND $u['email'] != '' AND !isset($u['password_hash'])){
//                $res_count[$phone] = $u;
//            }
//        }
//        d::ajax(count($res_count));
//        d::ajax($res_count);
        // ===============================================

        return $new_users;
    }

    public function getDuplicates()
    {
        $query = "SELECT `id`, " . $this->user_table . ".username, " . $this->user_table . ".phone, " . $this->user_table . ".email, " . $this->user_table . ".bonus, " . $this->user_table . ".order_sum, " . $this->user_table . ".password_hash, " . $this->user_table . ".auth_key FROM " . $this->user_table . " INNER JOIN (SELECT `phone` FROM `" . $this->user_table . "` GROUP BY `phone` HAVING COUNT(*) > 1) dup ON " . $this->user_table . ".phone != '' AND " . $this->user_table . ".phone = dup.phone";

        $users_result = Yii::$app->db->createCommand($query)->queryAll();
//        d::ajax($users_result);

        return $users_result;
    }

    public function getUser()
    {
        $field = 'id';
        if(preg_match('~\+7~', $this->post['user_data'])){
            $field = 'phone';
        }
        $user = ModelUser::find()->where([$field => $this->post['user_data']])->one();
        if ($user) {
            return $user;
        } else
            return 'Пользователь не найден';
    }

    public function getUsers()
    {
        $field = 'id';
        if(preg_match('~\+7~', $this->post['user_data'])){
            $field = 'phone';
        }
        $users = ModelUser::find()->where([$field => $this->post['user_data']])->all();
        if($this->post['user_data'])
        if ($users) {
            return $users;
        } else
            return 'Пользователь не найден';
    }

    public function setWholesale()
    {
        $user = ModelUser::findOne($this->post['user_id']);
        $user->isWholesale = $this->post['is_wholesale'];
        //        d::pe($user);
        if ($user->save()) {
            return 'Изменено: user->isWholesale - ' . $user->isWholesale;
        } else {
            return $user->getErrors();
        }
    }

} //Class
