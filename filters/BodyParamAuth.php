<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\filters;


use Yii;
use yii\web\Cookie;
use yii\filters\auth\AuthMethod;
use \Datetime;
use \DateInterval;
use yii\base\Security;
use app\models\User;
use app\models\Userb;
use app\controllers\MainapiController;
/**
 * HttpBearerAuth is an action filter that supports the authentication method based on HTTP Bearer token.
 *
 * You may use HttpBearerAuth by attaching it as a behavior to a controller or module, like the following:
 *
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'bearerAuth' => [
 *             'class' => \yii\filters\auth\HttpBearerAuth::className(),
 *         ],
 *     ];
 * }
 * ```
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BodyParamAuth extends AuthMethod
{
    /**
     * @var string the HTTP authentication realm
     */
    public $realm = 'api';
    protected $lifeTimeToken = 30;
	protected $TimeToChangeToken = 1;

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {	
		$cookies = Yii::$app->request->cookies;
		$authHeader = $cookies->getValue('token', 'en');
		
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            $identity = $user->loginByAccessToken($matches[1], get_class($this));
            
            if ($identity === null) {
                $this->handleFailure($response);
            } else { 
				
				$lastLoginTime = new DateTime($user->getIdentity()->lastLoginTime);
				$lastLoginTime->add(new DateInterval('P'.$this->lifeTimeToken.'D'));
				$currentDate = new DateTime(date('Y-m-d'));
				$currentDate->add(new DateInterval('P'.$this->TimeToChangeToken.'D'));
				
				if($lastLoginTime->format('Y-m-d') <= $currentDate->format('Y-m-d')) {
					$generate = Yii::$app->security;
					$generateToken = $generate->generateRandomString();
					//$customer = User::findOne($user->getIdentity()->idUser);
					$customer = User::findOne($user->getIdentity()->user_id);
					$customer->access_token = $generateToken;
					$customer->lastLoginTime = date('Y-m-d');
					
					if($customer->save()) {						
						$cookies = Yii::$app->response->cookies;
						$cookies->add(new \yii\web\Cookie([
							'name' => 'token',
							'value' => 'Bearer '.$generateToken,
						]));
					}
				} 
            }
            return $identity;
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function challenge($response)
    {
        $response->getHeaders()->set('WWW-Authenticate', "Bearer realm=\"{$this->realm}\"");
    }
}
