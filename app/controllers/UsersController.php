<?php
use Illuminate\Routing\Controller;
use Dingo\Api\Routing\ControllerTrait;

/**
 * UsersController Class
 *
 * Implements actions regarding user management
 */
class UsersController extends Controller
{
    use ControllerTrait;

    /**
     * Stores new account
     *
     * @return  Illuminate\Http\Response
     */
    public function postIndex()
    {
        $repo = App::make('UserRepository');
        $user = $repo->signup(Input::all());

        if ($user->id) {
            if (Config::get('confide::signup_email')) {
                Mail::queueOn(
                    Config::get('confide::email_queue'),
                    Config::get('confide::email_account_confirmation'),
                    compact('user'),
                    function ($message) use ($user) {
                        $message
                            ->to($user->email, $user->username)
                            ->subject(Lang::get('confide::confide.email.account_confirmation.subject'));
                    }
                );
            }

            return $this->response->array($user);
        } else {
            $error = $user->errors()->all(':message');

            return $this->response->errorBadRequest(array('message' => $error));
        }
    }

    public function putIndex($id) {
        $repo = App::make('UserRepository');
        $user = $repo->update(Input::all(), $id);

        if($user) {
            return $this->response->array($user);
        } else {
            $error = $user->errors()->all(':message');

            return $this->response->errorBadRequest(array('message' => $error));
        }
    }


    /**
     * Attempt to do login
     *
     * @return  Illuminate\Http\Response
     */
    public function postLogin()
    {
        $repo = App::make('UserRepository');
        $input = Input::all();

        if ($repo->login($input)) {
            $token = JWTAuth::fromUser(Auth::user());

            return $this->response->array(array('token' => $token));
        } else {
            if ($repo->isThrottled($input)) {
                $err_msg = Lang::get('confide::confide.alerts.too_many_attempts');
            } elseif ($repo->existsButNotConfirmed($input)) {
                $err_msg = Lang::get('confide::confide.alerts.not_confirmed');
            } else {
                $err_msg = Lang::get('confide::confide.alerts.wrong_credentials');
            }

            return $this->response->errorNotFound(array('message' => $err_msg));
        }
    }

    /**
     * Attempt to confirm account with code
     *
     * @param  string $code
     *
     * @return  Illuminate\Http\Response
     */
    public function getConfirm($code)
    {
        if (Confide::confirm($code)) {
            $notice_msg = Lang::get('confide::confide.alerts.confirmation');
            return Redirect::action('UsersController@getLogin')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');
            return Redirect::action('UsersController@getLogin')
                ->with('error', $error_msg);
        }
    }

    /**
     * Attempt to send change password link to the given email
     *
     * @return  Illuminate\Http\Response
     */
    public function postForgot()
    {
        if (Confide::forgotPassword(Input::get('email'))) {
            $notice_msg = Lang::get('confide::confide.alerts.password_forgot');
            // return Redirect::action('UsersController@getLogin')
            //     ->with('notice', $notice_msg);
            return $this->response->array($notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');
            // return Redirect::action('UsersController@postForgot')
            //     ->withInput()
            //     ->with('error', $error_msg);
            return $this->response->errorBadRequest($error_msg);
        }
    }

    /**
     * Attempt change password of the user
     *
     * @return  Illuminate\Http\Response
     */
    public function postReset()
    {
        $repo = App::make('UserRepository');
        $input = array(
            'token'                 =>Input::get('token'),
            'password'              =>Input::get('password'),
            'password_confirmation' =>Input::get('password_confirmation'),
        );

        // By passing an array with the token, password and confirmation
        if ($repo->resetPassword($input)) {
            $notice_msg = Lang::get('confide::confide.alerts.password_reset');
            return Redirect::action('UsersController@getLogin')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_reset');
            return Redirect::action('UsersController@getReset', array('token'=>$input['token']))
                ->withInput()
                ->with('error', $error_msg);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return  Illuminate\Http\Response
     */
    public function getLogout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return $this->response->array(array('message' => 'Successfully logged out'));
    }

    public function refresh()
    {
      $oldToken = JWTAuth::getToken();

      // try {
        $newToken = JWTAuth::refresh($oldToken);
      // } catch (Exception $e) {
        // return $this->response->errorBadRequest($e);
      // }

      return $newToken;
    }
}
