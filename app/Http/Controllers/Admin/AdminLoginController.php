<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Repositories\Admin\AdminRepository;
use App\Repositories\Admin\AdminLoginLogRepository;
use App\Repositories\System\Ip2locationRepository;

class AdminLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * 登入後導向的位置
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected $adminRepository;
    protected $adminLoginLogRepository;
    protected $ip2locationRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        AdminRepository $adminRepository,
        AdminLoginLogRepository $adminLoginLogRepository,
        Ip2locationRepository $ip2locationRepository
    ) {
        $this->middleware('guest')->except('logout');
        $this->adminRepository = $adminRepository;
        $this->adminLoginLogRepository = $adminLoginLogRepository;
        $this->ip2locationRepository = $ip2locationRepository;
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * 通過驗證後的動作
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //更新登入數次及時間
        $user->login_time = date('Y-m-d H:i:s');
        $user->login_count++;
        $user->token = session('_token');
        $user->save();
        //重要資訊寫入Session
        session([
            'id'       => $user->id,
            'username' => $user->username,
            'roleid'   => $user->roleid,
            'per_page' => 20,
        ]);
        //登入log
        $ip = $request->getClientIp();
        $ip_info = $this->ip2locationRepository->getIpData($ip);
        $ip_info = $ip_info ?? [];
        $this->adminLoginLogRepository->create([
            'adminid' => $user->id,
            'ip'      => $ip,
            'ip_info' => json_encode($ip_info),
        ]);
        //轉跳
        if (session('refer')) {
            $refer = session('refer');
            session(['refer'=>null]);
            return redirect($refer);
        } else {
            return redirect('/');
        }
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $domain = $request->server('HTTP_HOST');
        $where[] = [$this->username(), '=', $request[$this->username()]];
        $admin = $this->adminRepository->search(['domain'=>$domain])
                    ->where($where)->result_one();
        if ($admin !== null) {
            return $this->guard()->attempt(
                $this->credentials($request),
                $request->filled('remember')
            );
        }
        return false;
    }

    /**
     * 定義帳號欄位
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * 登出後動作
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        return redirect('login');
    }
}
