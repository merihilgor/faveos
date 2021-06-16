<?php

namespace App\Plugins\Twitter\tests\Backend;

use App\Http\Controllers\Agent\helpdesk\TicketController;
use Tests\AddOnTestCase;
use App\Plugins\Twitter\Model\TwitterApp;
use App\Plugins\Twitter\Controllers\Twitter;
use App\Plugins\Twitter\Controllers\TwitterController;
use App\Plugins\Twitter\Model\TwitterChannel;
use App\Plugins\Twitter\Model\TwitterHashtags;

class TwitterControllerTest extends AddOnTestCase {


public function test_getCredentials_Returns_SuccessJSonResponse_IfTwitterApp_Registered()
{
    $this->getLoggedInUserForWeb('admin');
    factory(TwitterApp::class,1)->create();
    $response = $this->call('GET','/twitter/credentials');
    $response->assertOk();

}

public function test_CreateApp_Returns_ErrorJsonResponse_WhenTriedToCreateApp_WithFakeTokens()
{
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST','/twitter/create',[
        'consumer_api_secret' => 'hgyffddgf',
        'consumer_api_key'    => 'ndkjhhdln',
        'access_token'        => 'rdfefres',
        'access_token_secret' => 'rjdojdj',
        'hashtag_text'        => 'dkdddkmfjfjdkjdjfe'
    ]);
    $response->assertStatus(400)->assertJsonFragment([
        'message' => 'Invalid or expired token.'
    ]);
    
}

public function test_DeleteApp_DeletesTheApp_ReturnsSuccessResponse()
{
    $this->getLoggedInUserForWeb('admin');
    $app = factory(TwitterApp::class,1)->create();
    $id = $app->toArray()[0]['id'];
    TwitterHashtags::create([
        'app_id' => $id,
        'hashtag' => 'hashtagss'
    ]);
    $response = $this->call('DELETE','/twitter/delete/'.$id);
    $response->assertOk()->assertJsonFragment([
        'message' => 'Twitter App successfully deleted.',
    ]);
    $this->assertDatabaseMissing('twitter_hashtags',[
        'hashtag' => 'hashtags'
    ]);
}

public function test_CreateTicketMethod_CreatesTicket()
{
    $fields = [
        [
            "channel" => "twitter",
            "name" => "Praj",
            "user_id" => "123",
            "username" => "PrajNu",
            "email" => "N/A",
            "message" => "message",
            "posted_at" => date("Y-m-d H:i:s"),
            "message_id" => "MSG1076",
            "via" => "message",
            "page_access_token" => "N/A"

        ]
    ];
    $expected = ['message_id' => "MSG1076",'username' => 'PrajNu'];
    $app = factory(TwitterApp::class,1)->create();
    $tw = new TwitterController();
    $this->assertDatabaseMissing('twitter_channel',$expected);
    $this->getPrivateMethod($tw,'createTicket',[$fields,"message"]);
    $this->assertDatabaseHas('twitter_channel',$expected);
    $this->assertDatabaseHas('ticket_thread',['title' => "Message from Twitter"]);
}

public function test_getSinceIdFortwitter_getsTheLastMessageId()
{
    
    $app = factory(TwitterChannel::class,1)->create();
    $tw = new TwitterController();
    $x = $this->getPrivateMethod($tw,'getSinceIdForTwitter',["tweet"]);
    $this->assertEquals("1198529298426028032",$x);
}

public function test_ticketController_InstantiatesTicketControllerClass()
{
    $tw = new TwitterController();
    $x = $this->getPrivateMethod($tw,'ticketController',[]);
    $this->assertInstanceOf(TicketController::class,$x);
}

public function test_itemExists_ChecksWhether_MessageAlreadyExists()
{
    $app = factory(TwitterChannel::class,1)->create();
    $tw = new TwitterController();
    $x = $this->getPrivateMethod($tw,'itemExists',["1198529298426028032","tweet"]);
    $this->assertTrue($x);
}

public function test_checkReplyMethod_ChecksWhetherTheNextMessageIsReplyOrNot()
{
    $app = factory(TwitterApp::class,1)->create();
    $channel = factory(TwitterChannel::class,1)->create();
    $tw = new TwitterController();
    $x = $this->getPrivateMethod($tw,'checkReply',["8959u7","tweet",'2019-11-19 14:40:26']);
    $this->assertTrue($x);
}


}