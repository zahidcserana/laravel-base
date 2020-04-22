<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class UserController extends Controller
{
    public $successStatus = 200;
/**
 * login api
 *
 * @return \Illuminate\Http\Response
 */
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->accessToken;
            return response()->json(['success' => $success], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
/**
 * Register api
 *
 * @return \Illuminate\Http\Response
 */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name;
        return response()->json(['success' => $success], $this->successStatus);
    }
/**
 * details api
 *
 * @return \Illuminate\Http\Response
 */
    public function details()
    {
        $user = Auth::user();
        $user->picture = "https://randomuser.me/api/portraits/thumb/men/3.jpg";
        $user->notifications = array(
                                    "id"=> 1,
                                    "type"=> "action",
                                    "user"=> "alexis",
                                    "action"=> "vient de lancer la ligne de commande \"$ create docker:container && restart apache && npm run docker\"",
                                    "date"=> "2017-03-15 10:25:13"
                                );
        return response()->json(['success' => $user], $this->successStatus);
    }

    public function test() {
        $from = '2020-04-20';
        $to = '2020-05-20';

        $dates = CarbonPeriod::create($from, $to);

        $days = ['Friday'];
        // $days = ['Sunday', 'Monday', 'Friday'];
        $times = ["6:00PM","6:30PM","7:00PM","7:30PM"];

        $data = array();
        foreach($dates as $date) {
            $day = $date->format('l');
            $date = Carbon::parse($date);
            $date = $date->format('Y-m-d');

            if(in_array($day, $days)) {

                foreach($times as $time){
                    $data[] = array(
                        'date' => $date,
                        'day' => $day,
                        'time' => $time,
                    );
                }
            }
        }
        dd($data);

    }
}
