<?php
namespace App\Http\Controllers\Batch;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Controllers\Controller;
use App\DataModels\RelationalUsers;
use Illuminate\Http\Request;

class UserRepairApiController extends Controller
{
    public function execute(Request $request)
    {
        $token = $request['token'];
        if($token !== "folloWcheck_BatCh_01092123"){
            return response(status: 404);
        }

        $updatableUserIds = RelationalUsers::select(['user_id'])
            ->where('disp_name', '<>', '　')
            ->where('disp_name', '<>', 'wait...')
            ->orderBy('update_datetime', 'asc')
            ->take(400)
            ->get()
            ->toArray();

        $repairableUserIds = RelationalUsers::select(['user_id'])
            ->where('disp_name', '　')
            ->whereOr('disp_name', 'wait...')
            ->orderBy('update_datetime', 'asc')
            ->take(100)
            ->get()
            ->toArray();

        $twitter_api = new TwitterOAuth(
            config('app.consumer_key'),
            config('app.consumer_secret'),
            config('app.access_token'),
            config('app.access_token_secret')
        );

        $user_ids = array_merge(
            array_column( $updatableUserIds, 'user_id'),
            array_column( $repairableUserIds, 'user_id')
        );

        foreach ($user_ids as $user_id) {

            $response = $twitter_api->get("users/show", [
                "user_id" => $user_id
            ]);

            if (! property_exists($response, 'id_str')) {
                RelationalUsers::where('user_id', $user_id)
                    ->update(['not_found' => 1]);
                continue;
            }

            RelationalUsers::where('user_id', $user_id)
                ->update(
                    [
                        'disp_name' => $response->screen_name,
                        'name' => $response->name,
                        'description' => $response->description,
                        'follow_count' => $response->friends_count,
                        'follower_count' => $response->followers_count,
                        'protected' => $response->protected,
                        'not_found' => 0
                    ]
                );
        }

        return response()->json("SUCCESS!");
    }
}
