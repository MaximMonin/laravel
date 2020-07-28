<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Swift_Events_EventListener;
use Illuminate\Support\Facades\Mail;

class RegisterTest extends DuskTestCase
{
    protected $user;
    protected $faker;
    protected $emails = [];

    public function testRegisterForm()
    {
        $this->setUpMailTracking();
        $this->faker = \Faker\Factory::create();
        $this->user = [ 'name' => $this->faker->name,
                        'email' => $this->faker->unique()->safeEmail,
                        'phone' => $this->faker->numerify('############')];

        $this->browse(function (Browser $browser) {
           $browser->visit('/')
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
//                    ->click(__('messages.Profile'))
//                    ->assertPathIs('/email/verify');

        });

        $this->seeEmailWasSent()
             ->seeEmailsSent(1)
             ->seeEmailContains(config('app.name'))
             ->seeEmailContains(__("Verify Email Address"))
             ->seeEmailTo ($this->user-email)
             ->seeEmailSubject(__("Verify Email Address"));

    }

    public function setUpMailTracking()
    {     
        Mail::getSwiftMailer()->registerPlugin(new TestingMailEventListener($this));

//        $mailer = $app->make(Mailer::class);
//        $mailer->registerPlugin(new TestingMailEventListener($this));
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

class TestingMailEventListener implements Swift_Events_EventListener
{
    protected $test;

    public function __construct($test)
    {
        $this->test = $test;
    }

    public function beforeSendPerformed($event)
    {
        $this->test->addEmail($event->getMessage());
    }
}
