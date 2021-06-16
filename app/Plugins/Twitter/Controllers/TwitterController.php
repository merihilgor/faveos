<?php

namespace App\Plugins\Twitter\Controllers;

use Logger;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use App\Model\MailJob\Condition;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use App\Plugins\Twitter\Model\TwitterApp;
use App\Plugins\Twitter\Controllers\Twitter;
use App\Plugins\Twitter\Model\TwitterChannel;
use App\Plugins\Twitter\Requests\TwitterAppRequest;
use App\Plugins\Twitter\Controllers\Core\SettingsController;
use App\Plugins\Twitter\Model\TwitterHashtags;

class TwitterController extends Controller
{

    private $twitter;
    private $dmTestUrl = "https://api.twitter.com/1.1/direct_messages/events/list.json";
    private $twUrl     = "https://api.twitter.com/1.1/search/tweets.json";
    private $dmUrl     = "https://api.twitter.com/1.1/direct_messages.json";
    private $mentionUrl ="https://api.twitter.com/1.1/statuses/mentions_timeline.json";
    private $settings;


    public function __construct()
    {
        $this->settings = new SettingsController;
        
    }

    /**
     * Sets the Twitter OAUTH Instance
     * @param array $settings
     * @return void
     */
    private function TwitterInit($settings)
    {
        try {
            $this->twitter = new Twitter($settings);
            return ($this->twitter) ? true : false;
        } catch(\Exception $e) {
            Logger::exception($e->getMessage());
            return false;
        }
    }

    /**
     * Checks whether the twiter instance is set and sets it.
     * @param void
     * @return void
     */
    private function DoTwitter()
    {
        $credentials = $this->getCredentials(false);
        if($credentials && is_array($credentials)) {

            $settings = [
                'oauth_access_token' => $credentials['access_token'],
                'oauth_access_token_secret' => $credentials['access_token_secret'],
                'consumer_key' => $credentials['consumer_api_key'],
                'consumer_secret' => $credentials['consumer_api_secret']
            ];

            return $this->TwitterInit($settings);
        }
        else {
            return false;
        }
    }


    /**
     * retrieves the twitter credentials if it is registerd or error response otherwise.
     * @param boolean $json
     * @return mixed
     */
    public function getCredentials($json=true)
    {
        $credentials = TwitterApp::first();
        if($credentials) 
            return ($json) ? successResponse($credentials->toArray()) : $credentials->toArray();
        return errorResponse([]);
    }

    /**
     * Registers the twitter application
     * @param Request $request
     * @return Response
     */
    public function createApp(TwitterAppRequest $request)
    {
        
        $hashtagArray = [];
        $cron_bit = $request->cron_confirm;
        $hashtag_texts = $request->hashtag_text;
        $request->request->remove('cron_confirm');
        $request->request->remove('hashtag_text');
        // $request->merge(['hashtag_text' => json_encode($hashtag_text)]);

        
        $settings = [
            'oauth_access_token' => trim($request->access_token),
            'oauth_access_token_secret' => trim($request->access_token_secret),
            'consumer_key' => trim($request->consumer_api_key),
            'consumer_secret' => trim($request->consumer_api_secret)
        ];

        $this->TwitterInit($settings);

        $response = json_decode($this->twitter->setGetfield("")
                    ->buildOauth($this->dmTestUrl, "GET")
                    ->performRequest(
                        true,array(
                            CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0
                        )),$assoc = TRUE
                    );

        if(array_key_exists("errors",$response)) {
            return errorResponse($response['errors'][0]['message']);
        }

        else {
            $app = TwitterApp::create(
                $request->only(
                    [
                        'consumer_api_key','consumer_api_secret',
                        'access_token','access_token_secret',
                        'reply_interval'
                    ]
                )
            );
            if($app) {

                foreach($hashtag_texts as $k => $v) {
                    array_push($hashtagArray,[
                        'app_id' => $app->id,
                        'hashtag' => $v['name']
                    ]);
                }
                $this->fillHashTags($hashtagArray);
                $this->changeCondition($cron_bit);
                return successResponse(trans('Twitter::lang.app_created'));
            } 
            return errorResponse(trans('Twitter::lang.create_error'));
        }

    }


    private function fillHashTags($hashtagArray)
    {
        try{
            TwitterHashtags::insert($hashtagArray);
        } catch(\Exception $e) {
            Logger::exception($e);
        }
    }

    /**
     * Deletes the twitterApp
     * @param integer id
     * @return void
     */
    public function deleteApp($id)     
    {
        $twitter = TwitterApp::find($id);
        try {
            if($twitter->hashtags()) {
                $twitter->hashtags()->delete();
            }
            $deleted = $twitter->delete();
        } catch(\Exception $e) {
            Logger::exception($e);
        }

        return ($deleted) ? successResponse(trans('Twitter::lang.deleted-app')) : errorResponse(trans('Twitter::lang.not_deleted'));
    }

    /**
     * Fetches tickets from twitter direct messages(dm)
     */
    public function getMessages()
    {
        if(!$this->DoTwitter())
            return;
        $since_id = ($this->getSinceIdForTwitter('message')) ?: '0';
        $messages = $this->twitter->setGetfield('since_id=' . $since_id)
                  ->buildOauth($this->dmTestUrl, 'GET')
                  ->performRequest(true,array(CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0));
        $messagesArray = json_decode($messages,true);
        if(isset($messagesArray['events']) && $messagesArray['events'])
            $this->createTicket($this->formatMessages($messagesArray['events']),"message");
    }

    /**
     * Fetches tickets from twitter tweets.
     */
    public function getTweets()
    {
        if(!$this->DoTwitter())
            return;
        $since_id = ($this->getSinceIdForTwitter('tweet')) ?: '0';
        $hashtags = TwitterHashtags::where('app_id',$this->getCredentials(false)['id'])->get()->toArray();
        foreach($hashtags as $k => $v) {
            $tweets = $this->twitter->setGetfield('q=' . $v['hashtag'] . '&since_id=' . $since_id)
                ->buildOauth($this->twUrl, 'GET')
                ->performRequest(true,array(CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0));
            $tweetsArray = json_decode($tweets,true);    
            if(isset($tweetsArray['statuses']) && $tweetsArray['statuses'])
                $this->createTicket($this->formatTweets($tweetsArray['statuses'],$v['hashtag']),"tweet");
        }
        
    }


    /**
     * Gets the last message id which was recently fetched by twitter
     * @param string $type
     * @return sting #since_id
     */
    private function getSinceIdForTwitter($type)
    {
        $id = "";
        $channels = new \App\Plugins\Twitter\Model\TwitterChannel();
        $channel = $channels->where('channel', 'twitter')->where('via',$type)->orderBy('posted_at', 'desc')->first();
        if ($channel) {
            $id = $channel->message_id;
        }
        return $id;
    }

    private function getUser($userId)
    {
        if(!$this->DoTwitter())
            return;
        $user = $this->twitter->setGetfield("user_id=$userId")
                ->buildOauth("https://api.twitter.com/1.1/users/lookup.json", 'GET')
                ->performRequest(true,array(CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0));
        return json_decode($user,true)[0];        
    }

    /**
     * Formats the tweet to saveble format
     * @param array $tweets
     * @return array $fields
     */
    private function formatTweets($tweets,$hashtag)
    {
        
        $fields = array();
       foreach($tweets as $message) {
            if(empty($message['entities']['hashtags']))
                continue;
            else if($message['entities']['hashtags'][0]['text'] != $hashtag) 
                continue;
            else if(isset($message['in_reply_to_status_id']) || isset($message['in_reply_to_screen_name']))
                continue;
            $field['channel'] = 'twitter';
            $field['name'] = $message['user']['name'];
            $field['user_id'] = $message['user']['id'];
            $field['username'] = $message['user']['screen_name'];
            $field['email'] = $message['user']['screen_name']."@twitter.com";
            $field['message'] = trim($message['text']);
            $field['posted_at'] = $this->formatTimezone($message['created_at']);
            $field['message_id'] = $message['id'];
            $field['via'] = "tweet";
            $field['page_access_token'] = "N/A";
            array_push($fields,$field);
       }
       return $fields;
    }


    /**
     * Formats the messages to saveble format
     * @param array $messages
     * @return array $fields
     */
    private function formatMessages($messages)
    {
        $fields = array();
        foreach($messages as $message) {
            if(isset($message['message_create']['source_app_id']))
                continue; //skips fetching the reply made by us as tickets.
            
            $field['channel'] = 'twitter';
            $field['name'] = $this->getUser($message['message_create']['sender_id'])['name'];
            $field['user_id'] = $message['message_create']['sender_id'];
            $field['username'] = $this->getUser($message['message_create']['sender_id'])['screen_name'];
            $field['email'] = "N/A";
            $field['message'] = trim($message['message_create']['message_data']['text']);
            
            //twitter returns the message created time as epoch timestamp that is converted to Unix timestamp as below.
            $field['posted_at'] = $this->formatTimezone(date("Y-m-d h:i:s",substr($message['created_timestamp'], 0, 10)));
            $field['message_id'] = $message['id'];
            $field['via'] = "message";
            $field['page_access_token'] = "N/A";

            array_push($fields,$field);
        }
       return $fields;
    }

    /**
     * Formats the tweet/message created time to agent timezone
     * @param string $dateTime
     * @return string 
     */
    private function formatTimezone($dateTime)       
    {
        $dt = new DateTime($dateTime, new DateTimeZone('UTC'));
        
        $dt->setTimezone(new DateTimeZone(timezone()));
        
        return $dt->format('Y-m-d H:i:s');
    }

    /**
     * Initiates the ticket controller
     * @param void
     * @return void
     */
    private function ticketController() {
        $PhpMailController = new \App\Http\Controllers\Common\PhpMailController();
        $NotificationController = new \App\Http\Controllers\Common\NotificationController();
        $ticket_controller = new \App\Http\Controllers\Agent\helpdesk\TicketController($PhpMailController, $NotificationController);
        return $ticket_controller;
    }


    /**
     * Creates the new ticket through twitter 
     * @param array $social_fields
     * @param string $type
     * @return void
     */
    private function createTicket($social_fields, $type) {
        $subject = ucfirst($type)." from Twitter";
        $provider = "twitter";
        $via = $type;
        $result = "";
        $ticket_controller = $this->ticketController();
        if (count($social_fields) > 0) {
            foreach ($social_fields as $social_field) {
                
                $username = $social_field['username'];
                $userid = $social_field['user_id'];
                $body = $social_field['message'];
                $priority = $this->settings->getSystemDefaultPriority();
                $department = $this->settings->getSystemDefaultDepartment();
                $helptopic = $this->settings->getSystemDefaultHelpTopic();
                $phone = "";
                $phonecode = "";
                $mobile = "";
                $source = $ticket_controller->getSourceByname($provider)->id;
                $headers = [];
                $assignto = NULL;
                $from_data = [];
                $auto_response = "";
                $status = "";
                $sla = "";
                $priority = "";
                $type = (\App\Model\helpdesk\Manage\Tickettype::select('id')->first()) ?: "";
                if(!$this->itemExists($social_field['message_id'],$via)) {
                    if ($this->checkReply($userid,$via,$social_field['posted_at'])) {
                        $result = $this->reply($provider, $via, $body, $userid);
                        $this->insertInfoReply($social_field,$via,$result->ticket_id);
                    }
    
    
                    else {
    
                        $result = $ticket_controller->create_user($username, $username, $subject, 
                        $body, $phone, $phonecode, $mobile, $helptopic, $sla, $priority, $source, 
                        $headers, $department, $assignto, $from_data, $auto_response, $status, $type);
                        if (is_array($result)) 
                            $this->insertInfo($social_field, $result, $provider, $via);
                    }
                }

            } //foreach
        }
        \Log::info("Twitter messages are read by the system");
    }

    /**
     * Checks whether a tweet/message already exists as aticket
     * @param string $item
     * @param string $type
     * @return boolean
     */
    private function itemExists($item,$type)
    {
        $itemExists = TwitterChannel::where([
            'via'        => $type,
            'message_id' => $item
        ])->first();
        
        return ($itemExists) ? true :false;
    }

    /**
     * Saves the reply related information
     * @param array $info
     * @param mixed $via
     * @param mixed $ticket_id
     * @return void
     */
    private function insertInfoReply($info, $via, $ticket_id) 
    {
        $users = $this->settings->getUser($info);
        $array['channel'] = "twitter";
        $array['via'] = $via;
        $array['message_id'] = $this->settings->checkArray('message_id', $info);
        $array['body'] = $info['message'];
        $array['user_id'] = $this->settings->checkArray('user_id', $users);
        $array['ticket_id'] = $ticket_id;
        $array['username'] = $this->settings->checkArray('username', $users);
        $array['posted_at'] = $this->settings->checkArray('posted_at', $info);
        $this->updateSocialChannel($array);
    }

    /**
     * Saves the ticket related information for further processing and keeping track of conversations
     * @param array $info
     * @param mixed $via
     * @param mixed $ticket_id
     * @return void
     */
    private function insertInfo($info, $result, $provider, $via) {
        $userid = $this->findUserFromTicketCreateUserId($result);
        $PhpMailController = new \App\Http\Controllers\Common\PhpMailController();
        $guest_controller = new \App\Http\Controllers\Client\helpdesk\GuestController($PhpMailController);
        $user = $this->settings->getUser($info);
        $guest_controller->update($userid, $user, $provider);
        $this->socialChannel($info, $result, $provider, $via);
    }

    /**
     * Add message/tweet as a reply to the existing ticket.
     * @param mixed $provider
     * @param mixed $via
     * @param mixed $body
     * @param mixed $userid
     * @return mixed
     */
    private function reply($provider, $via, $body, $userid) {
        $ticket_id = $this->getTicketIdForReply($provider, $via, $userid);
        $ticket_user_id = $this->ticketUserId($userid);
        $ticket_controller = $this->ticketController();
        $result = $ticket_controller->saveReply($ticket_id, $body, $ticket_user_id, "", [], [], false, 'client');
        return $result;
    }
    
    /**
     * Gets the user who created the ticket
     * @param mixed $twitter_userid
     * @return mixed $id
     */
    private function ticketUserId($twitter_userid){
        $user_name = "";
        $twitter_channel = new \App\Plugins\Twitter\Model\TwitterChannel();
        $channel = $twitter_channel->where('user_id',$twitter_userid)->select('id','username')->first();
        if($channel){
           $user_name = $channel->username; 
        }
        $user = \App\User::where('user_name',$user_name)->value('id');
        $id = ($user)?$user:"";
        return $id;
    }

    /**
     * Incase of Replies, this method gets ticketid for which the msg/tweet must be added as a reply.
     * @param mixed $provider
     * @param mixed $via
     * @param mixed $userid
     * @return mixed
     */
    private function getTicketIdForReply($provider, $via, $userid) {

        $channel = TwitterChannel::where([
            'channel' => $provider,
            'via'     => $via,
            'user_id' => $userid,
            'hasExpired' => 0
        ])->first();
        if ($channel) 
            return $channel->ticket_id;
        
    }

    /**
     * Checks whether the msg/tweet is a reply to the existing ticket
     * @param mixed $via
     * @param mixed $userid
     * @return boolean
     */
    private function checkReply($userId,$via,$posted_at) {

        $check = false;
        $isReply = TwitterChannel::where([
            'user_id'    => $userId,
            'via'        => $via,
            'hasExpired' => 0
        ])->first();
        $reply_interval = TwitterApp::first()->toArray()['reply_interval'];
        
        if ($isReply) {
            if(strtotime($posted_at) < strtotime("+$reply_interval day",strtotime($isReply->posted_at)))
                $check = true;
            else {
                TwitterChannel::where('ticket_id',$isReply->ticket_id)
                              ->update(['hasExpired' => 1]);
            }    
        }
        
        return ($check) ? true :false;

    }

    /**
     * Formats the message/tweet information to save for further processing
     * @param array $info
     * @param mixed $via
     * @param mixed $provider
     * @param array $result
     * @return void
     */
    private function socialChannel($info, $result, $provider, $via) {
        $users = $this->settings->getUser($info);
        $array['channel'] = $provider;
        $array['via'] = $via;
        $array['message_id'] = $this->settings->checkArray('message_id', $info);
        $array['body'] = $info['message'];
        $array['user_id'] = $this->settings->checkArray('user_id', $users);
        $array['ticket_id'] = $this->lastTicket($result);
        $array['username'] = $this->settings->checkArray('username', $users);
        $array['posted_at'] = $this->settings->checkArray('posted_at', $info);
        $this->updateSocialChannel($array);
    }

    /**
     * Gets the ID of the last ticket created by the user
     * @param array $result
     * @return mixed $ticket_id
     */
    private function lastTicket($result) {
        $ticket = $this->findTicketFromTicketCreateUser($result);
        if ($ticket) {
            $ticket_id = $ticket->id;
            return $ticket_id;
        }
    }

    /**
     * Saves the message information locally for further computations
     * @param array $array
     * @return void
     */
    private function updateSocialChannel($array) {
        $twitter_channel = new \App\Plugins\Twitter\Model\TwitterChannel();
        $twitter_channel->create($array);    
    }

    /**
     * Gets the ticket information 
     * @param array $result
     * @return mixed $ticket
     */
    private function findTicketFromTicketCreateUser($result = []) {
        $ticket_number = $this->settings->checkArray('0', $result);
        if ($ticket_number !== "") {
            $tickets = new \App\Model\helpdesk\Ticket\Tickets();
            $ticket = $tickets->where('ticket_number', $ticket_number)->first();
            if ($ticket) {
                return $ticket;
            }
        }
    }

    /**
     * Gets the ID of the user, the ticket belongs to
     * @param array $result
     * @return mixed $userid
     */
    private function findUserFromTicketCreateUserId($result = []) {
        $ticket = $this->findTicketFromTicketCreateUser($result);
        if ($ticket) {
            $userid = $ticket->user_id;
            return $userid;
        }
    }


    /**
     * Changes the cron settings for Twitter plugin
     * @param integer $enable
     * @return Response
     */
    private function changeCondition($enable)
    {
        $condition = Condition::where('job','twitter')->first();
        $condition->active = $enable;
        $condition->save();
    }

    /**
     * Checks whether the cron is set for fetching tickets from twitter plugin
     * @param void
     * @return Response
     */
    public function checkCondition()
    {
        $condition = Condition::where('job','twitter')->first();
        return successResponse('',['active'=>$condition->active]);
    }


    /**
     * Gets the tweets from mentions timeline
     */
    public function getMentionTweets()
    {
        if(!$this->DoTwitter())
            return;
        $since_id = ($this->getSinceIdForTwitter('mention')) ?: '';
        $getfield = ($since_id) ? "?since_id=$since_id" : "";
        $tweets = $this->twitter->setGetfield($getfield)
                ->buildOauth($this->mentionUrl, 'GET')
                ->performRequest(true,array(CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0));
        $this->createTicket($this->formatMentions(json_decode($tweets,true)),"mention");
    }

    /**
     * Formats the tweet to saveble format
     * @param array $tweets
     * @return array $fields
     */
    private function formatMentions($tweets)
    {
        $fields = array();
        foreach($tweets as $message) {

            $field['channel'] = 'twitter';
            $field['name'] = $message['user']['name'];
            $field['user_id'] = $message['user']['id'];
            $field['username'] = $message['user']['screen_name'];
            $field['email'] = $message['user']['screen_name']."@twitter.com";
            $field['message'] = trim(preg_replace("/([@]+[\w_-]+)/",'',$message['text']));
            $field['posted_at'] = $this->formatTimezone($message['created_at']);
            $field['message_id'] = $message['id'];
            $field['via'] = "mention";
            $field['page_access_token'] = "N/A";
            array_push($fields,$field);
       }
       return $fields;
    }


    /**
     * Return the twitter App for Datatable
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function getTwitterApp(Request $request)
    {
        $query = TwitterApp::with('hashtags');

        $query->when((bool)($request->ids),function($q) use ($request){
            return $q->whereIn('id',$request->ids);
        });

        $query->when((bool)($request->search_query),function($q) use ($request){
            $q->where(function($q) use ($request){
                return $q->where('app_id','LIKE',"%$request->search_query%")
                ->orWhere('secret','LIKE',"%$request->search_query%");
            });
        });

        $apps = $query->orderBy((($request->sort_field) ? : 'updated_at'), (($request->sort_order) ? : 'asc'))
        ->paginate((($request->limit) ? : '10'))->toArray();

        $condition = Condition::where('job','twitter')->first();
        foreach($apps['data'] as $k => &$v) {
            $v['cron'] = $condition->active;
        }
        $apps['apps'] = $apps['data'];
        unset($apps['data']);
        return successResponse('',$apps);  
    }


    /**
     * Registers the twitter application
     * @param Request $request
     * @return Response
     */
    public function updateApp(TwitterAppRequest $request,$id)
    {
        $cron_bit = $request->cron_confirm;
        $hashtag_texts = $request->hashtag_text;
        $request->request->remove('cron_confirm');
        $request->request->remove('hashtag_text');
        $hashtagArray = [];
        $settings = [
            'oauth_access_token' => trim($request->access_token),
            'oauth_access_token_secret' => trim($request->access_token_secret),
            'consumer_key' => trim($request->consumer_api_key),
            'consumer_secret' => trim($request->consumer_api_secret)
        ];


        $this->TwitterInit($settings);

        $response = json_decode($this->twitter->setGetfield("")
                    ->buildOauth($this->dmTestUrl, "GET")
                    ->performRequest(
                        true,array(
                            CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0
                        )),$assoc = TRUE
                    );

        if(array_key_exists("errors",$response)) {
            return errorResponse($response['errors'][0]['message']);
        }

        else {
            $app = tap(TwitterApp::findOrFail($id))->update($request->only([
                'consumer_api_key','consumer_api_secret',
                'access_token','access_token_secret',
                'reply_interval'
            ]));
            if($app) {
                foreach($hashtag_texts as $k => $v) {
                    array_push($hashtagArray,[
                        'app_id' => $app->id,
                        'hashtag' => $v['name']
                    ]);
                }
                $this->updateHashTags($hashtagArray,$app->id);
                $this->changeCondition($cron_bit);
                return successResponse(trans('Twitter::lang.app_updated'));
            }

            return errorResponse(trans('Twitter::lang.update_err'));

        }

    }

    private function updateHashTags($hashtagArray,$id)
    {
        try{
            TwitterHashtags::where('app_id',$id)->delete();
            $this->fillHashTags($hashtagArray);
        } catch(\Exception $e) {
            Logger::exception($e);
        }
    }

    /**
     * Returns Settings View For Twitter Plugin
     * @return Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsView()
    {
        return view('twitter::settings');
    }

}