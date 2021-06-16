<?php

namespace App\Plugins\Twitter\Controllers;

use App\Plugins\Twitter\Model\TwitterApp;
use App\Plugins\Twitter\Controllers\Twitter;
use App\Plugins\Twitter\Model\TwitterChannel;

class ReplyToTwitter 
{
    private $twitter;

    /**
     * Sets the Twitter OAUTH Instance
     * @param array $settings
     * @return void
     */
    private function TwitterInit()
    {
        $credentials = $this->getCredentials(false);
        if($credentials) {

            $settings = [
                'oauth_access_token' => $credentials['access_token'],
                'oauth_access_token_secret' => $credentials['access_token_secret'],
                'consumer_key' => $credentials['consumer_api_key'],
                'consumer_secret' => $credentials['consumer_api_secret']
            ];

        }
        $this->twitter = new Twitter($settings);
    }


    /**
     * retrieves the twitter credentials if it is registerd or error response otherwise.
     * @param boolean $json
     * @return mixed
     */
    private function getCredentials($json=true)
    {
        $credentials = TwitterApp::first();
        if($credentials) 
            return ($json) ? successResponse($credentials->toArray()) : $credentials->toArray();
        return errorResponse([]);
    }

    /**
     * Returns the ticket id to which  a tweet/message belongs to if it already exists
     * @param mixed $ticketid
     * @param string $searchString
     * @return TwitterChannel
     */
    private function getChannel($ticketid,$searchString) {
        return ($searchString) ? TwitterChannel::where([
            'ticket_id' => $ticketid,
            'body'  => $searchString
        ])->first() : TwitterChannel::where('ticket_id',$ticketid)->first();
    }
    
    /**
     * Replies to ticket raised from twitter, on twitter
     * @param array $data
     * @return void
     */
    public function replyTwitter($data) {

        $searchString = '';
        $ticketid = $data['ticket_id'];
        $string = $data['body'];
        $content = strip_tags(str_replace("&nbsp;", "", $string));
        $str_pos = strrpos($content,':');
        if($str_pos) {
            $searchString = ltrim(substr($content,$str_pos),":");
        }

        $channel = $this->getChannel($ticketid,($searchString) ?: '');

        if ($channel && $channel->channel == 'twitter') {
            $this->TwitterInit();
            $ticketid = $data['ticket_id'];
            $string = $data['body'];
            $content = strip_tags(str_replace("&nbsp;", "", $string));
            $channel = $channel;
            $user_id = $channel->user_id;
            $user = $channel->username;
            $username = "@$user";
            $msgid = $channel->message_id;
            $body = $username . " " . $content;
            if ($channel->via == 'tweet' || $channel->via == 'mention') {
                $this->replyTweet($msgid, $body);
            }

            $this->replyMessage($user_id, $user, $content);
        }
    }

    /**
     * Replies to twitter tweet
     * @param mixed $messageid
     * @param mixed $content
     * @return void
     */
    private function replyTweet($messageid, $content) {
        $postdata = [
            'status' => str_replace("On","\r\nOn",$content),
            'in_reply_to_status_id' => $messageid,
        ];
        $url = "https://api.twitter.com/1.1/statuses/update.json";
        $tweetBack = $this->twitter->setPostfields($postdata)
                ->buildOauth($url, 'POST')
                ->performRequest();
    }

    /**
     * Replies to twitter message
     * @param mixed $userId
     * @param mixed $content
     * @param mixed $userName
     * @return void
     */
    private function replyMessage($userId, $userName, $content) {
       
        $data = [
            'event' => [
                'type' => 'message_create',
                'message_create' => [
                    'target' => [
                        'recipient_id' => $userId
                    ],
                    'message_data' => [ 
                        'text' => str_replace("On","\r\nOn",$content)
                    ]
                ]
            ]
        ];

        $url = "https://api.twitter.com/1.1/direct_messages/events/new.json";

        $messageBack = $this->twitter->buildOauth($url, 'POST')->performRequest(true, [
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);
    }


}