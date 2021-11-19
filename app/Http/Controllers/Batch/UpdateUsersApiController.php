<?php
namespace App\Http\Controllers\Batch;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Constants\LogClasses;
use App\Http\Controllers\Controller;
use App\DataModels\RelationalUsers;
use App\ViewModels\Batch\BatchLog;
use Illuminate\Http\Request;

class UpdateUsersApiController extends Controller
{
    public function execute(Request $request)
    {
        $token = $request['token'];
        if($token !== config('app.batch_token')){
            return response(status: 401);
        }

        $twitter_api = new TwitterOAuth(
            config('app.consumer_key'),
            config('app.consumer_secret'),
            config('app.access_token'),
            config('app.access_token_secret')
        );

        $log = [];
        array_push($log, new BatchLog(NOW(), LogClasses::START, "", "START PROCESS."));

        // Select target user id.
        $user_ids = array_column(
            RelationalUsers::select(['user_id'])
                ->orderBy('update_datetime', 'asc')
                ->take(5000)
                ->get()
                ->toArray(),
            'user_id');

        $users_count = count($user_ids);
        array_push($log, new BatchLog(NOW(), LogClasses::INFO, "Select user id", "Users at {$users_count}."));

        $chunk_user_ids = array_chunk($user_ids, 100);


        // Request to Twitter API.
        $response_users = [];
        $requested_user_ids = [];
        foreach ($chunk_user_ids as $user_ids) {

            $response = $twitter_api->post("users/lookup", [
                "user_id" => implode(",", $user_ids)
            ]);

            $status_code = $twitter_api->getLastHttpCode();

            array_push($log, new BatchLog(NOW(), LogClasses::INFO, "Request to Twitter API", "TWITTER API RESPONSE = {$status_code}."));

            if($status_code == 429) {
                continue;
            }

            if($status_code == 200) {
                array_push($log, new BatchLog(NOW(), LogClasses::INFO, "Request to Twitter API", "TWITTER API RESPONSE = 200."));
                $response_users = array_merge($response_users, $response);
            }

            $requested_user_ids = array_merge($requested_user_ids, $user_ids);

        }

        // Update users.
        array_push($log, new BatchLog(NOW(), LogClasses::INFO, "Update users", "relational_usersの更新を開始します。"));
        $responded_user_ids = array_column($response_users, "id_str");
        foreach ($requested_user_ids as $requested_user_id) {

            $index = array_search($requested_user_id, $responded_user_ids);
            if($index === false){

                RelationalUsers::where('user_id', $requested_user_id)
                    ->update(
                        [
                            'icecream' => 0,
                            'icecream_datetime' => null,
                            'verify_datetime' => null,
                            'not_found' => 1
                        ]
                    );
                array_push($log, new BatchLog(NOW(), LogClasses::INFO, "Update users", "{$requested_user_id} was not found."));

            }else{

                RelationalUsers::where('user_id', $requested_user_id)
                    ->update(
                        [
                            'disp_name' => $response_users[$index]->screen_name,
                            'name' => $response_users[$index]->name,
                            'description' => $response_users[$index]->description,
                            'theme_color' => $response_users[$index]->profile_link_color,
                            'follow_count' => $response_users[$index]->friends_count,
                            'follower_count' => $response_users[$index]->followers_count,
                            'location' => $response_users[$index]->location,
                            'icecream' => 0,
                            'icecream_datetime' => null,
                            'verify_datetime' => null,
                            'not_found' => 0,
                            'protected' => $response_users[$index]->protected ? 1 : 0
                        ]
                    );
                array_push($log, new BatchLog(NOW(), LogClasses::INFO, "Update users", "{$requested_user_id} was updated."));

            }
        }

        array_push($log, new BatchLog(NOW(), LogClasses::END, "", "END PROCESS."));
        return response()->json($log);
    }
}
