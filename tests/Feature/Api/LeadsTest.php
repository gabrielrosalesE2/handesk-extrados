<?php

namespace Tests\Feature;

use App\Authenticatable\Admin;
use App\Lead;
use App\Notifications\LeadCreated;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketCreated;
use App\Requester;
use App\Services\Mailchimp;
use App\Services\MailchimpFake;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeadsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void {
        parent::setUp(); // TODO: Change the autogenerated stub
        Notification::fake();
    }

    /** @test */
    public function can_create_a_lead(){
        $mailChimpFake = new MailchimpFake();
        app()->instance(Mailchimp::class, $mailChimpFake);

        $admin      = factory(Admin::class)->create();
        $nonAdmin   = factory(User::class)->create(["admin" => 0]);

        $response = $this->post('api/leads',[
            "email"       => "bruce@wayne.com",
            "body"        => "I'm interested in buying this awesome app",
            "username"    => "brucewayne",
            "name"        => "Bruce Wayne",
            "phone"       => "0044 456 567 54",
            "address"     => "Wayne manner",
            "city"        => "Gotham",
            "postal_code" => "90872",
            "company"     => "Wayne enterprises",
            "tags"        => ["email", "xef", "email"],
        ], ["token" => 'the-api-token']);

        $response->assertStatus(Response::HTTP_CREATED);

        tap(Lead::first(), function($lead) use($admin){
            $this->assertEquals("bruce@wayne.com",  $lead->email);
            $this->assertEquals("I'm interested in buying this awesome app", $lead->body);
            $this->assertEquals("brucewayne",       $lead->username);
            $this->assertEquals("Wayne enterprises",$lead->company);
            $this->assertEquals("Bruce Wayne",      $lead->name);
            $this->assertEquals("0044 456 567 54",  $lead->phone);
            $this->assertEquals("Wayne manner",     $lead->address);
            $this->assertEquals("Gotham",           $lead->city);
            $this->assertEquals("90872",            $lead->postal_code);
            $this->assertCount(2, $lead->tags);
            $this->assertTrue( $lead->tags->pluck('name')->contains("xef"));

            Notification::assertSentTo( [$admin], LeadCreated::class, function ($notification, $channels) use ($lead) {
                    return $notification->lead->id === $lead->id;
                }
            );
        });
        Notification::assertNotSentTo( [$nonAdmin], LeadCreated::class );
    }

    /** @test */
    public function a_new_lead_is_added_to_mailchimp(){
        $mailChimpFake = new MailchimpFake();
        app()->instance(Mailchimp::class, $mailChimpFake);

        $response = $this->post('api/leads',[
            "email"    => "bruce@wayne.com",
            "username" => "brucewayne",
            "name"     => "Bruce Wayne",
            "tags"     => ["email", "xef", "retail", "email"],
        ],["token" => 'the-api-token']);

        $response->assertStatus(Response::HTTP_CREATED);

        tap(Lead::first(), function($lead) use($mailChimpFake){
            $mailChimpFake->assertSubscribed($lead->email, config('services.mailchimp.tag_list_id.xef'));
            $mailChimpFake->assertSubscribed($lead->email, config('services.mailchimp.tag_list_id.retail'));
            $mailChimpFake->assertNotSubscribed($lead->email, config('services.mailchimp.tag_list_id.flow'));
        });
    }

    /** @test */
    public function creating_a_lead_that_email_already_exists_only_adds_the_tags(){
        $lead = factory(Lead::class)->create(["email" => "bruce@wayne.com"]);
        $response = $this->post('api/leads',[
            "email"    => "bruce@wayne.com",
            "username" => "brucewayne",
            "name"     => "Bruce Wayne",
            "tags"     => ["email", "xef", "retail", "email"],
        ],["token" => 'the-api-token']);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertEquals(1, Lead::count() );
        tap($lead->fresh()->tags()->pluck('name'), function($tags){
            $this->assertTrue( $tags->contains("retail") );
            $this->assertTrue( $tags->contains("email") );
            $this->assertTrue( $tags->contains("xef") );
        });
    }
}
