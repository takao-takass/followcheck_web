<?php

namespace App\Models;

/**
 * ツイートリストの条件モデル
 */
class TweetListFilter
{
    // 特定条件
    public $service_user_id;
    public $user_id;
    public $group_id;

    // 絞り込み条件
    public $onreply;
    public $onretweet;
    public $onlymedia;
    public $onkeep;
    public $onunkeep;
    public $onunchecked;

    // ページング条件
    public $page;

    // コンストラクタ
    function __construct(
        $service_user_id, $user_id, $group_id, $page,
        $onreply, $onretweet, $onlymedia, $onkeep, $onunkeep, $onunchecked)
    {
        $this->service_user_id = $service_user_id;
        $this->user_id = $user_id;
        $this->group_id = $group_id;
        $this->page = $page;
        $this->onreply = $onreply;
        $this->onretweet = $onretweet;
        $this->onlymedia = $onlymedia;
        $this->onkeep = $onkeep;
        $this->onunkeep = $onunkeep;
        $this->onunchecked = $onunchecked;
    }
}
