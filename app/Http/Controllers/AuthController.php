<?php

namespace App\Http\Controllers;

use App\Models\User;
use Validator;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Users\UserInterface;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Illuminate\Support\Facades\Redirect;
use App\Models\CrawlerType;
use App\Repositories\User\UserRepositoryInterface;
use Config;

class AuthController extends BaseController
{
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(UserRepositoryInterface $user)
    {
        // $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
        $this->user = $user;
    }

    /**
	 * Show the form for logging the user in.
	 *
	 * @return \Illuminate\View\View
	 */
	public function login()
	{
		return view('sentinel.login');
	}

	/**
	 * Handle posting of the form for logging the user in.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
      public function processLogin(Request $request)
    {
        try {
            $input = $request->all();
            $rules = [
                'email'    => 'required|min:3',
                'password' => 'required|min:6',
            ];
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::back()
                        ->withInput()
                        ->withErrors($validator);
            }
            $remember = (bool) $request->get('remember', false);
            if (Sentinel::authenticate($input, $remember)) {
                return Redirect(Route('rank.index'));
            }
            $errors = 'Tên đăng nhập hoặc mật khẩu không đúng.';
        } catch (NotActivatedException $e) {
            $errors = 'Tài khoản của bạn chưa được kích hoạt!';
        } catch (ThrottlingException $e) {
            $delay = $e->getDelay();
            $errors = "Tài khoản của bạn bị block trong vòng {$delay} giây.";
        }

        return Redirect::back()->withInput()->withErrors($errors);
    }

    /**
     * Show the form for the user registration.
     *
     * @return \Illuminate\View\View
     */
    public function register()
    {
        return View::make('sentinel.register');
    }

    /**
     * Handle posting of the form for the user registration.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processRegistration(Request $request)
    {
        $input = $request->all();
        $rules = [
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'password_confirm' => 'required|same:password',
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        if ($user = Sentinel::register($input)) {
            $activation = Activation::create($user);
            $code = $activation->code;
            $sent = Mail::send('sentinel.emails.activate',
                    compact('user', 'code'), function($m) use ($user)
            {
                $m->to($user->email)->subject('Kích hoạt tài khoản');
            });
            if ($sent === 0) {
                return Redirect::to('register')
                    ->withErrors('Gửi email kích hoạt không thành công.');
            }
            return Redirect::to('login')
                ->withSuccess('Tài khoản đã được tạo. Hãy kiểm tra email để
                        nhận hướng dẫn tiếp theo.')
                ->with('userId', $user->getUserId());
        }
        return Redirect::to('register')
            ->withInput()
            ->withErrors('Đăng ký không thành công.');
    }

    public function activate($id, $code)
    {
        $user = Sentinel::findById($id);

        if (!Activation::complete($user, $code)) {
            return Redirect::to("login")
                ->withErrors('Mã kích hoạt không hợp lệ.');
        }

        return Redirect::to('login')
            ->withSuccess('Tài khoản đã được đăng ký. Bạn có thể đăng nhập ngay bây giờ.');
    }

    public function resetPassword()
    {
        return View::make('sentinel.reset.sendEmail');
    }

    public function sendCodeResetPassword(Request $request)
    {
        $rules = [
            'email' => 'required|email',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }
        $email = $request->get('email');
        $user = Sentinel::findByCredentials(compact('email'));

        if ( ! $user) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['email'=>'Không tìm thấy thành viên có email này.']);
        }
        $reminder = Reminder::exists($user) ?: Reminder::create($user);
        $code = $reminder->code;
        $sent = Mail::send('sentinel.emails.reminder', compact('user', 'code'),
            function($m) use ($user) {
            $m->to($user->email)->subject('Lấy lại mật khẩu.');
        });
        if ($sent === 0) {
            return Redirect::to('register')
            ->withErrors(['email'=>'Gửi email lấy lại mật khẩu không thành công.']);
        }

        return Redirect::to('login')
            ->withSuccess("Vui lòng kiểm tra email để nhận hướng dẫn tiếp theo!");
    }

    public function processResetPassword($id, $code)
    {
        $user = Sentinel::findById($id);
        return View::make('sentinel.reset.enterNewPassword');
    }

    public function confirmNewPassword(Request $request, $id, $code)
    {
        $rules = [
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }
        $user = Sentinel::findById($id);
        if ( ! $user) {
            return Redirect::back()
                ->withInput()
                ->withErrors('Thành viên không tồn tại');
        }
        if ( ! Reminder::complete($user, $code, $request->get('password'))) {
            return Redirect::to('login')
                ->withErrors('Mã reset password không hợp lệ');
        }

        return Redirect::to('login')->withSuccess("Đặt mật khẩu mới thành công.");
    }

    public function editProfile()
    {
        $user = Sentinel::getUser();
        $subUsers = $this->user->getSubUsers($user->id);
        $userNotSub = $this->user->getUsersNotSub($user->id);
        $sources = Config::get('constant.stream_types');

        if($user['id'] != $user['related_id']) {
            $userNotSub = [];
        } else {
            // get source by user
            if (!empty($userNotSub)) {
                foreach ($userNotSub as $key => $value) {
                    $nameSource = $sources[$value['source_id']];
                    $userNotSub[$key]['source'] = $nameSource;
                    if($userNotSub[$key]['related_id'] != $user['id'] && $userNotSub[$key]['related_id'] != $userNotSub[$key]['id']) {
                        unset($userNotSub[$key]);
                    }
                }
            }
        }

        return view('sentinel.profile')
            ->with('sources', $sources)
            ->with('user', $user)
            ->with('subUsers', $subUsers->pluck('id')->toArray())
            ->with('userNotSub', $userNotSub);
    }

    public function processEditProfile(Request $request)
    {
        $sources = Config::get('constant.stream_types');
        $input = $request->all();
        $user = Sentinel::getUser();
        $data = [];
        $rules = [
            'first_name' => 'required'
        ];
        $messages = [
            'first_name.required' => 'You can\'t leave the First name empty.'
        ];
        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $data = [
            'first_name' => $first_name,
            'last_name' => $last_name
        ];
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::back()
            ->withInput()
            ->withErrors($validator);
        }
        $sub_users = $request->get('sub_user');
        $mainUser = Sentinel::findById($user->related_id);
        // update sub user
        if (!empty($sub_users) && $user['id'] == $user['related_id']) {
            $sub_users = array_unique($sub_users);
            $keyMain = array_search($mainUser->id, $sub_users);
            if($keyMain != null) {
                unset($sub_users[$keyMain]);
            }
            foreach ($sub_users as $sub_user) {
                if ($sub_user != null) {
                    $subUser = Sentinel::findById($sub_user);
                    $sourceSub = $sources[$subUser->source_id];
                    $getListSubUsers = $this->user->getSubUsers($subUser->id);
                    if ($subUser->id != $subUser->related_id
                            && $subUser->related_id != $mainUser->id) {
                        $mainUserOther = Sentinel::findById($subUser->related_id);
                        $mainSourceOther = $sources[$mainUserOther->source_id];

                        return Redirect::back()
                            ->withErrors($subUser->user_name.' - '.$sourceSub. ' is a child of user '.$mainUserOther->user_name.' - '.$mainSourceOther.' !')
                            ->withInput();
                    } elseif (count($getListSubUsers) > 0) {
                        return Redirect::back()
                            ->withErrors($subUser->user_name.' - '.$sourceSub. ' is a main of another user !')
                            ->withInput();
                    } else {
                        Sentinel::update($subUser, array('related_id' => $mainUser->id));
                    }
                }
            }
        }
        
        Sentinel::update($user, $data);

        return Redirect::route('edit.profile')->withSuccess("Update profile successfully!");
    }
    
    public function changePassword()
    {
        $user = Sentinel::getUser();
    
        return view('sentinel.change_password')
        ->with('user', $user);
    }
    
    public function processChangePassword(Request $request)
    {
        $input = $request->all();
        $user = Sentinel::getUser();
        $data = [];
        $rules = [
            'old_password' => 'min:6',
            'new_password' => 'min:6|regex:/^[a-zA-Z0-9@]+$/',
            'new_password_confirm' => 'min:6|same:new_password|regex:/^[a-zA-Z0-9@]+$/'
        ];
        $messages = [
            'old_password.min' => 'Your password has at least 6 characters.',
            'new_password.min' => 'Your new password has at least 6 characters!',
            'new_password.regex' => 'Your new password only has some characters: a-z, A-Z, 0-9,@.',
            'new_password_confirm.min' => 'Your password must has at lease 6 characters!',
            'new_password_confirm.same' => 'The password don\'t match. Try again?',
            'new_password_confirm.regex' => 'Your new password only has some characters: a-z, A-Z, 0-9,@.'
        ];
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::back()
            ->withInput()
            ->withErrors($validator);
        }
        $hasher = Sentinel::getHasher();
        $oldPassword = $request->get('old_password');
        $password = $request->get('new_password');
        $passwordConf = $request->get('new_password_confirm');
        if(empty($oldPassword)) {
            return Redirect::back()
            ->withErrors('You can\'t leave the old password empty.')
            ->withInput();
        }
        if (ctype_space($password)) {
            return Redirect::back()
            ->withErrors('Password should include some characters: a-z,A-Z,0-9,@.')
            ->withInput();
        }
        if (!$hasher->check($oldPassword, $user->password) && !empty($oldPassword)) {
            return Redirect::route('change.password')
            ->withErrors(['old_password' => 'Your old password is incorrect!']);
        }
        if (!empty($password)) {
            $data['password'] = $password;
        }
    
        Sentinel::update($user, $data);
    
        return Redirect::route('change.password')->withSuccess("Change Password successfully!");
    }
}
