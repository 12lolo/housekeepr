<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info('Testing mail configuration...');
        $this->info('Sending test email to: ' . $email);

        try {
            Mail::raw('This is a test email from HouseKeepr. If you received this, your mail configuration is working correctly!', function ($message) use ($email) {
                $message->to($email)
                    ->subject('HouseKeepr Mail Test');
            });

            $this->info('✅ Test email sent successfully!');
            $this->info('Check your inbox at: ' . $email);

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email:');
            $this->error($e->getMessage());

            return 1;
        }
    }
}

