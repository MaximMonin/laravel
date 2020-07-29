<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Swift_Events_SendListener;
use Swift_Events_SendEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\User;

class RegisterTest extends DuskTestCase
{
    protected $user;
    protected $faker;
    protected $emails = [];

    public function testRegisterForm()
    {
/*
        $this->setUpMailTracking();
*/
        $this->faker = \Faker\Factory::create();
        $this->user = [ 'name' => $this->faker->name,
                        'email' => $this->faker->unique()->safeEmail,
                        'phone' => $this->faker->numerify('380#########')];

        $this->browse(function (Browser $browser) {
           $browser->logout()
                    ->visit('/')
                    ->clickLink (__('Register'))
                    ->assertPathIs('/register')
                    ->type('name', $this->user['name'])
                    ->type('email', $this->user['email'])
                    ->type('phone', $this->user['phone'])
                    ->type('password', 'testpassword')
                    ->type('password_confirmation', 'testpassword')
                    ->press(__('Register'))
                    ->assertPathIs('/email/verify')
                    ->click('@user-button')
                    ->screenshot('register-verify');

        });
        $users = User::where('email', '=', $this->user['email'])->first();
        $this->assertNotEmpty( $users, 'No user created in database');

        $notification = DB::table('notifications')->where('type' , 'App\Notifications\EmailVerification')
                                                  ->where('notifiable_id', $users->id)
                                                  ->latest()->first();
        $this->assertNotEmpty( $notification, 'No notification created by App/Notification/EmailVerification');
        $data = json_decode($notification->data);
        $this->assertNotEmpty( $data->verifylink, 'No verify link provided by App/Notification/EmailVerification');

        $this->browse(function (Browser $browser2) use ($data) {
            $browser2->visit($data->verifylink)
                     ->assertPathIs('/home')
                     ->click('@user-button')
                     ->screenshot('register-success')
                     ->click('@logout-button');
        });
        DB::table('notifications')->where('id' , $notification->id)->delete();
        $users->delete();

/* Email verification doesnt work with Laravel Dusk 
        $this->seeEmailWasSent()
             ->seeEmailsSent(1)
             ->seeEmailContains(config('app.name'))
             ->seeEmailContains(__("Verify Email Address"))
             ->seeEmailTo ($this->user-email)
             ->seeEmailSubject(__("Verify Email Address"));
*/

    }

    public function setUpMailTracking()
    {     
        Mail::getSwiftMailer()->registerPlugin(new TestingMailEventListener($this));
    }

    /**
     * Assert that at least one email was sent.
     */
    protected function seeEmailWasSent()
    {
        $this->assertNotEmpty(
            $this->emails, 'No emails have been sent.'
        );

        return $this;
    }


    /**
     * Assert that the given number of emails were sent.
     *
     * @param integer $count
     */
    protected function seeEmailsSent($count)
    {
        $emailsSent = count($this->emails);

        $this->assertCount(
            $count, $this->emails,
            "Expected $count emails to have been sent, but $emailsSent were."
        );

        return $this;
    }

    /**
     * Assert that the last email's body contains the given text.
     *
     * @param string        $excerpt
     * @param Swift_Message $message
     */
    protected function seeEmailContains($excerpt, Swift_Message $message = null)
    {
        $this->assertContains(
            $excerpt, $this->getEmail($message)->getBody(),
            "No email containing the provided body was found."
        );

        return $this;
    }

    /**
     * Assert that the last email's subject matches the given string.
     *
     * @param string        $subject
     * @param Swift_Message $message
     */
    protected function seeEmailSubject($subject, Swift_Message $message = null)
    {
        $this->assertEquals(
            $subject, $this->getEmail($message)->getSubject(),
            "No email with a subject of $subject was found."
        );

        return $this;
    }

    /**
     * Assert that the last email was sent to the given recipient.
     *
     * @param string        $recipient
     * @param Swift_Message $message
     */
    protected function seeEmailTo($recipient, Swift_Message $message = null)
    {
        $this->assertArrayHasKey(
            $recipient, (array) $this->getEmail($message)->getTo(),
            "No email was sent to $recipient."
        );

        return $this;
    }

    /**
     * Assert that the last email was delivered by the given address.
     *
     * @param string        $sender
     * @param Swift_Message $message
     */
    protected function seeEmailFrom($sender, Swift_Message $message = null)
    {
        $this->assertArrayHasKey(
            $sender, (array) $this->getEmail($message)->getFrom(),
            "No email was sent from $sender."
        );

        return $this;
    }

    /**
     * Store a new swift message.
     *
     * @param Swift_Message $email
     */
    public function addEmail(Swift_Message $email)
    {
        $this->emails[] = $email;
    }

    /**
     * Retrieve the appropriate swift message.
     *
     * @param Swift_Message $message
     */
    protected function getEmail(Swift_Message $message = null)
    {
        $this->seeEmailWasSent();

        return $message ?: $this->lastEmail();
    }

    /**
     * Retrieve the mostly recently sent swift message.
     */
    protected function lastEmail()
    {
        return end($this->emails);
    }
}

class TestingMailEventListener implements Swift_Events_SendListener
{
    /**
     * @var Swift_Mime_SimpleMessage[]
     */
    private $messages;
    protected $test;

    public function __construct($test)
    {
        $this->test = $test;
        $this->messages = [];
    }

    /**
     * Get the message list.
     *
     * @return Swift_Mime_SimpleMessage[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Get the message count.
     *
     * @return int count
     */
    public function countMessages()
    {
        return count($this->messages);
    }

    /**
     * Empty the message list.
     */
    public function clear()
    {
        $this->messages = [];
    }

    /**
     * Invoked immediately before the Message is sent.
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $this->messages[] = clone $evt->getMessage();
        $this->test->addEmail($evt->getMessage());
    }

    /**
     * Invoked immediately after the Message is sent.
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
    }
}

