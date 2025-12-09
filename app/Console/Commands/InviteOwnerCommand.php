<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Hotel;
use App\Mail\OwnerInviteMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InviteOwnerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'owner:invite {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invite a new hotel owner by email - they fill in their own details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        // Check if email already exists
        if (User::where('email', $email)->exists()) {
            $this->error('❌ User with email ' . $email . ' already exists!');
            return 1;
        }

        $this->info('Creating new hotel owner...');

        // Generate temporary password
        $tempPassword = Str::random(16);

        // Create owner user (name is set to email temporarily)
        $owner = User::create([
            'name' => $email,
            'email' => $email,
            'password' => Hash::make($tempPassword),
            'role' => 'owner',
            'status' => 'pending',
        ]);

        $this->info('✅ Owner created: ' . $owner->email);

        // Send invitation email
        $this->info('Sending invitation email...');

        try {
            Mail::to($owner->email)->send(new OwnerInviteMail($owner, null, $tempPassword));

            $this->info('✅ Invitation email sent successfully!');
            $this->info('');
            $this->info('═══════════════════════════════════════');
            $this->info('Owner Details:');
            $this->info('═══════════════════════════════════════');
            $this->info('Email: ' . $owner->email);
            $this->info('Temporary Password: ' . $tempPassword);
            $this->info('═══════════════════════════════════════');
            $this->info('');
            $this->info('The owner will receive an email and can then:');
            $this->info('- Login with the temporary password');
            $this->info('- Fill in their name');
            $this->info('- Create their hotel(s)');
            $this->info('- Set up rooms, cleaners, etc.');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send invitation email:');
            $this->error($e->getMessage());

            // Rollback
            $owner->delete();

            return 1;
        }
    }
}

