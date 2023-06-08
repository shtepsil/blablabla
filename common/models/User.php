<?php
namespace common\models;

use backend\models\SUser;
use common\components\Debugger as d;
use app\models\Auth;
use shadow\helpers\StringHelper;
use shadow\SActiveRecord;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use shadow\helpers\Json;
use common\validators\UserExist;
use common\models\UserDeleted;
use frontend\form\EditRequisites;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $isSubscription
 * @property integer $isNotification
 * @property integer $sex
 * @property integer $dob
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $isEntity
 * @property integer $isWholesale
 * @property float $bonus
 * @property string $password write-only password
 * @property string $subs_email
 * @property string $data JSON
 * @property string $phone
 * @property string $code
 * @property integer $order_sum
 * @property double $discount
 * @property string $personal_discount
 * @property double $manager_id
 * @property integer $city_id
 *
 * @property City $city
 * @property UserAddress[] $userAddresses
 * @property HistoryBonus[] $historyBonuses
 * @property UserInvited[] $userInviteds
 * @property UserInvited[] $userInviteds0
 * @property Orders[] $userOrders
 * @property Orders $lastUserOrder
 */
class User extends SActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    public $cnt;
    public static $user_type = false;
    // И тут оно нужно и в EditUser тоже нужно
    public $parent;

    // Наверно всё это фигня, что я тут по написал, но пока так.
    public static $id;
    public static $current_user = null;

    // Пользователь/Оптовик/Оптовик 2
    public static $user_types = [
        [
            'type' => '0',
            'label' => 'Пользователь',
        ],
        [
            'type' => '1',
            'label' => 'Оптовик',
        ],
        [
            'type' => '2',
            'label' => 'Оптовик 2',
        ],
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * В общем этот выкрутас нужен для того, чтобы при получении данных из БД
     * запускался метод afterFind, который настраивает кое-какие поля.
     * А при получении пользователя через Yii::$app->user - afterFind что то не запускается.
     * =====================================================================================
     * Если текущий метод участвует в цикле, то
     * обращение к БД для получения пользователя сделаем единожды.
     * @return array|User|null
     */
    public static function getCurrentUser()
    {
        if(self::$id != null){
            // Если пользователь ещё не задан
            if(self::$current_user == null){
                self::$current_user = User::find()->where(['id' => self::$id, 'status' => User::STATUS_ACTIVE])->one();
            }else{
            // Если пользователь уже задан, но...
                // Если нужно получить другого пользователя
                if(is_object(self::$current_user) AND self::$current_user instanceof User AND self::$current_user->id != self::$id){
                    self::$current_user = User::find()->where(['id' => self::$id, 'status' => User::STATUS_ACTIVE])->one();
                }
            }
        }

        return self::$current_user;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            [['isEntity', 'bonus', 'order_sum', 'isWholesale'], 'default', 'value' => 0],
            [['count_sms', 'date_last_sms'], 'integer'],
            [['photo'], 'string', 'max' => 100],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['email', UserExist::className()],
            ['phone', UserExist::className()],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
        //throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $email
     * @return static|null
     */
    public static function findByUsername($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    public function getOrders()
    {
        return $this->hasOne(Orders::class, ['user_id' => 'id']);
    }

    public function getOrdersall()
    {
        return $this->hasMany(Orders::class, ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuths()
    {
        return $this->hasMany(Auth::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAddresses()
    {
        return $this->hasMany(UserAddress::className(), ['user_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistoryBonuses()
    {
        return $this->hasMany(HistoryBonus::className(), ['user_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInviteds()
    {
        return $this->hasMany(UserInvited::className(), ['user_invited' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInviteds0()
    {
        return $this->hasMany(UserInvited::className(), ['user_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserOrders()
    {
        return $this->hasMany(Orders::className(), ['user_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastUserOrder()
    {
        return $this->hasOne(Orders::className(), ['user_id' => 'id'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(1)
        ;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(SUser::className(), ['id' => 'manager_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountOrders()
    {
        return $this->hasMany(Orders::className(), ['user_id' => 'id'])->andWhere(['status' => 5]);

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCou()
    {
        return $this->hasMany(Orders::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function showCountOrders($user_id)
    {
        return Orders::find()->andWhere(['user_id' => $user_id])->andWhere(['status' => 5])->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function sumOrders($user_id)
    {
        return Orders::find()->where(['user_id' => $user_id])->andWhere(['status' => 5])->sum('full_price');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }
    public $data_sex = [
        1 => 'Я — мужчина',
        2 => 'Я — женщина',
    ];
    public $all_users = [];
    public function generateCode($int = 0)
    {
        if ($int == 0) {
            $int = $this->created_at;
        }
        if (!$this->all_users) {
            $this->all_users = User::find()->indexBy('code')->all();
        }
        $code = StringHelper::num2alpha($int);
        if (isset($this->all_users[$code])) {
            $code = $this->generateCode($int - $this->id);
        }
        return $code;
    }

    /**
     * @param $item
     * @param bool $value
     * @param string $type
     * @return array|bool|mixed
     *
     * Чтобы добавить новую настройку,
     * в параметр $item - нужно задать ключ настройки [$item => ... ].
     * в параметр $value нужно передать
     * либо массив
     * ['one','two','three']
     *   - получится [$item => ['one','two','three']],
     * либо строку 'one'
     *   - получится [$item => 'one'].
     *
     * Если настройка была задана(создана!) массивом ['one','two','three']
     *   - [$item => ['one','two','three']],
     * то в массив можно добавлять новые значения.
     *
     * Если настройка была задана(создана!) стокой 'three' - [$item => 'three'],
     * то у такой настройки значение будет только изменяться,
     * в такую настроку, добавить массив невозможно!
     *
     * Существующие значения настроек удалить пока что нельзя.
     * Можно только добавлять новые значения (если значение настройки - массив),
     * либо заменять существующие (если значение настройки - строка).
     *
     */
    public function settings($item, $value = false, $type = 'get', $flag = false)
    {
        //if($flag) d::ajax($this->id);
        $s = [];
        if ($this->settings) {
            $s = Json::decode($this->settings);
        }
        switch ($type) {
            case 'set':
                // Если в настройках есть ключ настройки
                if (array_key_exists($item, $s)) {
                    // Если значение настройки из БД это массив и этот массив не пуст
                    if (is_array($s[$item]) and count($s[$item])) {
                        // Если $value - это массив значений
                        if (is_array($value) and count($value)) {
                            // Если в массиве нет значения, которое добавляется.
                            $new_items = [];
                            // Перебираем массив значений для добавления
                            foreach ($value as $v) {
                                /*
                                 * Добавятся только те новые значения,
                                 * которых нет в запрошенной настройке.
                                 */
                                if (!in_array($v, $s[$item])) {
                                    $new_items[] = $v;
                                }
                            }
                            /*
                             * Если новые значения выбрались,
                             * то соединяем массив новых значений
                             * с существующим массивом текущей настройки.
                             */
                            if (count($new_items)) {

                                $s[$item] = array_merge($s[$item], $new_items);
                                $this->settings = Json::encode($s, 256);
                                if ($this->save()) {
                                    if (count($new_items) > 1) {
                                        //d::td('В существующий массив добавлены новые значения');
                                    } else {
                                        //d::td('В существующий массив добавлено 1 новое значение');
                                    }
                                    return true;
                                } else {
                                    //d::td('Ошибка добавления 1');
                                    return false;
                                }
                            } else {
                                /*
                                 * Если новых заначений для добавления не нашлось,
                                 * то ничего не делаем.
                                 */
                                //d::td('Такие значения уже существуют');
                                return false;
                            }

                        } else {
                            // Если $value - это строка
                            // Если добавляемой строки нет в массиве
                            if (!in_array($value, $s[$item])) {
                                $s[$item][] = $value;
                                $this->settings = Json::encode($s, 256);
                                if ($this->save()) {
                                    //d::td('Новое значение добавлено в массив настройки');
                                    return true;
                                } else {
                                    //d::td('Ошибка добавления 2');
                                    return false;
                                }
                            } else {
                                //d::td('Такое значение уже существует');
                                return false;
                            }
                        }
                    } else {
                        /*
                         * Если значение настройки из БД это строка
                         * Либо добавляем новое значение,
                         * либо заменяем существующее.
                         */
                        if (is_string($value)) {
                            $s[$item] = $value;
                            $this->settings = Json::encode($s, 256);
                            if ($this->save()) {
                                //d::td('Новое значение добавлено/изменено');
                                return true;
                            } else {
                                //d::td('Ошибка добавления 3');
                                return false;
                            }
                        } else {
                            //d::td('Ошибка операции.<br>Настройка является строкой, а не массивом.');
                            return false;
                        }
                    }
                } else {
                    /*
                     * Если в настройках нет ключа настройки
                     * Просто добавлям новый ключ со значением
                     */
                    $s[$item] = $value;
                    $this->settings = Json::encode($s, 256);
                    //                    if($flag) d::ajax($this->settings);
//                    $this->email = 'tratatatat@mail.ru';// user1@mail.ru
//                    if($flag) d::ajax($this->email);
                    if ($this->save()) {
                        if (is_array($value) and count($value)) {
                            //                            if($flag) d::ajax('Массив настройка');
                            //d::td('Добавлена новая массив-настройка');
                        } else {
                            //                            if($flag) d::ajax('Строка настройка');
                            //d::td('Добавлена новая строка-настройка');
                        }
                        return true;
                    } else {
                        //                        if($flag) d::ajax('Ошибка');
                        //d::td('Ошибка добавления 4');
                        return false;
                    }
                }
                break;
            case 'delete':
                unset($s[$item]);
                if (count($s) > 0) {
                    $this->settings = Json::encode($s, 256);
                } else {
                    $this->settings = NULL;
                }

                if ($this->save()) {
                    //d::ajax('Настройка по ключу полностью удалена');
                    return true;
                } else {
                    //if($flag) d::ajax('Ошибка');
                    //d::ajax('Ошибка удаления');
                    return false;
                }
                break;
            default:
                //                //d::td('Просто получить значение');
                $settings = [];
                /*
                 * Если существует значение $value и это строка
                 * и в БД существует $item-ключ настройки
                 */
                if (($value !== false and is_string($value)) and isset($s[$item])) {
                    $key = array_search($value, $s[$item]);
                    if ($key) {
                        $settings = $s[$item][$key];
                    } else {
                        //                        //d::td('Значение не найдено');
                    }
                } else {
                    // Если значение не передано, то просто получаем настройку по ключу.
                    if (isset($s[$item])) {
                        $settings = $s[$item];
                    }
                }
                return $settings;
        }

        return false;
    }
    public static function checkPhone($phone, $attributes)
    {
        $user = User::find()
            ->where(['not', ['username' => 'Быстрый заказ']])
            ->andWhere(['phone' => $phone, 'status' => self::STATUS_ACTIVE])->one();
        if (!$user) {
            $user = new User();
            $user->phone = $phone;
            $user->setAttributes($attributes, false);
            $user->status = $user::STATUS_ACTIVE;
            $user->password = \Yii::$app->security->generateRandomString(6);
            $user->generateAuthKey();
            if (!$user->save(false)) {
                $user = null;
            }
        }
        return $user;
    }

    public function isWholesale()
    {
        if ($this->isWholesale > 0) { return true; }
        return false;
    }

    public static function exportAll($items, $excel_filename = 'report', $template = 'export_users')
    {
        $data = Yii::$app->controller->renderPartial(
            '@backend/views/blocks/debug/shortcodes/' . $template,
            [
                'items' => $items,
                'city_all' => City::find()->indexBy('id')->all()
            ]
        );

        $file = $excel_filename . '.xls';
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file");
        exit("$data");

        // \moonland\phpexcel\Excel::export([
        // 'fileName' => 'export_users',
        // 'models' => $items,
        // 'columns' => $columns,
// //            'savePath' => Yii::getAlias('@frontend/tmp'),
// //            'asAttachment' => false
        // ]);

    }

    public static function export($items)
    {
        $city_all = City::find()->indexBy('id')->all();
        $columns = [
            [
                'attribute' => 'username',
                'header' => 'ФИО',
                'format' => 'text',
                'value' => function ($model) {
                    return ($model->username) ? $model->username : '';
                },
            ],
            [
                'attribute' => 'countorders',
                'header' => 'Количество заказов',
                'format' => 'text',
                'value' => function ($model) {
                    return ($model->showCountOrders($model->id));
                },
            ],
            [
                'attribute' => 'phone',
                'header' => 'Телефон',
                'format' => 'text',
                'value' => function ($model) {
                    return ($model->phone) ? $model->phone : '';
                },
            ],
            [
                'attribute' => 'city_id',
                'header' => 'Город',
                'format' => 'text',
                'value' => function ($model) use ($city_all) {
                    return (isset($city_all[$model->city_id]) ? $city_all[$model->city_id]->name : 'Не выбран');
                },
            ],
            [
                'attribute' => 'email',
                'header' => 'E-Mail',
                'format' => 'text',
                'value' => function ($model) {
                    return ($model->email) ? $model->email : '';
                },
            ],
            [
                'attribute' => 'isWholesale',
                'header' => 'Статус',
                'format' => 'text',
                'value' => function ($model) {
                    return ($model->isWholesale == 1) ? 'Оптовый' : 'Розничный';
                },
            ],
            [
                'attribute' => 'order_sum',
                'header' => 'Сумма заказов',
                'format' => 'text',
                'value' => function ($model) {
                    return number_format($model->order_sum, 0, '', ' ');
                },
            ],
            [
                'attribute' => 'bonus',
                'header' => 'Сумма бонусов',
                'format' => 'text',
                'value' => function ($model) {
                    /** @var User $model */
                    return ($model->bonus) ? $model->bonus : 0;
                },
            ],
            [
                'attribute' => 'order_sum',
                'header' => 'Процент с заказа',
                'format' => 'text',
                'value' => function ($model) {
                    return Yii::$app->function_system->percent($model->id) . '%';
                },
            ],
            [
                'attribute' => 'discount',
                'header' => 'Скидка',
                'format' => 'text',
                'value' => function ($model) {
                    return ($model->discount ? ($model->discount . '%') : '');
                },
            ],
            [
                'header' => 'Последний заказ',
                'format' => 'text',
                'value' => function ($model) {
                    /** @var User $model */
                    $orders = $model->lastUserOrder;
                    if ($orders) {
                        /**@var Orders $order */
                        $order = $orders;
                        return date('d.m.Y', $order->created_at);
                    } else {
                        return '';
                    }
                },
            ],
            [
                'header' => 'Статус',
                'format' => 'text',
                'value' => function ($model) {
                    /** @var User $model */
                    return ($model->status == $model::STATUS_ACTIVE) ? 'Активирован' : 'Не активирован';
                },
            ],
        ];
        $data = '<table><tr><td>ФИО</td><td>Количество заказов</td><td>Телефон</td><td>Город</td><td>E-Mail</td><td>Статус</td><td>Сумма заказов</td><td>Сумма бонусов</td><td>Последний заказ</td></tr>';

        foreach ($items as $result) {

            $orders_sum = $result->sumOrders($result->id);

            $data .= '<tr><td>'
                . $result->username . '</td><td style="text-align:center;">'
                . $result->showCountOrders($result->id)
                . '</td><td>' . $result->phone . '</td><td>'
                . (isset($city_all[$result->city_id]) ? $city_all[$result->city_id]->name : 'Не выбран')
                . '</td><td>' . $result->email . '</td><td>'
                . (($result->isWholesale == 1) ? 'Оптовый' : 'Розничный') . '</td><td>'
                . number_format($orders_sum, 0, '', ' ')
                . '</td><td>' . (($result->bonus) ? $result->bonus : 0) . '</td><td>'
                . (($result->lastUserOrder != null) ? date('Y-m-d', $result->lastUserOrder->created_at) : '') . '</td></tr>';
        }

        $data .= '</table>';

        $file = '1.xls';
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file");
        exit("$data");

        // \moonland\phpexcel\Excel::export([
        // 'fileName' => 'export_users',
        // 'models' => $items,
        // 'columns' => $columns,
// //            'savePath' => Yii::getAlias('@frontend/tmp'),
// //            'asAttachment' => false
        // ]);

    }

    public function beforeSave($insert)
    {
        $post = Yii::$app->request->post();
        //        d::ajax($post);
        /** Удаляем лишние свойства реквизитов из модели User */
        foreach (EditRequisites::getAttrs() as $prop => $p_value) {
            unset($this->$prop);
        }

        /*
         * Если payment_type не пуст и это массив, сделаем его в строку json.
         * Проверка для админки происходит в EditUser, а это для frontend.
         */
        if(is_array($this->payment_type) and count($this->payment_type) > 0){
            $this->payment_type = json_encode($this->payment_type, 256);
        }elseif(!is_string($this->payment_type)){
            $this->payment_type = NULL;
        }

        /*
         * Если personal_discount не пуст и это массив, сделаем его в строку json.
         * Проверка для админки происходит в EditUser, а это для frontend.
         */
        if(is_array($this->personal_discount) and count($this->personal_discount) > 0){
            $this->personal_discount = Json::encode($this->personal_discount, 256);
        }elseif(!is_string($this->personal_discount)){
            $this->personal_discount = NULL;
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        /*
         * Удаляемого пользователя нужно сохранить
         * в таблицу удалённых пользователей
         */
        $del_user = UserDeleted::findOne(['user_id' => $this->id]);
        /*
         * Чтобы не дублировать запись
         * ===========================
         * Если пользователь ещё не существует,
         * то создадим новую запись(внесём его в БД)
         */
        if (!$del_user) {
            $model_deleted = new UserDeleted();
            $model_deleted->user_id = $this->id;
            $model_deleted->username = $this->username;
            $model_deleted->email = $this->email;
            $model_deleted->phone = $this->phone;
            $model_deleted->sex = $this->sex;
            $model_deleted->deleted_at = time();
            $model_deleted->save();
        }

        return parent::beforeDelete();
    }

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            array_keys(get_class_vars(EditRequisites::className()))
        );
    }

    public $parent_data = NULL;
    public function afterFind()
    {
        parent::afterFind();
        $this->dataJsonToObject();

        if ($this->isWholesale != NULL and $this->isWholesale > 0 and $this->opt_user_id) {
            $user_opt = User::find()->select('data')->where(['id' => $this->opt_user_id])->one();
            if ($user_opt) {
                $this->parent_data = $user_opt->dataJsonToObject();
            }
        }

        $this->payment_type = $this->fieldJsonDecode($this->payment_type);
        $this->personal_discount = $this->fieldJsonDecode($this->personal_discount);

    }

    /**
     * @param $json
     * @param $asArray
     * @return array|mixed|null
     */
    private function fieldJsonDecode($json, $asArray = true)
    {
        $result = [];
        if ($json != NULL and Json::isJson($json)) {
            $result = Json::decode($json, $asArray);
        }
        return $result;
    }

    /**
     * @return $this
     */
    private function dataJsonToObject()
    {
        if (Json::isJson($this->data)) {
            $entity = Json::decode($this->data);
            if (is_array($entity)) {
                $is_requisites = false;
                foreach ($entity as $key => $value) {
                    if (
                            // todo Тут надо по думать, чтобы entity_nds и parent не писать прям тут...
                        ($value != '' and $key != 'entity_nds' and $key != 'parent')
                        or ($key == 'entity_nds' and $value != '0')
                    )
                        $is_requisites = true;

                    $this->{$key} = $value;
                }
                if (!$is_requisites) {
                    foreach ($entity as $key => $value) {
                        unset($this->$key);
                    }
                }
            }
        }
        return $this;
    }

} //Class
